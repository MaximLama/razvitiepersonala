BX.namespace('BX.Sale.OrderAjaxComponent');

(function() {
	'use strict';

	/**
	 * Show empty default property value to multiple properties without default values
	 */
	if (BX.Sale && BX.Sale.Input && BX.Sale.Input.Utils)
	{
		BX.Sale.Input.Utils.asMultiple = function (value)
		{
			if (value === undefined || value === null || value === '')
			{
				return [];
			}
			else if (value.constructor === Array)
			{
				var i = 0, length = value.length, val;

				for (; i < length;)
				{
					val = value[i];

					if (val === undefined || val === null || val === '')
					{
						value.splice(i, 1);
						--length;
					}
					else
					{
						++i;
					}
				}

				return value.length ? value : [''];
			}
			else
			{
				return [value];
			}
		};
	}

	BX.Sale.OrderAjaxComponent = {

		initializePrimaryFields: function()
		{
			console.log('initializePrimaryFields');
			this.BXFormPosting = false;
			this.regionBlockNotEmpty = false;
			this.locationsInitialized = false;
			this.locations = {};
			this.cleanLocations = {};
			this.locationsTemplate = '';
			this.options = {};
			this.activeSectionId = '';
			this.firstLoad = true;
			this.initialized = {};
			this.mapsReady = false;
			this.paySystemPagination = {};
			this.validation = {};
			this.hasErrorSection = {};
			this.timeOut = {};
			this.isMobile = BX.browser.IsMobile();
			this.isHttps = window.location.protocol === "https:";
			this.orderSaveAllowed = false;
			this.socServiceHiddenNode = false;
		},

		/**
		 * Initialization of sale.order.ajax component js
		 */
		init: function(parameters)
		{
			this.initializePrimaryFields();

			this.result = parameters.result || {};
			this.prepareLocations(parameters.locations);
			this.params = parameters.params || {};
			this.signedParamsString = parameters.signedParamsString || '';
			this.siteId = parameters.siteID || '';
			this.ajaxUrl = parameters.ajaxUrl || '';
			this.templateFolder = parameters.templateFolder || '';
			this.defaultBasketItemLogo = this.templateFolder + "/images/product_logo.png";
			this.defaultPaySystemLogo = this.templateFolder + "/images/pay_system_logo.png";

			this.orderBlockNode = BX(parameters.orderBlockId);
			this.totalBlockNode = BX(parameters.totalBlockId);
			this.mobileTotalBlockNode = BX(parameters.totalBlockId + '-mobile');
			this.savedFilesBlockNode = BX('bx-soa-saved-files');
			this.orderSaveBlockNode = BX('bx-soa-orderSave');
			this.mainErrorsNode = BX('bx-soa-main-notifications');

			this.authBlockNode = BX(parameters.authBlockId);
			this.authHiddenBlockNode = BX(parameters.authBlockId + '-hidden');
			this.basketBlockNode = BX(parameters.basketBlockId);
			this.basketHiddenBlockNode = BX(parameters.basketBlockId + '-hidden');
			this.regionBlockNode = BX(parameters.regionBlockId);
			this.regionHiddenBlockNode = BX(parameters.regionBlockId + '-hidden');
			this.paySystemBlockNode = BX(parameters.paySystemBlockId);
			this.paySystemHiddenBlockNode = BX(parameters.paySystemBlockId + '-hidden');
			this.propsBlockNode = BX(parameters.propsBlockId);
			this.propsHiddenBlockNode = BX(parameters.propsBlockId + '-hidden');
			if (this.result.SHOW_AUTH)
			{
				this.authGenerateUser = this.result.AUTH.new_user_registration_email_confirmation !== 'Y' && this.result.AUTH.new_user_phone_required !== 'Y';
			}
			if (this.totalBlockNode)
			{
				this.totalInfoBlockNode = this.totalBlockNode.querySelector('.bx-soa-cart-total');
				this.totalGhostBlockNode = this.totalBlockNode.querySelector('.bx-soa-cart-total-ghost');
			}

			this.options.paySystemsPerPage = parseInt(parameters.params.PAY_SYSTEMS_PER_PAGE);

			this.options.showWarnings = !!parameters.showWarnings;
			this.options.propertyValidation = !!parameters.propertyValidation;
			this.options.priceDiffWithLastTime = false;

			this.options.propertyMap = parameters.propertyMap;

			this.options.totalPriceChanged = false;
			this.noticeCloseDuration = false;
			this.noticeClose = false;
			this.initOptions();
			this.editOrder();
			this.bindEvents();


			if (this.params.USE_ENHANCED_ECOMMERCE === 'Y')
			{
				this.setAnalyticsDataLayer('checkout');
			}

			if (this.params.USER_CONSENT === 'Y')
			{
				this.initUserConsent();
			}
		},
		/**
		 * Send ajax request with order data and executes callback by action
		 */
		sendRequest: function(action, actionData)
		{
			console.log('sendRequest');
			var form;

			if (!this.startLoader())
				return;

			this.firstLoad = false;

			action = BX.type.isNotEmptyString(action) ? action : 'refreshOrderAjax';
			var eventArgs = {
				action: action,
				actionData: actionData,
				cancel: false
			};
			BX.Event.EventEmitter.emit('BX.Sale.OrderAjaxComponent:onBeforeSendRequest', eventArgs);
			if (eventArgs.cancel)
			{
				this.endLoader();
				return;
			}
			if (eventArgs.action === 'saveOrderAjax')
			{
				form = BX('bx-soa-order-form');
				if (form)
				{
					form.querySelector('input[type=hidden][name=sessid]').value = BX.bitrix_sessid();
				}
				BX.ajax.submitAjax(
					BX('bx-soa-order-form'),
					{
						url: '/bitrix/templates/razvitie/ajax/order.php',
						method: 'POST',
						dataType: 'json',
						data: {
							via_ajax: 'Y',
							action: 'saveOrderAjax',
							sessid: BX.bitrix_sessid(),
							SITE_ID: this.siteId,
							signedParamsString: this.signedParamsString
						},
						onsuccess: BX.proxy(this.saveOrderWithJson, this),
						onfailure: BX.proxy(this.handleNotRedirected, this)
					}
				);
			}
			else 
			{
				console.log('here');
				BX.ajax({
					method: 'POST',
					dataType: 'json',
					url: this.ajaxUrl,
					data: this.getData(eventArgs.action, eventArgs.actionData),
					onsuccess: BX.delegate(function(result) {

						if (result.redirect && result.redirect.length)
							document.location.href = result.redirect;
						this.saveFiles();
						switch (eventArgs.action)
						{
							case 'refreshOrderAjax':
								this.refreshOrder(result);
								break;
							case 'confirmSmsCode':
							case 'showAuthForm':
								this.firstLoad = true;
								this.refreshOrder(result);
								break;
							case 'enterCoupon':
								if (result && result.order)
								{
									this.deliveryCachedInfo = [];
									this.refreshOrder(result);
								}
								else
								{
									this.addCoupon(result);
								}

								break;
							case 'removeCoupon':
								if (result && result.order)
								{
									this.deliveryCachedInfo = [];
									this.refreshOrder(result);
								}
								else
								{
									this.removeCoupon(result);
								}

								break;
						}
						BX.cleanNode(this.savedFilesBlockNode);
						this.endLoader();
					}, this),
					onfailure: BX.delegate(function(){
						this.endLoader();
					}, this)
				});
			}
		},

		getData: function(action, actionData)
		{
			console.log('getData');
			var data = {
				order: this.getAllFormData(),
				sessid: BX.bitrix_sessid(),
				via_ajax: 'Y',
				SITE_ID: this.siteId,
				signedParamsString: this.signedParamsString
			};

			data[this.params.ACTION_VARIABLE] = action;

			if (action === 'enterCoupon' || action === 'removeCoupon')
				data.coupon = actionData;

			return data;
		},

		getAllFormData: function()
		{
			console.log('getAllFormData');
			var form = BX('bx-soa-order-form'),
				prepared = BX.ajax.prepareForm(form),
				i;
			for (i in prepared.data)
			{
				if (prepared.data.hasOwnProperty(i) && i == '')
				{
					delete prepared.data[i];
				}
			}

			return !!prepared && prepared.data ? prepared.data : {};
		},

		/**
		 * Refreshes order via json data from ajax request
		 */
		refreshOrder: function(result)
		{
			console.log('refreshOrder');
			if (result.error)
			{
				this.showError(this.mainErrorsNode, result.error);
				this.animateScrollTo(this.mainErrorsNode, 800, 20);
			}
			else
			{
				//this.isPriceChanged(result);

				this.result = result.order;
				this.prepareLocations(result.locations);
				this.locationsInitialized = false;
				this.maxWaitTimeExpired = false;
				this.pickUpMapFocused = false;
				this.deliveryLocationInfo = {};

				this.initialized = {};
				this.clearBlocks();
				
				this.initOptions();
				this.editOrder();
				/*this.mapsReady && this.initMaps();
				BX.saleOrderAjax && BX.saleOrderAjax.initDeferredControl();*/
			}

			return true;
		},

		saveOrderWithJson: function(result)
		{
			var redirected = false;
			if (result)
			{
				window.location = result;
			}
			
			if (!redirected)
			{
				this.handleNotRedirected();
			}
		},

		handleNotRedirected: function()
		{
			/*console.log('handleNotRedirected');
			this.endLoader();
			this.disallowOrderSave();*/
		},

		/**
		 * Showing loader image with overlay.
		 */
		startLoader: function()
		{
			console.log('startLoader');
			if (this.BXFormPosting === true)
				return false;

			this.BXFormPosting = true;

			if (!this.loadingScreen)
			{
				this.loadingScreen = new BX.PopupWindow('loading_screen', null, {
					overlay: {backgroundColor: 'white', opacity: 1},
					events: {
						onAfterPopupShow: BX.delegate(function(){
							BX.cleanNode(this.loadingScreen.popupContainer);
							BX.removeClass(this.loadingScreen.popupContainer, 'popup-window');
							this.loadingScreen.popupContainer.appendChild(
								BX.create('IMG', {props: {src: this.templateFolder + '/images/loader.gif'}})
							);
							this.loadingScreen.popupContainer.removeAttribute('style');
							this.loadingScreen.popupContainer.style.display = 'block';
						}, this)
					}
				});
				BX.addClass(this.loadingScreen.overlay.element, 'bx-step-opacity');
			}

			this.loadingScreen.overlay.element.style.opacity = '0';
			this.loadingScreen.show();
			this.loadingScreen.overlay.element.style.opacity = '0.6';

			return true;
		},

		/**
		 * Hiding loader image with overlay.
		 */
		endLoader: function()
		{
			console.log('endLoader');
			this.BXFormPosting = false;

			if (this.loadingScreen && this.loadingScreen.isShown())
			{
				this.loadingScreen.close();
			}
		},

		htmlspecialcharsEx: function(str)
		{
			console.log('htmlspecialcharsEx');
			return str.replace(/&amp;/g, '&amp;amp;')
				.replace(/&lt;/g, '&amp;lt;').replace(/&gt;/g, '&amp;gt;')
				.replace(/&quot;/g, '&amp;quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
		},

		saveFiles: function()
		{
			console.log('saveFiles');
			if (this.result.ORDER_PROP && this.result.ORDER_PROP.properties)
			{
				var props = this.result.ORDER_PROP.properties, i, prop;
				for (i = 0; i < props.length; i++)
				{
					if (props[i].TYPE == 'FILE')
					{
						prop = this.orderBlockNode.querySelector('div[data-property-id-row="' + props[i].ID + '"]');
						if (prop)
							this.savedFilesBlockNode.appendChild(prop);
					}
				}
			}
		},

		/**
		 * Animating scroll to certain node
		 */
		animateScrollTo: function(node, duration, shiftToTop)
		{
			console.log('animateScrollTo');
			if (!node)
				return;

			var scrollTop = BX.GetWindowScrollPos().scrollTop,
				orderBlockPos = BX.pos(this.orderBlockNode),
				ghostTop = BX.pos(node).top - (this.isMobile ? 50 : 0);

			if (shiftToTop)
				ghostTop -= parseInt(shiftToTop);

			if (ghostTop + window.innerHeight > orderBlockPos.bottom)
				ghostTop = orderBlockPos.bottom - window.innerHeight + 17;

			new BX.easing({
				duration: duration || 800,
				start: {scroll: scrollTop},
				finish: {scroll: ghostTop},
				transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
				step: BX.delegate(function(state){
					window.scrollTo(0, state.scroll);
				}, this)
			}).animate();
		},

		checkKeyPress: function(event)
		{
			console.log('checkKeyPress');
			if (event.keyCode == 13)
			{
				var target = event.target || event.srcElement,
					send = target.getAttribute('data-send'),
					nextAttr, next;

				if (!send)
				{
					nextAttr = target.getAttribute('data-next');
					if (nextAttr)
					{
						next = this.orderBlockNode.querySelector('input[name=' + nextAttr + ']');
						next && next.focus();
					}

					return BX.PreventDefault(event);
				}
			}
		},

		getSizeString: function(maxSize, len)
		{
			console.log('getSizeString');
			var gbDivider = 1024 * 1024 * 1024,
				mbDivider = 1024 * 1024,
				kbDivider = 1024,
				str;

			maxSize = parseInt(maxSize);
			len = parseInt(len);

			if (maxSize > gbDivider)
				str = parseFloat(maxSize / gbDivider).toFixed(len) + ' Gb';
			else if (maxSize > mbDivider)
				str = parseFloat(maxSize / mbDivider).toFixed(len) + ' Mb';
			else if (maxSize > kbDivider)
				str = parseFloat(maxSize / kbDivider).toFixed(len) + ' Kb';
			else
				str = maxSize + ' B';

			return str;
		},

		getFileAccepts: function(accepts)
		{
			console.log('getFileAccepts');
			var arr = [],
				arAccepts = accepts.split(','),
				i, currentAccept;

			var mimeTypesMap = {
				json: 'application/json', javascript: 'application/javascript', 'octet-stream': 'application/octet-stream',
				ogg: 'application/ogg', pdf: 'application/pdf', zip: 'application/zip', gzip: 'application/gzip',
				aac: 'audio/aac', mp3: 'audio/mpeg', gif: 'image/gif', jpeg: 'image/jpeg', png: 'image/png', svg: 'image/svg+xml',
				tiff: 'image/tiff', css: 'text/css', csv: 'text/csv', html: 'text/html', plain: 'text/plain',
				php: 'text/php', xml: 'text/xml', mpeg: 'video/mpeg', mp4: 'video/mp4', quicktime: 'video/quicktime',
				flv: 'video/x-flv', doc: 'application/msword', docx: 'application/msword',
				xls: 'application/vnd.ms-excel', xlsx: 'application/vnd.ms-excel'
			};

			for (i = 0; i < arAccepts.length; i++)
			{
				currentAccept = BX.util.trim(arAccepts[i]);
				currentAccept = mimeTypesMap[currentAccept] || currentAccept;
				arr.push(currentAccept);
			}

			return arr.join(',');
		},

		uniqueText: function(text, separator)
		{
			console.log('uniqueText');
			var phrases, i, output = [];

			text = text || '';
			separator = separator || '<br>';

			phrases = text.split(separator);
			phrases = BX.util.array_unique(phrases);

			for (i = 0; i < phrases.length; i++)
			{
				if (phrases[i] == '')
					continue;

				output.push(BX.util.trim(phrases[i]));
			}

			return output.join(separator);
		},

		getImageSources: function(item, key)
		{
			console.log('getImageSources');
			if (!item || !key || !item[key])
				return false;

			return {
				src_1x: item[key + '_SRC'],
				src_2x: item[key + '_SRC_2X'],
				src_orig: item[key + '_SRC_ORIGINAL']
			};
		},

		getErrorContainer: function(node)
		{
			console.log('getErrorContainer');
			if (!node)
				return;

			node.appendChild(
				BX.create('DIV', {props: {className: 'alert alert-danger'}, style: {display: 'none'}})
			);
		},

		showError: function(node, msg, border)
		{
			console.log('showError');
			if (BX.type.isArray(msg))
				msg = msg.join('<br>');

			var errorContainer = node.querySelector('.alert.alert-danger'), animate;
			if (errorContainer && msg.length)
			{
				BX.cleanNode(errorContainer);
				errorContainer.appendChild(BX.create('DIV', {html: msg}));

				animate = !this.hasErrorSection[node.id];
				if (animate)
				{
					errorContainer.style.opacity = 0;
					errorContainer.style.display = '';
					new BX.easing({
						duration: 300,
						start: {opacity: 0},
						finish: {opacity: 100},
						transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
						step: function(state){
							errorContainer.style.opacity = state.opacity / 100;
						},
						complete: function(){
							errorContainer.removeAttribute('style');
						}
					}).animate();
				}
				else
					errorContainer.style.display = '';

				if (!!border)
					BX.addClass(node, 'bx-step-error');
			}
		},

		showErrors: function(errors, scroll, showAll)
		{
			console.log('showErrors');
			var errorNodes = this.orderBlockNode.querySelectorAll('div.alert.alert-danger'),
				section, k, blockErrors;

			for (k = 0; k < errorNodes.length; k++)
			{
				section = BX.findParent(errorNodes[k], {className: 'bx-soa-section'});
				BX.removeClass(section, 'bx-step-error');
				errorNodes[k].style.display = 'none';
				BX.cleanNode(errorNodes[k]);
			}

			if (!errors || BX.util.object_keys(errors).length < 1)
				return;

			for (k in errors)
			{
				if (!errors.hasOwnProperty(k))
					continue;

				blockErrors = errors[k];
				switch (k.toUpperCase())
				{
					case 'MAIN':
						this.showError(this.mainErrorsNode, blockErrors);
						this.animateScrollTo(this.mainErrorsNode, 800, 20);
						scroll = false;
						break;
					case 'AUTH':
						if (this.authBlockNode.style.display == 'none')
						{
							this.showError(this.mainErrorsNode, blockErrors, true);
							this.animateScrollTo(this.mainErrorsNode, 800, 20);
							scroll = false;
						}
						else
							this.showError(this.authBlockNode, blockErrors, true);
						break;
					case 'REGION':
						if (showAll || this.regionBlockNode.getAttribute('data-visited') === 'true')
						{
							this.showError(this.regionBlockNode, blockErrors, true);
							this.showError(this.regionHiddenBlockNode, blockErrors);
						}
						break;
					case 'DELIVERY':
						if (showAll || this.deliveryBlockNode.getAttribute('data-visited') === 'true')
						{
							this.showError(this.deliveryBlockNode, blockErrors, true);
							this.showError(this.deliveryHiddenBlockNode, blockErrors);
						}
						break;
					case 'PAY_SYSTEM':
						if (showAll || this.paySystemBlockNode.getAttribute('data-visited') === 'true')
						{
							this.showError(this.paySystemBlockNode, blockErrors, true);
							this.showError(this.paySystemHiddenBlockNode, blockErrors);
						}
						break;
					case 'PROPERTY':
						if (showAll || this.propsBlockNode.getAttribute('data-visited') === 'true')
						{
							this.showError(this.propsBlockNode, blockErrors, true);
							this.showError(this.propsHiddenBlockNode, blockErrors);
						}
						break;
				}
			}

			!!scroll && this.scrollToError();
		},

		showBlockErrors: function(node)
		{
			console.log('showBlockErrors');
			var errorNode = node.querySelector('div.alert.alert-danger'),
				hiddenNode, errors;

			if (!errorNode)
				return;

			BX.removeClass(node, 'bx-step-error');
			errorNode.style.display = 'none';
			BX.cleanNode(errorNode);

			switch (node.id)
			{
				case this.regionBlockNode.id:
					hiddenNode = this.regionHiddenBlockNode;
					errors = this.result.ERROR.REGION;
					break;
				case this.deliveryBlockNode.id:
					hiddenNode = this.deliveryHiddenBlockNode;
					errors = this.result.ERROR.DELIVERY;
					break;
				case this.paySystemBlockNode.id:
					hiddenNode = this.paySystemHiddenBlockNode;
					errors = this.result.ERROR.PAY_SYSTEM;
					break;
				case this.propsBlockNode.id:
					hiddenNode = this.propsHiddenBlockNode;
					errors = this.result.ERROR.PROPERTY;
					break;
			}

			if (errors && BX.util.object_keys(errors).length)
			{
				this.showError(node, errors, true);
				this.showError(hiddenNode, errors);
			}
		},

		/**
		 * Returns status of preloaded data from back-end for certain block
		 */
		checkPreload: function(node)
		{
			console.log('checkPreload');
			var status;

			switch (node.id)
			{
				case this.regionBlockNode.id:
					status = this.result.LAST_ORDER_DATA && this.result.LAST_ORDER_DATA.PERSON_TYPE;
					break;
				case this.paySystemBlockNode.id:
					status = this.result.LAST_ORDER_DATA && this.result.LAST_ORDER_DATA.PAY_SYSTEM;
					break;
				case this.deliveryBlockNode.id:
					status = this.result.LAST_ORDER_DATA && this.result.LAST_ORDER_DATA.DELIVERY;
					break;
				case this.pickUpBlockNode.id:
					status = this.result.LAST_ORDER_DATA && this.result.LAST_ORDER_DATA.PICK_UP;
					break;
				default:
					status = true;
			}

			return status;
		},

		checkBlockErrors: function(node)
		{
			console.log('checkBlockErrors');
			var hiddenNode, errorNode, showError, showWarning, errorTooltips, i;

			if (hiddenNode = BX(node.id + '-hidden'))
			{
				errorNode = hiddenNode.querySelector('div.alert.alert-danger');
				showError = errorNode && errorNode.style.display != 'none';
				showWarning = hiddenNode.querySelector('div.alert.alert-warning.alert-show');

				if (!showError)
				{
					errorTooltips = hiddenNode.querySelectorAll('div.tooltip');
					for (i = 0; i < errorTooltips.length; i++)
					{
						if (errorTooltips[i].getAttribute('data-state') == 'opened')
						{
							showError = true;
							break;
						}
					}
				}
			}

			if (showError)
				BX.addClass(node, 'bx-step-error');
			else if (showWarning)
				BX.addClass(node, 'bx-step-warning');
			else
				BX.removeClass(node, 'bx-step-error bx-step-warning');

			return !showError;
		},

		scrollToError: function()
		{
			console.log('scrollToError');
			var sections = this.orderBlockNode.querySelectorAll('div.bx-soa-section.bx-active'),
				i, errorNode;

			for (i in sections)
			{
				if (sections.hasOwnProperty(i))
				{
					errorNode = sections[i].querySelector('.alert.alert-danger');
					if (errorNode && errorNode.style.display != 'none')
					{
						this.animateScrollTo(sections[i]);
						break;
					}
				}
			}
		},

		showWarnings: function()
		{
			console.log('showWarnings');
			var sections = this.orderBlockNode.querySelectorAll('div.bx-soa-section.bx-active'),
				k,  warningString;

			for (k = 0; k < sections.length; k++)
			{
				BX.removeClass(sections[k], 'bx-step-warning');

				if (sections[k].getAttribute('data-visited') == 'false')
					BX.removeClass(sections[k], 'bx-step-completed');
			}

			if (currentDelivery && currentDelivery.CALCULATE_ERRORS)
			{
				BX.addClass(this.deliveryBlockNode, 'bx-step-warning');

				warningString = '<strong>' + this.params.MESS_DELIVERY_CALC_ERROR_TITLE + '</strong>';
				if (this.params.MESS_DELIVERY_CALC_ERROR_TEXT.length)
					warningString += '<br><small>' + this.params.MESS_DELIVERY_CALC_ERROR_TEXT + '</small>';

				this.showBlockWarning(this.deliveryBlockNode, warningString);
				this.showBlockWarning(this.deliveryHiddenBlockNode, warningString);

				if (this.activeSectionId != this.deliveryBlockNode.id)
				{
					BX.addClass(this.deliveryBlockNode, 'bx-step-completed');
					BX.bind(this.deliveryBlockNode.querySelector('.alert.alert-warning'), 'click', BX.proxy(this.showByClick, this));
				}
			}
			else if (BX.hasClass(this.deliveryBlockNode, 'bx-step-warning') && this.activeSectionId != this.deliveryBlockNode.id)
			{
				BX.removeClass(this.deliveryBlockNode, 'bx-step-warning');
			}

			if (!this.result.WARNING || !this.options.showWarnings)
				return;

			for (k in this.result.WARNING)
			{
				if (this.result.WARNING.hasOwnProperty(k))
				{
					switch (k.toUpperCase())
					{
						case 'DELIVERY':
							if (this.deliveryBlockNode.getAttribute('data-visited') === 'true')
							{
								this.showBlockWarning(this.deliveryBlockNode, this.result.WARNING[k], true);
								this.showBlockWarning(this.deliveryHiddenBlockNode, this.result.WARNING[k], true);
							}

							break;
						case 'PAY_SYSTEM':
							if (this.paySystemBlockNode.getAttribute('data-visited') === 'true')
							{
								this.showBlockWarning(this.paySystemBlockNode, this.result.WARNING[k], true);
								this.showBlockWarning(this.paySystemHiddenBlockNode, this.result.WARNING[k], true);
							}

							break;
					}
				}
			}
		},

		notifyAboutWarnings: function(node)
		{
			console.log('notifyAboutWarnings');
			if (!BX.type.isDomNode(node))
				return;

			switch (node.id)
			{
				case this.deliveryBlockNode.id:
					this.showBlockWarning(this.deliveryBlockNode, this.result.WARNING.DELIVERY, true);
					break;
				case this.paySystemBlockNode.id:
					this.showBlockWarning(this.paySystemBlockNode, this.result.WARNING.PAY_SYSTEM, true);
					break;
			}
		},

		showBlockWarning: function(node, warnings, hide)
		{
			console.log('showBlockWarning');
			var errorNode = node.querySelector('.alert.alert-danger'),
				warnStr = '',
				i, warningNode, existedWarningNodes;

			if (errorNode)
			{
				if (BX.type.isString(warnings))
				{
					warnStr = warnings;
				}
				else
				{
					for (i in warnings)
					{
						if (warnings.hasOwnProperty(i) && warnings[i])
						{
							warnStr += warnings[i] + '<br>';
						}
					}
				}

				if (!warnStr)
				{
					return;
				}

				existedWarningNodes = node.querySelectorAll('.alert.alert-warning');
				for (i in existedWarningNodes)
				{
					if (existedWarningNodes.hasOwnProperty(i) && BX.type.isDomNode(existedWarningNodes[i]))
					{
						if (existedWarningNodes[i].innerHTML.indexOf(warnStr) !== -1)
						{
							return;
						}
					}
				}

				warningNode = BX.create('DIV', {
					props: {className: 'alert alert-warning' + (!!hide ? ' alert-hide' : ' alert-show')},
					html: warnStr
				});
				BX.prepend(warningNode, errorNode.parentNode);
				BX.addClass(node, 'bx-step-warning');
			}
		},

		showPagination: function(entity, node)
		{
			console.log('showPagination');
			if (!node || !entity)
				return;

			var pagination, navigation = [], i,
				pageCounter, active,
				colorTheme, paginationNode;

			switch (entity)
			{
				case 'delivery':
					pagination = this.deliveryPagination; break;
				case 'paySystem':
					pagination = this.paySystemPagination; break;
				case 'pickUp':
					pagination = this.pickUpPagination; break;
			}

			if (pagination.pages.length > 1)
			{
				navigation.push(
					BX.create('LI', {
						attrs: {
							'data-action': 'prev',
							'data-entity': entity
						},
						props: {className: 'bx-pag-prev'},
						html: pagination.pageNumber == 1
							? '<span>' + this.params.MESS_NAV_BACK + '</span>'
							: '<a href=""><span>' + this.params.MESS_NAV_BACK + '</span></a>',
						events: {click: BX.proxy(this.doPagination, this)}
					})
				);
				for (i = 0; i < pagination.pages.length; i++)
				{
					pageCounter = parseInt(i) + 1;
					active = pageCounter == pagination.pageNumber ? 'bx-active' : '';

					navigation.push(
						BX.create('LI', {
							attrs: {
								'data-action': pageCounter,
								'data-entity': entity
							},
							props: {className: active},
							html: '<a href=""><span>' + pageCounter  + '</span></a>',
							events: {click: BX.proxy(this.doPagination, this)}
						})
					);
				}

				navigation.push(
					BX.create('LI', {
						attrs: {
							'data-action': 'next',
							'data-entity': entity
						},
						props: {className: 'bx-pag-next'},
						html: pagination.pageNumber == pagination.pages.length
							? '<span>' + this.params.MESS_NAV_FORWARD + '</span>'
							: '<a href=""><span>' + this.params.MESS_NAV_FORWARD + '</span></a>',
						events: {click: BX.proxy(this.doPagination, this)}
					})
				);
				colorTheme = this.params.TEMPLATE_THEME || '';
				paginationNode = BX.create('DIV', {
					props: {className: 'bx-pagination' + (colorTheme ? ' bx-' + colorTheme : '')},
					children: [
						BX.create('DIV', {
							props: {className: 'bx-pagination-container'},
							children: [BX.create('UL', {children: navigation})]
						})
					]
				});

				node.appendChild(BX.create('DIV', {style: {clear: 'both'}}));
				node.appendChild(paginationNode);
			}
		},

		doPagination: function(e)
		{
			console.log('doPagination');
			var target = e.target || e.srcElement,
				node = target.tagName == 'LI' ? target : BX.findParent(target, {tagName: 'LI'}),
				page = node.getAttribute('data-action'),
				entity = node.getAttribute('data-entity'),
				pageNum;

			if (BX.hasClass(node, 'bx-active'))
				return BX.PreventDefault(e);

			if (page == 'prev' || page == 'next')
			{
				pageNum = parseInt(BX.findParent(node).querySelector('.bx-active').getAttribute('data-action'));
				page = page == 'next' ? ++pageNum : --pageNum;
			}

			if (entity == 'delivery')
				this.showDeliveryItemsPage(page);
			else if (entity == 'paySystem')
				this.showPaySystemItemsPage(page);
			else if (entity == 'pickUp')
				this.showPickUpItemsPage(page);

			return BX.PreventDefault(e);
		},

		showDeliveryItemsPage: function(page)
		{
			console.log('showDeliveryItemsPage');
			this.getCurrentPageItems('delivery', page);

			var selectedDelivery = this.getSelectedDelivery(), hidden,
				deliveryItemsContainer, k, deliveryItemNode;

			if (selectedDelivery && selectedDelivery.ID)
			{
				hidden = this.deliveryBlockNode.querySelector('input[type=hidden][name=DELIVERY_ID]');
				if (!hidden)
				{
					hidden = BX.create('INPUT', {
						props: {
							type: 'hidden',
							name: 'DELIVERY_ID',
							value: selectedDelivery.ID
						}
					})
				}
			}

			deliveryItemsContainer = this.deliveryBlockNode.querySelector('.bx-soa-pp-item-container');
			BX.cleanNode(deliveryItemsContainer);

			if (BX.type.isDomNode(hidden))
				BX.prepend(hidden, BX.findParent(deliveryItemsContainer));

			for (k = 0; k < this.deliveryPagination.currentPage.length; k++)
			{
				deliveryItemNode = this.createDeliveryItem(this.deliveryPagination.currentPage[k]);
				deliveryItemsContainer.appendChild(deliveryItemNode);
			}

			this.showPagination('delivery', deliveryItemsContainer);
		},

		showPaySystemItemsPage: function(page)
		{
			console.log('showPaySystemItemsPage');
			this.getCurrentPageItems('paySystem', page);

			var selectedPaySystem = this.getSelectedPaySystem(), hidden,
				paySystemItemsContainer, k, paySystemItemNode;

			if (selectedPaySystem && selectedPaySystem.ID)
			{
				hidden = this.paySystemBlockNode.querySelector('input[type=hidden][name=PAY_SYSTEM_ID]');
				if (!hidden)
				{
					hidden = BX.create('INPUT', {
						props: {
							type: 'hidden',
							name: 'PAY_SYSTEM_ID',
							value: selectedPaySystem.ID
						}
					})
				}
			}

			paySystemItemsContainer = this.paySystemBlockNode.querySelector('.bx-soa-pp-item-container');
			BX.cleanNode(paySystemItemsContainer);

			if (BX.type.isDomNode(hidden))
				BX.prepend(hidden, BX.findParent(paySystemItemsContainer));

			for (k = 0; k < this.paySystemPagination.currentPage.length; k++)
			{
				paySystemItemNode = this.createPaySystemItem(this.paySystemPagination.currentPage[k]);
				paySystemItemsContainer.appendChild(paySystemItemNode);
			}

			this.showPagination('paySystem', paySystemItemsContainer);
		},

		showPickUpItemsPage: function(page)
		{
			console.log('showPickUpItemsPage');
			this.getCurrentPageItems('pickUp', page);
			this.editPickUpList(false);
		},

		getCurrentPageItems: function(entity, page)
		{
			console.log('getCurrentPageItems');
			if (!entity || typeof page === 'undefined')
				return;

			var pagination, perPage;

			switch (entity)
			{
				case 'delivery':
					pagination = this.deliveryPagination;
					perPage = this.options.deliveriesPerPage;
					break;
				case 'paySystem':
					pagination = this.paySystemPagination;
					perPage = this.options.paySystemsPerPage;
					break;
				case 'pickUp':
					pagination = this.pickUpPagination;
					perPage = this.options.pickUpsPerPage;
					break;
			}

			if (pagination && perPage > 0)
			{
				if (page <= 0 || page > pagination.pages.length)
					return;

				pagination.pageNumber = page;
				pagination.currentPage = pagination.pages.slice(pagination.pageNumber - 1, pagination.pageNumber)[0];
			}
		},

		initPropsListForLocation: function()
		{
			console.log('initPropsListForLocation');
			if (BX.saleOrderAjax && this.result.ORDER_PROP && this.result.ORDER_PROP.properties)
			{
				var i, k, curProp, attrObj;

				BX.saleOrderAjax.cleanUp();

				for (i = 0; i < this.result.ORDER_PROP.properties.length; i++)
				{
					curProp = this.result.ORDER_PROP.properties[i];

					if (curProp.TYPE == 'LOCATION' && curProp.MULTIPLE == 'Y' && curProp.IS_LOCATION != 'Y')
					{
						for (k = 0; k < this.locations[curProp.ID].length; k++)
						{
							BX.saleOrderAjax.addPropertyDesc({
								id: curProp.ID + '_' + k,
								attributes: {
									id: curProp.ID + '_' + k,
									type: curProp.TYPE,
									valueSource: curProp.SOURCE == 'DEFAULT' ? 'default' : 'form'
								}
							});
						}
					}
					else
					{
						attrObj = {
							id: curProp.ID,
							type: curProp.TYPE,
							valueSource: curProp.SOURCE == 'DEFAULT' ? 'default' : 'form'
						};
						BX.saleOrderAjax.addPropertyDesc({
							id: curProp.ID,
							attributes: attrObj
						});
					}
				}
			}
		},

		/**
		 * Binds main events for scrolling/resizing
		 */
		bindEvents: function()
		{
			console.log('bindEvents');
			BX.bind(this.regionBlockNode.querySelector(".calculate__input--select"), "change", BX.proxy(this.sendRequest, this));
			BX.bind(this.orderSaveBlockNode.querySelector('[data-save-button]'), 'click', BX.proxy(this.clickOrderSaveAction, this));
			BX.bind(window, 'scroll', BX.proxy(this.totalBlockScrollCheck, this));
			BX.bind(window, 'resize', BX.throttle(function(){
				this.totalBlockResizeCheck();
				this.alignBasketColumns();
				this.basketBlockScrollCheck();
				this.mapsReady && this.resizeMapContainers();
			}, 50, this));
			BX.addCustomEvent('onDeliveryExtraServiceValueChange', BX.proxy(this.sendRequest, this));
		    BX.bind(BX('close'), "click", BX.proxy(function(event){
		    	BX.removeClass(BX.findParent(event.target, {'class':'notice'}), "open");
		    	clearTimeout(this.noticeClose);
		    	this.noticeCloseDuration = false;
		    }, this))
		},

		initOptions: function()
		{
			console.log('initOptions');
			var headers, i, total;

			this.initPropsListForLocation();

			this.propertyCollection = new BX.Sale.PropertyCollection(BX.merge({publicMode: true}, this.result.ORDER_PROP));
			this.fadedPropertyCollection = new BX.Sale.PropertyCollection(BX.merge({publicMode: true}, this.result.ORDER_PROP));

			if (this.options.propertyValidation)
				this.initValidation();

			this.initPagination();

			this.options.showPreviewPicInBasket = false;
			this.options.showDetailPicInBasket = false;
			this.options.showPropsInBasket = false;
			this.options.showPriceNotesInBasket = false;

			if (this.result.GRID && this.result.GRID.HEADERS)
			{
				headers = this.result.GRID.HEADERS;
				for (i = 0; i < headers.length; i++)
				{
					if (headers[i].id === 'PREVIEW_PICTURE')
						this.options.showPreviewPicInBasket = true;

					if (headers[i].id === 'DETAIL_PICTURE')
						this.options.showDetailPicInBasket = true;

					if (headers[i].id === 'PROPS')
						this.options.showPropsInBasket = true;

					if (headers[i].id === 'NOTES')
						this.options.showPriceNotesInBasket = true;
				}
			}

			if (this.result.TOTAL)
			{
				total = this.result.TOTAL;
				this.options.showOrderWeight = total.ORDER_WEIGHT && parseFloat(total.ORDER_WEIGHT) > 0;
				this.options.showPriceWithoutDiscount = parseFloat(total.ORDER_PRICE) < parseFloat(total.PRICE_WITHOUT_DISCOUNT_VALUE);
				this.options.showDiscountPrice = total.DISCOUNT_PRICE && parseFloat(total.DISCOUNT_PRICE) > 0;
				this.options.showTaxList = total.TAX_LIST && total.TAX_LIST.length;
				this.options.showPayedFromInnerBudget = total.PAYED_FROM_ACCOUNT_FORMATED && total.PAYED_FROM_ACCOUNT_FORMATED.length;
			}
		},

		reachGoal: function(goal, section)
		{
			console.log('reachGoal');
			var counter = this.params.YM_GOALS_COUNTER || '',
				useGoals = this.params.USE_YM_GOALS == 'Y' && typeof window['yaCounter' + counter] !== 'undefined',
				goalId;
			if (useGoals)
			{
				goalId = this.getGoalId(goal, section);
				window['yaCounter' + counter].reachGoal(goalId);
			}
		},

		getGoalId: function(goal, section)
		{
			console.log('getGoalId');
			if (!goal)
				return '';

			if (goal == 'initialization')
				return this.params.YM_GOALS_INITIALIZE;

			if (goal == 'order')
				return this.params.YM_GOALS_SAVE_ORDER;

			var goalId = '',
				isEdit = goal == 'edit';

			if (!section || !section.id)
				return '';

			switch (section.id)
			{
				case this.basketBlockNode.id:
					goalId = isEdit ? this.params.YM_GOALS_EDIT_BASKET : this.params.YM_GOALS_NEXT_BASKET; break;
				case this.regionBlockNode.id:
					goalId = isEdit ? this.params.YM_GOALS_EDIT_REGION : this.params.YM_GOALS_NEXT_REGION; break;
				case this.paySystemBlockNode.id:
					goalId = isEdit ? this.params.YM_GOALS_EDIT_PAY_SYSTEM : this.params.YM_GOALS_NEXT_PAY_SYSTEM; break;
				case this.deliveryBlockNode.id:
					goalId = isEdit ? this.params.YM_GOALS_EDIT_DELIVERY : this.params.YM_GOALS_NEXT_DELIVERY; break;
				case this.pickUpBlockNode.id:
					goalId = isEdit ? this.params.YM_GOALS_EDIT_PICKUP : this.params.YM_GOALS_NEXT_PICKUP; break;
				case this.propsBlockNode.id:
					goalId = isEdit ? this.params.YM_GOALS_EDIT_PROPERTIES : this.params.YM_GOALS_NEXT_PROPERTIES; break;
			}

			return goalId;
		},

		isPriceChanged: function(result)
		{
			console.log('isPriceChanged');
			var priceBefore = this.result.TOTAL.ORDER_TOTAL_LEFT_TO_PAY === null || this.result.TOTAL.ORDER_TOTAL_LEFT_TO_PAY === ''
					? this.result.TOTAL.ORDER_TOTAL_PRICE
					: this.result.TOTAL.ORDER_TOTAL_LEFT_TO_PAY,
				priceAfter = result.order.TOTAL.ORDER_TOTAL_LEFT_TO_PAY === null ? result.order.TOTAL.ORDER_TOTAL_PRICE : result.order.TOTAL.ORDER_TOTAL_LEFT_TO_PAY;

			this.options.totalPriceChanged = parseFloat(priceBefore) != parseFloat(priceAfter);
		},

		initValidation: function()
		{
			console.log('initValidation');
			if (!this.result.ORDER_PROP || !this.result.ORDER_PROP.properties)
				return;

			var properties = this.result.ORDER_PROP.properties,
				obj = {}, i;

			for (i in properties)
			{
				if (properties.hasOwnProperty(i))
					obj[properties[i].ID] = properties[i];
			}

			this.validation.properties = obj;
		},

		initPagination: function()
		{
			console.log('initPagination');
			var arReserve, pages, arPages, i;

			if (this.result.PAY_SYSTEM)
			{
				if (this.options.paySystemsPerPage > 0 && this.result.PAY_SYSTEM.length > this.options.paySystemsPerPage)
				{
					arReserve = this.result.PAY_SYSTEM.slice();
					pages = Math.ceil(arReserve.length / this.options.paySystemsPerPage);
					arPages = [];

					for (i = 0; i < pages; i++)
					{
						arPages.push(arReserve.splice(0, this.options.paySystemsPerPage));
					}
					this.paySystemPagination.pages = arPages;

					for (i = 0; i < this.result.PAY_SYSTEM.length; i++)
					{
						if (this.result.PAY_SYSTEM[i].CHECKED == 'Y')
						{
							this.paySystemPagination.pageNumber = Math.ceil(++i / this.options.paySystemsPerPage);
							break;
						}
					}

					this.paySystemPagination.pageNumber = this.paySystemPagination.pageNumber || 1;
					this.paySystemPagination.currentPage = arPages.slice(this.paySystemPagination.pageNumber - 1, this.paySystemPagination.pageNumber)[0];
					this.paySystemPagination.show = true
				}
				else
				{
					this.paySystemPagination.pageNumber = 1;
					this.paySystemPagination.currentPage = this.result.PAY_SYSTEM;
					this.paySystemPagination.show = false;
				}
			}
		},

		initPickUpPagination: function()
		{
			console.log('initPickUpPagination');
			var usePickUpPagination = false,
				usePickUp = false,
				stores, i = 0,
				arReserve, pages, arPages;

			if (this.options.pickUpsPerPage >= 0 && this.result.DELIVERY)
			{
				for (i = 0; i < this.result.DELIVERY.length; i++)
				{
					if (this.result.DELIVERY[i].CHECKED === 'Y' && this.result.DELIVERY[i].STORE_MAIN)
					{
						usePickUp = this.result.DELIVERY[i].STORE_MAIN.length > 0;
						usePickUpPagination = this.result.DELIVERY[i].STORE_MAIN.length > this.options.pickUpsPerPage;
						if (usePickUp)
							stores = this.getPickUpInfoArray(this.result.DELIVERY[i].STORE_MAIN);
						break;
					}
				}
			}

			if (usePickUp)
			{
				if (this.options.pickUpsPerPage > 0 && usePickUpPagination)
				{
					arReserve = stores.slice();
					pages = Math.ceil(arReserve.length / this.options.pickUpsPerPage);
					arPages = [];

					for (i = 0; i < pages; i++)
						arPages.push(arReserve.splice(0, this.options.pickUpsPerPage));

					this.pickUpPagination.pages = arPages;

					for (i = 0; i < stores.length; i++)
					{
						if (!this.result.BUYER_STORE || stores[i].ID == this.result.BUYER_STORE)
						{
							this.pickUpPagination.pageNumber = Math.ceil(++i / this.options.pickUpsPerPage);
							break;
						}
					}

					if (!this.pickUpPagination.pageNumber)
						this.pickUpPagination.pageNumber = 1;

					this.pickUpPagination.currentPage = arPages.slice(this.pickUpPagination.pageNumber - 1, this.pickUpPagination.pageNumber)[0];
					this.pickUpPagination.show = true
				}
				else
				{
					this.pickUpPagination.pageNumber = 1;
					this.pickUpPagination.currentPage = stores;
					this.pickUpPagination.show = false;
				}
			}
		},

		prepareLocations: function(locations)
		{
			console.log('prepareLocations');
			this.locations = {};
			this.cleanLocations = {};

			var temporaryLocations,
				i, k, output;
			if (BX.util.object_keys(locations).length)
			{
				for (i in locations)
				{
					if (!locations.hasOwnProperty(i))
						continue;

					this.locationsTemplate = locations[i].template || '';
					temporaryLocations = [];
					output = locations[i].output;

					if (output.clean)
					{
						this.cleanLocations[i] = BX.processHTML(output.clean, false);
						delete output.clean;
					}

					for (k in output)
					{
						if (output.hasOwnProperty(k))
						{
							temporaryLocations.push({
								output: BX.processHTML(output[k], false),
								showAlt: locations[i].showAlt,
								lastValue: locations[i].lastValue,
								coordinates: locations[i].coordinates || false
							});
						}
					}

					this.locations[i] = temporaryLocations;
				}
			}
		},

		locationsCompletion: function()
		{
			console.log('locationsCompletion');
			var i, locationNode, clearButton, inputStep, inputSearch,
				arProperty, data, section;

			this.locationsInitialized = true;
			this.fixLocationsStyle(this.regionBlockNode, this.regionHiddenBlockNode);
			this.fixLocationsStyle(this.propsBlockNode, this.propsHiddenBlockNode);
			for (i in this.locations)
			{
				if (!this.locations.hasOwnProperty(i))
					continue;

				locationNode = this.orderBlockNode.querySelector('div[data-property-id-row="' + i + '"]');
				if (!locationNode)
					continue;

				clearButton = locationNode.querySelector('div.bx-ui-sls-clear');
				inputStep = locationNode.querySelector('div.bx-ui-slst-pool');
				inputSearch = locationNode.querySelector('input.bx-ui-sls-fake[type=text]');

				locationNode.removeAttribute('style');
				this.bindValidation(i, locationNode);
				if (clearButton)
				{
					BX.bind(clearButton, 'click', function(e){
						var target = e.target || e.srcElement,
							parent = BX.findParent(target, {tagName: 'DIV', className: 'form-group'}),
							locationInput;

						if (parent)
							locationInput = parent.querySelector('input.bx-ui-sls-fake[type=text]');

						if (locationInput)
							BX.fireEvent(locationInput, 'keyup');
					});
				}

				if (!this.firstLoad && this.options.propertyValidation)
				{
					if (inputStep)
					{
						arProperty = this.validation.properties[i];
						data = this.getValidationData(arProperty, locationNode);
						section = BX.findParent(locationNode, {className: 'bx-soa-section'});

						if (section && section.getAttribute('data-visited') == 'true')
							this.isValidProperty(data);
					}

					if (inputSearch)
						BX.fireEvent(inputSearch, 'keyup');
				}
			}

			if (this.firstLoad && this.result.IS_AUTHORIZED && typeof this.result.LAST_ORDER_DATA.FAIL === 'undefined')
			{
				this.showActualBlock();
			}
			else if (!this.result.SHOW_AUTH)
			{
				this.changeVisibleContent();
			}
			if (this.activeSectionId !== this.regionBlockNode.id)
				this.editFadeRegionContent(this.regionBlockNode.querySelector('calculate__input.calculate__input--select'));

			if (this.activeSectionId != this.propsBlockNode.id)
				this.editFadePropsContent(this.propsBlockNode.querySelector('.bx-soa-section-content'));
		},

		fixLocationsStyle: function(section, hiddenSection)
		{
			console.log('fixLocationsStyle');
			if (!section || !hiddenSection)
				return;

			var regionActive = this.activeSectionId == section.id ? section : hiddenSection,
				locationSearchInputs, locationStepInputs, i;

			locationSearchInputs = regionActive.querySelectorAll('div.bx-sls div.dropdown-block.bx-ui-sls-input-block');
			locationStepInputs = regionActive.querySelectorAll('div.bx-slst div.dropdown-block.bx-ui-slst-input-block');

			if (locationSearchInputs.length)
				for (i = 0; i < locationSearchInputs.length; i++)
					BX.addClass(locationSearchInputs[i], 'form-control');

			if (locationStepInputs.length)
				for (i = 0; i < locationStepInputs.length; i++)
					BX.addClass(locationStepInputs[i], 'form-control');
		},

		/**
		 * Order saving action with validation. Doesn't send request while have errors
		 */
		clickOrderSaveAction: function(event)
		{
			console.log('clickOrderSaveAction');
			if (this.isValidForm())
			{
				this.allowOrderSave();

				if (this.params.USER_CONSENT === 'Y' && BX.UserConsent)
				{
					BX.onCustomEvent('bx-soa-order-save', []);
				}
				else
				{
					this.doSaveAction();
				}
			}

			return BX.PreventDefault(event);
		},

		doSaveAction: function()
		{	
			console.log('doSaveAction');
			if (this.isOrderSaveAllowed())
			{
				this.reachGoal('order');
				this.sendRequest('saveOrderAjax');
			}
		},

		/**
		 * Hiding current block node and showing next available block node
		 */
		clickNextAction: function(event)
		{
			console.log('clickNextAction');
			var target = event.target || event.srcElement,
				actionSection = BX.findParent(target, {className : "bx-active"}),
				section = this.getNextSection(actionSection),
				allSections, titleNode, editStep;

			this.reachGoal('next', actionSection);

			if (
				(!this.result.IS_AUTHORIZED || typeof this.result.LAST_ORDER_DATA.FAIL !== 'undefined')
				&& section.next.getAttribute('data-visited') == 'false'
			)
			{
				titleNode = section.next.querySelector('.bx-soa-section-title-container');
				BX.bind(titleNode, 'click', BX.proxy(this.showByClick, this));
				editStep = section.next.querySelector('.bx-soa-editstep');
				if (editStep)
					editStep.style.display = '';

				allSections = this.orderBlockNode.querySelectorAll('.bx-soa-section.bx-active');
				if (section.next.id == allSections[allSections.length - 1].id)
					this.switchOrderSaveButtons(true);
			}

			this.fade(actionSection, section.next);
			this.show(section.next);

			return BX.PreventDefault(event);
		},

		/**
		 * Hiding current block node and showing previous available block node
		 */
		clickPrevAction: function(event)
		{
			console.log('clickPrevAction');
			var target = event.target || event.srcElement,
				actionSection = BX.findParent(target, {className: "bx-active"}),
				section = this.getPrevSection(actionSection);

			this.fade(actionSection);
			this.show(section.next);
			this.animateScrollTo(section.next, 800);
			return BX.PreventDefault(event);
		},

		/**
		 * Showing authentication block node
		 */
		showAuthBlock: function()
		{
			console.log('showAuthBlock');
			var showNode = this.authBlockNode,
				fadeNode = BX(this.activeSectionId);

			if (!showNode || BX.hasClass(showNode, 'bx-selected'))
				return;

			fadeNode && this.fade(fadeNode);
			this.show(showNode);
		},

		/**
		 * Hiding authentication block node
		 */
		closeAuthBlock: function()
		{
			console.log('closeAuthBlock');
			var actionSection = this.authBlockNode,
				nextSection = this.getNextSection(actionSection).next;

			this.fade(actionSection);
			BX.cleanNode(BX(nextSection.id + '-hidden'));
			this.show(nextSection);
		},

		/**
		 * Checks possibility to skip section
		 */
		shouldSkipSection: function(section)
		{
			console.log('shouldSkipSection');
			var skip = false;

			if (this.params.SKIP_USELESS_BLOCK === 'Y')
			{
				if (section.id === this.pickUpBlockNode.id)
				{
					var delivery = this.getSelectedDelivery();
					if (delivery)
					{
						skip = this.getPickUpInfoArray(delivery.STORE).length === 1;
					}
				}

				if (section.id === this.deliveryBlockNode.id)
				{
					skip = this.result.DELIVERY && this.result.DELIVERY.length === 1
						&& this.result.DELIVERY[0].EXTRA_SERVICES.length === 0
						&& !this.result.DELIVERY[0].CALCULATE_ERRORS;
				}

				if (section.id === this.paySystemBlockNode.id)
				{
					skip = this.result.PAY_SYSTEM && this.result.PAY_SYSTEM.length === 1 && this.result.PAY_FROM_ACCOUNT !== 'Y';
				}
			}

			return skip;
		},

		/**
		 * Returns next available block node (node skipped while have one pay system, delivery or pick up)
		 */
		getNextSection: function(actionSection, skippedSection)
		{
			console.log('getNextSection');
			if (!this.orderBlockNode || !actionSection)
				return {};

			var allSections = this.orderBlockNode.querySelectorAll('.bx-soa-section.bx-active'),
				nextSection, i;

			for (i = 0; i < allSections.length; i++)
			{
				if (allSections[i].id === actionSection.id && allSections[i + 1])
				{
					nextSection = allSections[i + 1];

					if (this.shouldSkipSection(nextSection))
					{
						this.markSectionAsCompleted(nextSection);

						return this.getNextSection(nextSection, nextSection);
					}

					return {
						prev: actionSection,
						next: nextSection,
						skip: skippedSection
					};
				}
			}

			return {next: actionSection};
		},

		markSectionAsCompleted: function(section)
		{
			console.log('markSectionAsCompleted');
			var titleNode;

			if (
				(!this.result.IS_AUTHORIZED || typeof this.result.LAST_ORDER_DATA.FAIL !== 'undefined')
				&& section.getAttribute('data-visited') === 'false'
			)
			{
				this.changeVisibleSection(section, true);
				titleNode = section.querySelector('.bx-soa-section-title-container');
				BX.bind(titleNode, 'click', BX.proxy(this.showByClick, this));
			}

			section.setAttribute('data-visited', 'true');
			BX.addClass(section, 'bx-step-completed');
			BX.remove(section.querySelector('.alert.alert-warning.alert-hide'));
			this.checkBlockErrors(section);
		},

		/**
		 * Returns previous available block node (node skipped while have one pay system, delivery or pick up)
		 */
		getPrevSection: function(actionSection)
		{
			console.log('getPrevSection');
			if (!this.orderBlockNode || !actionSection)
				return {};

			var allSections = this.orderBlockNode.querySelectorAll('.bx-soa-section.bx-active'),
				prevSection, i;

			for (i = 0; i < allSections.length; i++)
			{
				if (allSections[i].id === actionSection.id && allSections[i - 1])
				{
					prevSection = allSections[i - 1];

					if (this.shouldSkipSection(prevSection))
					{
						this.markSectionAsCompleted(prevSection);

						return this.getPrevSection(prevSection);
					}

					return {
						prev: actionSection,
						next: prevSection
					};
				}
			}

			return {next: actionSection};
		},

		addAnimationEffect: function(node, className, timeout)
		{
			console.log('addAnimationEffect');
			if (!node || !className)
				return;

			if (this.timeOut[node.id])
			{
				clearTimeout(this.timeOut[node.id].timer);
				BX.removeClass(node, this.timeOut[node.id].className);
			}

			setTimeout(function(){BX.addClass(node, className)}, 10);
			this.timeOut[node.id] = {
				className: className,
				timer: setTimeout(
					BX.delegate(function(){
						BX.removeClass(node, className);
						delete this.timeOut[node.id];
					}, this),
					timeout || 5000)
			};
		},

		/**
		 * Replacing current active block node with generated fade block node
		 */
		fade: function(node, nextSection)
		{
			console.log('fade');
			if (!node || !node.id || this.activeSectionId != node.id)
				return;

			this.hasErrorSection[node.id] = false;

			var objHeightOrig = node.offsetHeight,
				objHeight;

			switch (node.id)
			{
				case this.authBlockNode.id:
					this.authBlockNode.style.display = 'none';
					BX.removeClass(this.authBlockNode, 'bx-active');
					break;
				case this.basketBlockNode.id:
					this.editFadeBasketBlock();
					break;
				case this.regionBlockNode.id:
					this.editFadeRegionBlock();
					break;
				case this.paySystemBlockNode.id:
					BX.remove(this.paySystemBlockNode.querySelector('.alert.alert-warning.alert-hide'));
					this.editFadePaySystemBlock();
					break;
				case this.deliveryBlockNode.id:
					BX.remove(this.deliveryBlockNode.querySelector('.alert.alert-warning.alert-hide'));
					this.editFadeDeliveryBlock();
					break;
				case this.pickUpBlockNode.id:
					this.editFadePickUpBlock();
					break;
				case this.propsBlockNode.id:
					this.editFadePropsBlock();
					break;
			}

			BX.addClass(node, 'bx-step-completed');
			BX.removeClass(node, 'bx-selected');

			objHeight = node.offsetHeight;
			node.style.height = objHeightOrig + 'px';

			// calculations of scrolling animation
			if (nextSection)
			{
				var windowScrollTop = BX.GetWindowScrollPos().scrollTop,
					orderPos = BX.pos(this.orderBlockNode),
					nodePos = BX.pos(node),
					diff, scrollTo, nextSectionHeightBefore, nextSectionHeightAfter, nextSectionHidden, offset;

				nextSectionHidden = BX(nextSection.id + '-hidden');
				nextSectionHidden.style.left = '-10000';
				nextSectionHidden.style.position = 'absolute';
				this.orderBlockNode.appendChild(nextSectionHidden);
				nextSectionHeightBefore = nextSection.offsetHeight;
				nextSectionHeightAfter = nextSectionHidden.offsetHeight + 57;
				BX(node.id + '-hidden').parentNode.appendChild(nextSectionHidden);
				nextSectionHidden.removeAttribute('style');

				diff = objHeight + nextSectionHeightAfter - objHeightOrig - nextSectionHeightBefore;

				offset = window.innerHeight - orderPos.height - diff;
				if (offset > 0)
					scrollTo = orderPos.top - offset/2;
				else
				{
					if (nodePos.top > windowScrollTop)
						scrollTo = nodePos.top;
					else
						scrollTo = nodePos.bottom + 6 - objHeightOrig + objHeight;

					if (scrollTo + window.innerHeight > orderPos.bottom + 25 + diff)
						scrollTo = orderPos.bottom + 25 + diff - window.innerHeight;
				}

				scrollTo -= this.isMobile ? 50 : 0;
			}

			new BX.easing({
				duration: nextSection ? 800 : 600,
				start: {height: objHeightOrig, scrollTop: windowScrollTop},
				finish: {height: objHeight, scrollTop: scrollTo},
				transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
				step: function(state){
					node.style.height = state.height + "px";
					if (nextSection)
						window.scrollTo(0, state.scrollTop);
				},
				complete: function(){
					node.style.height = '';
				}
			}).animate();

			this.checkBlockErrors(node);
		},

		/**
		 * Showing active data in certain block node
		 */
		show: function(node)
		{
			console.log('show');
			if (!node || !node.id || this.activeSectionId == node.id)
				return;

			this.activeSectionId = node.id;
			BX.removeClass(node, 'bx-step-error bx-step-warning');

			switch (node.id)
			{
				case this.authBlockNode.id:
					this.authBlockNode.style.display = '';
					BX.addClass(this.authBlockNode, 'bx-active');
					break;
				case this.basketBlockNode.id:
					this.editActiveBasketBlock(true);
					this.alignBasketColumns();
					break;
				case this.regionBlockNode.id:
					this.editActiveRegionBlock(true);
					break;
				case this.deliveryBlockNode.id:
					this.editActiveDeliveryBlock(true);
					break;
				case this.paySystemBlockNode.id:
					this.editActivePaySystemBlock(true);
					break;
				case this.pickUpBlockNode.id:
					this.editActivePickUpBlock(true);
					break;
				case this.propsBlockNode.id:
					this.editActivePropsBlock(true);
					break;
			}

			if (node.getAttribute('data-visited') === 'false')
			{
				this.showBlockErrors(node);
				this.notifyAboutWarnings(node);
			}

			node.setAttribute('data-visited', 'true');
			BX.addClass(node, 'bx-selected');
			BX.removeClass(node, 'bx-step-completed');
		},

		showByClick: function(event)
		{
			console.log('showByClick');
			var target = event.target || event.srcElement,
				showNode = BX.findParent(target, {className: "bx-active"}),
				fadeNode = BX(this.activeSectionId),
				scrollTop = BX.GetWindowScrollPos().scrollTop;

			if (!showNode || BX.hasClass(showNode, 'bx-selected'))
				return BX.PreventDefault(event);

			this.reachGoal('edit', showNode);

			fadeNode && this.fade(fadeNode);
			this.show(showNode);

			setTimeout(BX.delegate(function(){
				if (BX.pos(showNode).top < scrollTop)
					this.animateScrollTo(showNode, 300);
			}, this), 320);

			return BX.PreventDefault(event);
		},

		/**
		 * Checks each active block from top to bottom for errors (showing first block with errors or last block)
		 */
		showActualBlock: function()
		{
			console.log('showActualBlock');
			var allSections = this.orderBlockNode.querySelectorAll('.bx-soa-section.bx-active'),
				i = 0;

			while (allSections[i])
			{
				if (allSections[i].id === this.regionBlockNode.id)
					this.isValidRegionBlock();

				if (allSections[i].id === this.propsBlockNode.id)
					this.isValidPropertiesBlock();

				if (!this.checkBlockErrors(allSections[i]) || !this.checkPreload(allSections[i]))
				{
					if (this.activeSectionId !== allSections[i].id)
					{
						BX(this.activeSectionId) && this.fade(BX(this.activeSectionId));
						this.show(allSections[i]);
					}

					break;
				}

				BX.addClass(allSections[i], 'bx-step-completed');
				allSections[i].setAttribute('data-visited', 'true');
				i++;
			}
		},

		/**
		 * Returns footer node with navigation buttons
		 */
		getBlockFooter: function(node)
		{
			console.log('getBlockFooter');
			var sections = this.orderBlockNode.querySelectorAll('.bx-soa-section.bx-active'),
				firstSection = sections[0],
				lastSection = sections[sections.length - 1],
				currentSection = BX.findParent(node, {className: "bx-soa-section"}),
				isLastNode = false,
				buttons = [];

			if (currentSection && currentSection.id.indexOf(firstSection.id) == '-1')
			{
				buttons.push(
					BX.create('A', {
						props: {
							href: 'javascript:void(0)',
							className: 'pull-left btn btn-default btn-md'
						},
						html: this.params.MESS_BACK,
						events: {
							click: BX.proxy(this.clickPrevAction, this)
						}
					})
				);
			}

			if (currentSection && currentSection.id.indexOf(lastSection.id) != '-1')
				isLastNode = true;

			if (!isLastNode)
			{
				buttons.push(
					BX.create('A', {
						props: {href: 'javascript:void(0)', className: 'pull-right btn btn-default btn-md'},
						html: this.params.MESS_FURTHER,
						events: {click: BX.proxy(this.clickNextAction, this)}
					})
				);
			}

			node.appendChild(
				BX.create('DIV', {
					props: {className: 'row bx-soa-more'},
					children: [
						BX.create('DIV', {
							props: {className: 'bx-soa-more-btn col-xs-12'},
							children: buttons
						})
					]
				})
			);
		},

		getNewContainer: function(notFluid)
		{
			console.log('getNewContainer');
			return BX.create('DIV', {props: {className: 'bx-soa-section-content' + (!!notFluid ? '' : ' container-fluid')}});
		},

		/**
		 * Showing/hiding order save buttons
		 */
		switchOrderSaveButtons: function(state)
		{
			console.log('switchOrderSaveButtons');
			var orderSaveNode = this.orderSaveBlockNode,
				totalButton = this.totalBlockNode.querySelector('.calculate__btn'),
				mobileButton = this.mobileTotalBlockNode.querySelector('.calculate__btn'),
				lastState = this.orderSaveBlockNode.style.display == '';

			if (lastState != state)
			{
				if (state)
				{
					orderSaveNode.style.opacity = 0;
					orderSaveNode.style.display = '';
					if (totalButton)
					{
						totalButton.style.opacity = 0;
						totalButton.style.display = '';
					}
					if (mobileButton)
					{
						mobileButton.style.opacity = 0;
						mobileButton.style.display = '';
					}

					new BX.easing({
						duration: 500,
						start: {opacity: 0},
						finish: {opacity: 100},
						transition: BX.easing.transitions.linear,
						step: function(state){
							orderSaveNode.style.opacity = state.opacity / 100;
							if (totalButton)
								totalButton.style.opacity = state.opacity / 100;
							if (mobileButton)
								mobileButton.style.opacity = state.opacity / 100;
						},
						complete: function(){
							orderSaveNode.removeAttribute('style');
							totalButton && totalButton.removeAttribute('style');
							mobileButton && mobileButton.removeAttribute('style');
						}
					}).animate();
				}
				else
				{
					orderSaveNode.style.display = 'none';
					if (totalButton)
						totalButton.setAttribute('style', 'display: none !important');
					if (mobileButton)
						mobileButton.setAttribute('style', 'display: none !important');
				}
			}
		},

		/**
		 * Returns true if current section or next sections had already visited
		 */
		shouldBeSectionVisible: function(sections, currentPosition)
		{
			console.log('shouldBeSectionVisible');
			var state = false, editStepNode;

			if (!sections || !sections.length)
				return state;

			for (; currentPosition < sections.length; currentPosition++)
			{
				if (sections[currentPosition].getAttribute('data-visited') == 'true')
				{
					state = true;
					break;
				}

				if (!this.firstLoad)
				{
					editStepNode = sections[currentPosition].querySelector('.bx-soa-editstep');
					if (editStepNode && editStepNode.style.display !== 'none')
					{
						state = true;
						break;
					}
				}
			}

			return state;
		},

		/**
		 * Showing/hiding blocks content if user authorized/unauthorized
		 */
		changeVisibleContent: function()
		{
			console.log('changeVisibleContent');
			BX.create("DIV",{
				props:{
					id:'bx-soa-total',
					className: 'section'
				}
			});
			var sections = this.orderBlockNode.querySelectorAll('.section'),
				i, state;
			var orderDataLoaded = !!this.result.IS_AUTHORIZED && this.params.USE_PRELOAD === 'Y' && this.result.LAST_ORDER_DATA.FAIL !== true,
				skipFlag = true;
			console.log(sections)
			for (i = 0; i < sections.length; i++)
			{
				state = this.firstLoad && orderDataLoaded;
				state = true;
				if (this.firstLoad && skipFlag)
				{
					if (
						state
						&& sections[i + 1]
						&& this.checkBlockErrors(sections[i])
						&& (
							(orderDataLoaded && this.checkPreload(sections[i]))
							|| (!orderDataLoaded && this.shouldSkipSection(sections[i]))
						)
					)
					{
						this.fade(sections[i]);
						this.markSectionAsCompleted(sections[i]);
						this.show(sections[i + 1]);
					}
					else
					{
						skipFlag = false;
					}
				}
			}

			if (
				(!this.result.IS_AUTHORIZED || typeof this.result.LAST_ORDER_DATA.FAIL !== 'undefined')
				&& this.params.SHOW_ORDER_BUTTON === 'final_step'
			)
			{
				//this.switchOrderSaveButtons(true);
			}
		},

		changeVisibleSection: function(section, state)
		{
			console.log('changeVisibleSection');
			var titleNode, content, editStep;

			if (section.id !== this.basketBlockNode.id)
			{
				content = section.querySelector('.bx-soa-section-content');
				if (content)
					content.style.display = state ? '' : 'none';
			}

			editStep = section.querySelector('.bx-soa-editstep');
			if (editStep)
				editStep.style.display = state ? '' : 'none';

			titleNode = section.querySelector('.bx-soa-section-title-container');
			if (titleNode && !state)
				BX.unbindAll(titleNode);
		},

		clearBlocks: function()
		{
			console.log('clearBlocks');
			let regionLabel = this.regionBlockNode.querySelector('.calculate__input-select-btn-load');
			BX.remove(regionLabel.querySelector('input'));
			regionLabel.style.display = "none";
			BX.cleanNode(this.regionBlockNode.querySelector('.calculate__input--select'));
			BX.cleanNode(this.paySystemBlockNode.querySelector('.calculate__order-pay-option-block'));
			let labels = this.orderBlockNode.querySelectorAll('label[data-property-id-row]');
			for(let label in labels){
				BX.remove(labels[label]);
			}
			this.propsBlockNode = BX.create("DIV", {
				props:{
					id:"bx-soa-properties",
					className:'section'
				}
			});
			BX.insertBefore(this.propsBlockNode, this.paySystemBlockNode);
			BX.remove(BX.lastChild(this.authHiddenBlockNode));
			BX.remove(BX.lastChild(this.basketHiddenBlockNode));
			BX.remove(BX.lastChild(this.regionHiddenBlockNode));
			BX.remove(BX.lastChild(this.paySystemHiddenBlockNode));
			BX.remove(BX.lastChild(this.deliveryHiddenBlockNode));
			BX.remove(BX.lastChild(this.pickUpHiddenBlockNode));
			BX.remove(BX.lastChild(this.propsHiddenBlockNode));
		},

		/**
		 * Edit order block nodes with this.result/this.params data
		 */
		editOrder: function()
		{
			if (!this.orderBlockNode || !this.result)
				return;
			var sections = this.orderBlockNode.querySelectorAll('.section'), i;
			for (i in sections)
			{
				if (sections.hasOwnProperty(i))
				{
					this.editSection(sections[i]);
				}
			}
			this.editTotalBlock();

			this.showErrors(this.result.ERROR, false);
			//this.showWarnings();

		},

		/**
		 * Edit certain block node
		 */
		editSection: function(section)
		{
			if (!section || !section.id)
				return;
			/*if (this.result.SHOW_AUTH && section.id != this.authBlockNode.id)
				section.style.display = 'none';
			else section.style.display = '';*/
			switch (section.id)
			{
				/*case this.authBlockNode.id:
					this.editAuthBlock();
					break;*/
				case this.regionBlockNode.id:
					this.editRegionBlock();
					break;
				case this.paySystemBlockNode.id:
					this.editPaySystemBlock();
					break;
				case this.propsBlockNode.id:
					this.editPropsBlock();
					break;
			}
			
			/*if (active)
				section.setAttribute('data-visited', 'true');*/
		},

		editAuthBlock: function()
		{
			console.log('editAuthBlock');
			if (!this.authBlockNode)
				return;

			var authContent = this.authBlockNode.querySelector('.bx-soa-section-content'),
				regContent, okMessageNode;

			if (BX.hasClass(authContent, 'reg'))
			{
				regContent = authContent;
				authContent = BX.firstChild(this.authHiddenBlockNode);
			}
			else
				regContent = BX.firstChild(this.authHiddenBlockNode);

			BX.cleanNode(authContent);
			BX.cleanNode(regContent);

			if (this.result.SHOW_AUTH)
			{
				this.getErrorContainer(authContent);
				this.editAuthorizeForm(authContent);
				this.editSocialContent(authContent);
				this.getAuthReference(authContent);

				this.getErrorContainer(regContent);
				this.editRegistrationForm(regContent);
				this.getAuthReference(regContent);
			}
			else
			{
				BX.onCustomEvent('OnBasketChange');
				this.closeAuthBlock();
			}

			if (this.result.OK_MESSAGE && this.result.OK_MESSAGE.length)
			{
				this.toggleAuthForm({target: this.authBlockNode.querySelector('input[type=submit]')});
				okMessageNode = BX.create('DIV', {
					props: {className: 'alert alert-success'},
					text: this.result.OK_MESSAGE.join()
				});
				this.result.OK_MESSAGE = '';
				BX.prepend(okMessageNode, this.authBlockNode.querySelector('.bx-soa-section-content'));
			}
		},

		editAuthorizeForm: function(authContent)
		{
			console.log('editAuthorizeForm');
			var login, password, remember, button, authFormNode;

			login = this.createAuthFormInputContainer(
				BX.message('STOF_LOGIN'),
				BX.create('INPUT', {
					attrs: {'data-next': 'USER_PASSWORD'},
					props: {
						name: 'USER_LOGIN',
						type: 'text',
						value: this.result.AUTH.USER_LOGIN,
						maxlength: "30"
					},
					events: {keypress: BX.proxy(this.checkKeyPress, this)}
				})
			);
			password = this.createAuthFormInputContainer(
				BX.message('STOF_PASSWORD'),
				BX.create('INPUT', {
					attrs: {'data-send': true},
					props: {
						name: 'USER_PASSWORD',
						type: 'password',
						value: '',
						maxlength: "30"
					},
					events: {keypress: BX.proxy(this.checkKeyPress, this)}
				})
			);
			remember = BX.create('DIV', {
				props: {className: 'bx-authform-formgroup-container'},
				children: [
					BX.create('DIV', {
						props: {className: 'checkbox'},
						children: [
							BX.create('LABEL', {
								props: {className: 'bx-filter-param-label'},
								children: [
									BX.create('INPUT', {
										props: {
											type: 'checkbox',
											name: 'USER_REMEMBER',
											value: 'Y'
										}
									}),
									BX.create('SPAN', {props: {className: 'bx-filter-param-text'}, text: BX.message('STOF_REMEMBER')})
								]
							})
						]
					})
				]
			});
			button = BX.create('DIV', {
				props: {className: 'bx-authform-formgroup-container'},
				children: [
					BX.create('INPUT', {
						props: {
							id: 'do_authorize',
							type: 'hidden',
							name: 'do_authorize',
							value: 'N'
						}
					}),
					BX.create('INPUT', {
						props: {
							type: 'submit',
							className: 'btn btn-lg btn-default',
							value: BX.message('STOF_ENTER')
						},
						events: {
							click: BX.delegate(function(e){
								BX('do_authorize').value = 'Y';
								this.sendRequest('showAuthForm');
								return BX.PreventDefault(e);
							}, this)
						}
					})
				]
			});
			authFormNode = BX.create('DIV', {
				props: {className: 'bx-authform'},
				children: [
					BX.create('H3', {props: {className: 'bx-title'}, text: BX.message('STOF_AUTH_REQUEST')}),
					login,
					password,
					remember,
					button,
					BX.create('A', {
						props: {
							href: this.params.PATH_TO_AUTH + '?forgot_password=yes&back_url=' + encodeURIComponent(document.location.href)
						},
						text: BX.message('STOF_FORGET_PASSWORD')
					})
				]
			});

			authContent.appendChild(BX.create('DIV', {props: {className: 'col-md-6'}, children: [authFormNode]}));
		},

		createAuthFormInputContainer: function(labelText, inputNode, required)
		{
			console.log('createAuthFormInputContainer');
			var labelHtml = '';

			if (required)
				labelHtml += '<span class="bx-authform-starrequired">*</span>';

			labelHtml += labelText;

			return BX.create('DIV', {
				props: {className: 'bx-authform-formgroup-container'},
				children: [
					BX.create('DIV', {props: {className: 'bx-authform-label-container'}, html: labelHtml}),
					BX.create('DIV', {props: {className: 'bx-authform-input-container'},  children: [inputNode]})
				]
			});
		},

		activatePhoneAuth: function()
		{
			console.log('activatePhoneAuth');
			if (!this.result.SMS_AUTH)
				return;

			new BX.PhoneAuth({
				containerId: 'bx_register_resend',
				errorContainerId: 'bx_register_error',
				interval: 60,
				data: {
					signedData: this.result.SMS_AUTH.SIGNED_DATA
				},
				onError: function(response)
				{
					var errorDiv = BX('bx_register_error');
					var errorNode = BX.findChildByClassName(errorDiv, 'errortext');
					errorNode.innerHTML = '';

					for (var i = 0; i < response.errors.length; i++)
					{
						errorNode.innerHTML = errorNode.innerHTML + BX.util.htmlspecialchars(response.errors[i].message) + '<br>';
					}

					errorDiv.style.display = '';
				}
			});
		},

		editRegistrationForm: function(authContent)
		{
			console.log('editRegistrationForm');
			if (!this.result.AUTH)
				return;

			var authFormNodes = [];
			var showSmsConfirm = this.result.SMS_AUTH && this.result.SMS_AUTH.TYPE === 'OK';

			if (showSmsConfirm)
			{
				authFormNodes.push(BX.create('DIV', {
					props: {className: 'alert alert-success'},
					text: BX.message('STOF_REG_SMS_REQUEST')
				}));
				authFormNodes.push(BX.create('INPUT', {
					props: {
						type: 'hidden',
						name: 'SIGNED_DATA',
						value: this.result.SMS_AUTH.SIGNED_DATA || ''
					}
				}));
				authFormNodes.push(this.createAuthFormInputContainer(
					BX.message('STOF_SMS_CODE'),
					BX.create('INPUT', {
						attrs: {'data-send': true},
						props: {
							name: 'SMS_CODE',
							type: 'text',
							size: 40,
							value: ''
						},
						events: {keypress: BX.proxy(this.checkKeyPress, this)}
					}),
					true
				));
				authFormNodes.push(BX.create('DIV', {
					props: {className: 'bx-authform-formgroup-container'},
					children: [
						BX.create('INPUT', {
							props: {
								name: 'code_submit_button',
								type: 'submit',
								className: 'btn btn-lg btn-default',
								value: BX.message('STOF_SEND')
							},
							events: {
								click: BX.delegate(function(e)
								{
									this.sendRequest('confirmSmsCode');
									return BX.PreventDefault(e);
								}, this)
							}
						})
					]
				}));
				authFormNodes.push(BX.create('DIV', {
					props: {className: 'bx-authform-formgroup-container'},
					children: [
						BX.create('DIV', {
							props: {id: 'bx_register_error'},
							style: {display: 'none'}
						}),
						BX.create('DIV', {
							props: {id: 'bx_register_resend'}
						})
					]
				}));
			}
			else
			{
				authFormNodes.push(BX.create('H3', {
					props: {className: 'bx-title'},
					text: BX.message('STOF_REG_REQUEST')
				}));
				authFormNodes.push(this.createAuthFormInputContainer(
					BX.message('STOF_NAME'),
					BX.create('INPUT', {
						attrs: {'data-next': 'NEW_LAST_NAME'},
						props: {
							name: 'NEW_NAME',
							type: 'text',
							size: 40,
							value: this.result.AUTH.NEW_NAME || ''
						},
						events: {keypress: BX.proxy(this.checkKeyPress, this)}
					}),
					true
				));
				authFormNodes.push(this.createAuthFormInputContainer(
					BX.message('STOF_LASTNAME'),
					BX.create('INPUT', {
						attrs: {'data-next': 'NEW_EMAIL'},
						props: {
							name: 'NEW_LAST_NAME',
							type: 'text',
							size: 40,
							value: this.result.AUTH.NEW_LAST_NAME || ''
						},
						events: {keypress: BX.proxy(this.checkKeyPress, this)}
					}),
					true
				));
				authFormNodes.push(this.createAuthFormInputContainer(
					BX.message('STOF_EMAIL'),
					BX.create('INPUT', {
						attrs: {'data-next': 'PHONE_NUMBER'},
						props: {
							name: 'NEW_EMAIL',
							type: 'text',
							size: 40,
							value: this.result.AUTH.NEW_EMAIL || ''
						},
						events: {keypress: BX.proxy(this.checkKeyPress, this)}
					}),
					this.result.AUTH.new_user_email_required == 'Y'
				));

				if (this.result.AUTH.new_user_phone_auth === 'Y')
				{
					authFormNodes.push(this.createAuthFormInputContainer(
						BX.message('STOF_PHONE'),
						BX.create('INPUT', {
							attrs: {'data-next': 'captcha_word'},
							props: {
								name: 'PHONE_NUMBER',
								type: 'text',
								size: 40,
								value: this.result.AUTH.PHONE_NUMBER || ''
							},
							events: {keypress: BX.proxy(this.checkKeyPress, this)}
						}),
						this.result.AUTH.new_user_phone_required === 'Y'
					));
				}

				if (this.authGenerateUser)
				{
					authFormNodes.push(
						BX.create('LABEL', {
							props: {for: 'NEW_GENERATE_N'},
							children: [
								BX.create('INPUT', {
									attrs: {checked: !this.authGenerateUser},
									props: {
										id: 'NEW_GENERATE_N',
										type: 'radio',
										name: 'NEW_GENERATE',
										value: 'N'
									}
								}),
								BX.message('STOF_MY_PASSWORD')
							],
							events: {
								change: BX.delegate(function(){
									var generated = this.authBlockNode.querySelector('.generated');
									generated.style.display = '';
									this.authGenerateUser = false;
								}, this)
							}
						})
					);
					authFormNodes.push(BX.create('BR'));
					authFormNodes.push(
						BX.create('LABEL', {
							props: {for: 'NEW_GENERATE_Y'},
							children: [
								BX.create('INPUT', {
									attrs: {checked: this.authGenerateUser},
									props: {
										id: 'NEW_GENERATE_Y',
										type: 'radio',
										name: 'NEW_GENERATE',
										value: 'Y'
									}
								}),
								BX.message('STOF_SYS_PASSWORD')
							],
							events: {
								change: BX.delegate(function(){
									var generated = this.authBlockNode.querySelector('.generated');
									generated.style.display = 'none';
									this.authGenerateUser = true;
								}, this)
							}
						})
					);
				}

				authFormNodes.push(
					BX.create('DIV', {
						props: {className: 'generated'},
						style: {display: this.authGenerateUser ? 'none' : ''},
						children: [
							this.createAuthFormInputContainer(
								BX.message('STOF_LOGIN'),
								BX.create('INPUT', {
									props: {
										name: 'NEW_LOGIN',
										type: 'text',
										size: 30,
										value: this.result.AUTH.NEW_LOGIN || ''
									},
									events: {
										keypress: BX.proxy(this.checkKeyPress, this)
									}
								}),
								true
							),
							this.createAuthFormInputContainer(
								BX.message('STOF_PASSWORD'),
								BX.create('INPUT', {
									props: {
										name: 'NEW_PASSWORD',
										type: 'password',
										size: 30
									},
									events: {
										keypress: BX.proxy(this.checkKeyPress, this)
									}
								}),
								true
							),
							this.createAuthFormInputContainer(
								BX.message('STOF_RE_PASSWORD'),
								BX.create('INPUT', {
									props: {
										name: 'NEW_PASSWORD_CONFIRM',
										type: 'password',
										size: 30
									},
									events: {
										keypress: BX.proxy(this.checkKeyPress, this)
									}
								}),
								true
							)
						]
					})
				);

				if (this.result.AUTH.captcha_registration == 'Y')
				{
					authFormNodes.push(BX.create('DIV', {
						props: {className: 'bx-authform-formgroup-container'},
						children: [
							BX.create('DIV', {
								props: {className: 'bx-authform-label-container'},
								children: [
									BX.create('SPAN', {props: {className: 'bx-authform-starrequired'}, text: '*'}),
									BX.message('CAPTCHA_REGF_PROMT'),
									BX.create('DIV', {
										props: {className: 'bx-captcha'},
										children: [
											BX.create('INPUT', {
												props: {
													name: 'captcha_sid',
													type: 'hidden',
													value: this.result.AUTH.capCode || ''
												}
											}),
											BX.create('IMG', {
												props: {
													src: '/bitrix/tools/captcha.php?captcha_sid=' + this.result.AUTH.capCode,
													alt: ''
												}
											})
										]
									})
								]
							}),
							BX.create('DIV', {
								props: {className: 'bx-authform-input-container'},
								children: [
									BX.create('INPUT', {
										attrs: {'data-send': true},
										props: {
											name: 'captcha_word',
											type: 'text',
											size: '30',
											maxlength: '50',
											value: ''
										},
										events: {keypress: BX.proxy(this.checkKeyPress, this)}
									})
								]
							})
						]
					}));
				}
				authFormNodes.push(
					BX.create('DIV', {
						props: {className: 'bx-authform-formgroup-container'},
						children: [
							BX.create('INPUT', {
								props: {
									id: 'do_register',
									name: 'do_register',
									type: 'hidden',
									value: 'N'
								}
							}),
							BX.create('INPUT', {
								props: {
									type: 'submit',
									className: 'btn btn-lg btn-default',
									value: BX.message('STOF_REGISTER')
								},
								events: {
									click: BX.delegate(function(e){
										BX('do_register').value = 'Y';
										this.sendRequest('showAuthForm');
										return BX.PreventDefault(e);
									}, this)
								}
							}),
							BX.create('A', {
								props: {className: 'btn btn-link', href: ''},
								text: BX.message('STOF_DO_AUTHORIZE'),
								events: {
									click: BX.delegate(function(e){
										this.toggleAuthForm(e);
										return BX.PreventDefault(e);
									}, this)
								}
							})
						]
					})
				);
			}

			authContent.appendChild(
				BX.create('DIV', {
					props: {className: 'col-md-12'},
					children: [BX.create('DIV', {props: {className: 'bx-authform'}, children: authFormNodes})]
				})
			);

			if (showSmsConfirm)
			{
				this.activatePhoneAuth();
			}
		},

		editSocialContent: function(authContent)
		{
			console.log('editSocialContent');
			if (!BX('bx-soa-soc-auth-services'))
				return;

			var nodes = [];

			if (this.socServiceHiddenNode === false)
			{
				var socServiceHiddenNode = BX('bx-soa-soc-auth-services').querySelector('.bx-authform-social');

				if (BX.type.isDomNode(socServiceHiddenNode))
				{
					this.socServiceHiddenNode = socServiceHiddenNode.innerHTML;
					BX.remove(socServiceHiddenNode);
				}
			}

			if (this.socServiceHiddenNode)
			{
				nodes.push(BX.create('DIV', {
					props: {className: 'bx-authform-social'},
					html: '<h3 class="bx-title">' + BX.message('SOA_DO_SOC_SERV') + '</h3>' + this.socServiceHiddenNode
				}));
				nodes.push(BX.create('hr', {props: {className: 'bxe-light'}}));
			}

			if (this.result.AUTH.new_user_registration === 'Y')
			{
				nodes.push(BX.create('DIV', {
					props: {className: 'bx-soa-reg-block'},
					children: [
						BX.create('P', {html: this.params.MESS_REGISTRATION_REFERENCE}),
						BX.create('A', {
							props: {className: 'btn btn-default btn-lg'},
							text: BX.message('STOF_DO_REGISTER'),
							events: {
								click: BX.delegate(function(e){
									this.toggleAuthForm(e);
									return BX.PreventDefault(e);
								}, this)
							}
						})
					]
				}));
			}

			authContent.appendChild(BX.create('DIV', {props: {className: 'col-md-6'}, children: nodes}));
		},

		getAuthReference: function(authContent)
		{
			console.log('getAuthReference');
			authContent.appendChild(
				BX.create('DIV', {
					props: {className: 'row'},
					children: [
						BX.create('DIV', {
							props: {className: 'bx-soa-reference col-xs-12'},
							children: [
								this.params.MESS_AUTH_REFERENCE_1,
								BX.create('BR'),
								this.params.MESS_AUTH_REFERENCE_2,
								BX.create('BR'),
								this.params.MESS_AUTH_REFERENCE_3
							]
						})
					]
				})
			);
		},

		toggleAuthForm: function(event)
		{
			console.log('toggleAuthForm');
			if (!event)
				return;

			var target = event.target || event.srcElement,
				section = BX.findParent(target, {className: 'bx-soa-section'}),
				container = BX.findParent(target, {className: 'bx-soa-section-content'}),
				insertContainer = BX.firstChild(this.authHiddenBlockNode);

			new BX.easing({
				duration: 100,
				start: {opacity: 100},
				finish: {opacity: 0},
				transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
				step: function(state){
					container.style.opacity = state.opacity / 100;
				}
			}).animate();

			this.authHiddenBlockNode.appendChild(container);
			BX.cleanNode(section);
			section.appendChild(
				BX.create('DIV', {
					props: {className: 'bx-soa-section-title-container'},
					children: [
						BX.create('h2', {
							props: {className: 'bx-soa-section-title col-xs-7 col-sm-9'},
							html: BX.hasClass(insertContainer, 'reg') ? this.params.MESS_REG_BLOCK_NAME : this.params.MESS_AUTH_BLOCK_NAME
						})
					]
				})
			);
			insertContainer.style.opacity = 0;
			section.appendChild(insertContainer);

			setTimeout(function(){
				new BX.easing({
					duration: 100,
					start: {opacity: 0},
					finish: {opacity: 100},
					transition: BX.easing.makeEaseOut(BX.easing.transitions.quart),
					step: function(state){
						insertContainer.style.opacity = state.opacity / 100;
					},
					complete: function() {
						insertContainer.style.height = '';
						insertContainer.style.opacity = '';
					}
				}).animate();
			}, 110);

			this.animateScrollTo(section);
		},

		alignBasketColumns: function()
		{
			console.log('alignBasketColumns');
			if (!this.basketBlockNode)
				return;

			var i = 0, k, columns = 0, columnNodes,
				windowSize = BX.GetWindowInnerSize(),
				basketRows, percent;

			if (windowSize.innerWidth > 580 && windowSize.innerWidth < 992)
			{
				basketRows = this.basketBlockNode.querySelectorAll('.bx-soa-basket-info');
				percent = 100;

				if (basketRows.length)
				{
					columnNodes = basketRows[0].querySelectorAll('.bx-soa-item-properties');

					if (columnNodes.length && columnNodes[0].style.width != '')
						return;

					columns = columnNodes.length;
					if (columns > 0)
					{
						columns = columns > 4 ? 4 : columns;
						percent = parseInt(percent / columns);
						for (; i < basketRows.length; i++)
						{
							columnNodes = basketRows[i].querySelectorAll('.bx-soa-item-properties')
							for (k = 0; k < columnNodes.length; k++)
							{
								columnNodes[k].style.width = percent + '%';
							}
						}
					}
				}
			}
			else
			{
				columnNodes = this.basketBlockNode.querySelectorAll('.bx-soa-item-properties');

				if (columnNodes.length && columnNodes[0].style.width == '')
					return;

				for (; i < columnNodes.length; i++)
				{
					columnNodes[i].style.width = '';
				}
			}
		},

		editBasketBlock: function(active)
		{
			console.log('editBasketBlock');
			if (!this.basketBlockNode || !this.basketHiddenBlockNode || !this.result.GRID)
				return;

			BX.remove(BX.lastChild(this.basketBlockNode));
			BX.remove(BX.lastChild(this.basketHiddenBlockNode));

			this.editActiveBasketBlock(active);
			this.editFadeBasketBlock(active);

			this.initialized.basket = true;
		},

		editActiveBasketBlock: function(activeNodeMode)
		{
			console.log('editActiveBasketBlock');
			var node = !!activeNodeMode ? this.basketBlockNode : this.basketHiddenBlockNode,
				basketContent, basketTable;

			if (this.initialized.basket)
			{
				this.basketHiddenBlockNode.appendChild(BX.lastChild(node));
				node.appendChild(BX.firstChild(this.basketHiddenBlockNode));
			}
			else
			{
				basketContent = node.querySelector('.bx-soa-section-content');
				basketTable = BX.create('DIV', {props: {className: 'bx-soa-item-table'}});

				if (!basketContent)
				{
					basketContent = this.getNewContainer();
					node.appendChild(basketContent);
				}
				else
				{
					BX.cleanNode(basketContent);
				}

				this.editBasketItems(basketTable, true);

				basketContent.appendChild(
					BX.create('DIV', {
						props: {className: 'bx-soa-table-fade'},
						children: [
							BX.create('DIV', {
								style: {overflowX: 'auto', overflowY: 'hidden'},
								children: [basketTable]
							})
						]
					})
				);

				if (this.params.SHOW_COUPONS_BASKET === 'Y')
				{
					this.editCoupons(basketContent);
				}

				this.getBlockFooter(basketContent);

				BX.bind(
					basketContent.querySelector('div.bx-soa-table-fade').firstChild,
					'scroll',
					BX.proxy(this.basketBlockScrollCheckEvent, this)
				);
			}

			this.alignBasketColumns();
		},

		editFadeBasketBlock: function(activeNodeMode)
		{
			console.log('editFadeBasketBlock');
			var node = !!activeNodeMode ? this.basketHiddenBlockNode : this.basketBlockNode,
				newContent, basketTable;

			if (this.initialized.basket)
			{
				this.basketHiddenBlockNode.appendChild(node.querySelector('.bx-soa-section-content'));
				this.basketBlockNode.appendChild(BX.firstChild(this.basketHiddenBlockNode));
			}
			else
			{
				newContent = this.getNewContainer();
				basketTable = BX.create('DIV', {props: {className: 'bx-soa-item-table'}});

				this.editBasketItems(basketTable, false);

				newContent.appendChild(
					BX.create('DIV', {
						props: {className: 'bx-soa-table-fade'},
						children: [
							BX.create('DIV', {
								style: {overflowX: 'auto', overflowY: 'hidden'},
								children: [basketTable]
							})
						]
					})
				);

				if (this.params.SHOW_COUPONS_BASKET === 'Y')
				{
					this.editCouponsFade(newContent);
				}

				node.appendChild(newContent);
				this.alignBasketColumns();
				this.basketBlockScrollCheck();

				BX.bind(
					this.basketBlockNode.querySelector('div.bx-soa-table-fade').firstChild,
					'scroll',
					BX.proxy(this.basketBlockScrollCheckEvent, this)
				);
			}

			this.alignBasketColumns();
		},

		editBasketItems: function(basketItemsNode, active)
		{
			console.log('editBasketItems');
			if (!this.result.GRID.ROWS)
				return;

			var index = 0, i;

			if (this.params.SHOW_BASKET_HEADERS === 'Y')
			{
				this.editBasketItemsHeader(basketItemsNode);
			}

			for (i in this.result.GRID.ROWS)
			{
				if (this.result.GRID.ROWS.hasOwnProperty(i))
				{
					this.createBasketItem(basketItemsNode, this.result.GRID.ROWS[i], index++, !!active);
				}
			}
		},

		editBasketItemsHeader: function(basketItemsNode)
		{
			console.log('editBasketItemsHeader');
			if (!basketItemsNode)
				return;

			var headers = [
					BX.create('DIV', {
						props: {className: 'bx-soa-item-td'},
						style: {paddingBottom: '5px'},
						children: [
							BX.create('DIV', {
								props: {className: 'bx-soa-item-td-title'},
								text: BX.message('SOA_SUM_NAME')
							})
						]
					})
				],
				toRight = false, column, basketColumnIndex = 0, i;

			for (i = 0; i < this.result.GRID.HEADERS.length; i++)
			{
				column = this.result.GRID.HEADERS[i];

				if (column.id === 'NAME' || column.id === 'PREVIEW_PICTURE' || column.id === 'PROPS' || column.id === 'NOTES')
					continue;

				if (column.id === 'DETAIL_PICTURE' && !this.options.showPreviewPicInBasket)
					continue;

				toRight = BX.util.in_array(column.id, ["QUANTITY", "PRICE_FORMATED", "DISCOUNT_PRICE_PERCENT_FORMATED", "SUM"]);
				headers.push(
					BX.create('DIV', {
						props: {className: 'bx-soa-item-td bx-soa-item-properties' + (toRight ? ' bx-text-right' : '')},
						style: {paddingBottom: '5px'},
						children: [
							BX.create('DIV', {
								props: {className: 'bx-soa-item-td-title'},
								text: column.name
							})
						]
					})
				);

				++basketColumnIndex;
				if (basketColumnIndex == 4 && this.result.GRID.HEADERS[i + 1])
				{
					headers.push(BX.create('DIV', {props: {className: 'bx-soa-item-nth-4p1'}}));
					basketColumnIndex = 0;
				}
			}

			basketItemsNode.appendChild(
				BX.create('DIV', {
					props: {className: 'bx-soa-item-tr hidden-sm hidden-xs'},
					children: headers
				})
			);
		},

		createBasketItem: function(basketItemsNode, item, index, active)
		{
			console.log('createBasketItem');
			var mainColumns = [],
				otherColumns = [],
				hiddenColumns = [],
				currentColumn, basketColumnIndex = 0,
				i, tr, cols;

			if (this.options.showPreviewPicInBasket || this.options.showDetailPicInBasket)
				mainColumns.push(this.createBasketItemImg(item.data));

			mainColumns.push(this.createBasketItemContent(item.data));

			for (i = 0; i < this.result.GRID.HEADERS.length; i++)
			{
				currentColumn = this.result.GRID.HEADERS[i];

				if (currentColumn.id === 'NAME' || currentColumn.id === 'PREVIEW_PICTURE' || currentColumn.id === 'PROPS' || currentColumn.id === 'NOTES')
					continue;

				if (currentColumn.id === 'DETAIL_PICTURE' && !this.options.showPreviewPicInBasket)
					continue;

				otherColumns.push(this.createBasketItemColumn(currentColumn, item, active));

				++basketColumnIndex;
				if (basketColumnIndex == 4 && this.result.GRID.HEADERS[i + 1])
				{
					otherColumns.push(BX.create('DIV', {props: {className: 'bx-soa-item-nth-4p1'}}));
					basketColumnIndex = 0;
				}
			}

			if (active)
			{
				for (i = 0; i < this.result.GRID.HEADERS_HIDDEN.length; i++)
				{
					tr = this.createBasketItemHiddenColumn(this.result.GRID.HEADERS_HIDDEN[i], item);
					if (BX.type.isArray(tr))
						hiddenColumns = hiddenColumns.concat(tr);
					else if (tr)
						hiddenColumns.push(tr);
				}
			}

			cols = [
				BX.create('DIV', {
					props: {className: 'bx-soa-item-td'},
					style: {minWidth: '300px'},
					children: [
						BX.create('DIV', {
							props: {className: 'bx-soa-item-block'},
							children: mainColumns
						})
					]
				})
			].concat(otherColumns);

			basketItemsNode.appendChild(
				BX.create('DIV', {
					props: {className: 'bx-soa-item-tr bx-soa-basket-info' + (index == 0 ? ' bx-soa-item-tr-first' : '')},
					children: cols
				})
			);

			if (hiddenColumns.length)
			{
				basketItemsNode.appendChild(
					BX.create('DIV', {
						props: {className: 'bx-soa-item-tr bx-soa-item-info-container'},
						children: [
							BX.create('DIV', {
								props: {className: 'bx-soa-item-td'},
								children: [
									BX.create('A', {
										props: {href: '', className: 'bx-soa-info-shower'},
										html: this.params.MESS_ADDITIONAL_PROPS,
										events: {
											click: BX.proxy(this.showAdditionalProperties, this)
										}
									}),
									BX.create('DIV', {
										props: {className: 'bx-soa-item-info-block'},
										children: [
											BX.create('TABLE', {
												props: {className: 'bx-soa-info-block'},
												children: hiddenColumns
											})
										]
									})
								]
							})
						]
					})
				);
			}
		},

		showAdditionalProperties: function(event)
		{
			console.log('showAdditionalProperties');
			var target = event.target || event.srcElement,
				infoContainer = target.nextSibling,
				parentContainer = BX.findParent(target, {className: 'bx-soa-item-tr bx-soa-item-info-container'}),
				parentHeight = parentContainer.offsetHeight;

			if (BX.hasClass(infoContainer, 'bx-active'))
			{
				new BX.easing({
					duration: 300,
					start: {opacity: 100, height: parentHeight},
					finish: {opacity: 0, height: 35},
					transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
					step: function(state){
						infoContainer.style.opacity = state.opacity / 100;
						infoContainer.style.height = state.height + 'px';
						parentContainer.style.height = state.height + 'px';
					},
					complete: function(){
						BX.removeClass(infoContainer, 'bx-active');
						infoContainer.removeAttribute("style");
						parentContainer.removeAttribute("style");
					}
				}).animate();
			}
			else
			{
				infoContainer.style.opacity = 0;
				BX.addClass(infoContainer, 'bx-active');
				var height = infoContainer.offsetHeight + parentHeight;
				BX.removeClass(infoContainer, 'bx-active');
				infoContainer.style.paddingTop = '10px';

				new BX.easing({
					duration: 300,
					start: {opacity: 0, height: parentHeight},
					finish: {opacity: 100, height: height},
					transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
					step: function(state){
						infoContainer.style.opacity = state.opacity / 100;
						infoContainer.style.height = state.height + 'px';
						parentContainer.style.height = state.height + 'px';
					},
					complete: function(){
						BX.addClass(infoContainer, 'bx-active');
						infoContainer.removeAttribute("style");
					}
				}).animate();
			}

			return BX.PreventDefault(event);
		},

		createBasketItemImg: function(data)
		{
			console.log('createBasketItemImg');
			if (!data)
				return;

			var logoNode, logotype;

			logoNode = BX.create('DIV', {props: {className: 'bx-soa-item-imgcontainer'}});

			if (data.PREVIEW_PICTURE_SRC && data.PREVIEW_PICTURE_SRC.length)
				logotype = this.getImageSources(data, 'PREVIEW_PICTURE');
			else if (data.DETAIL_PICTURE_SRC && data.DETAIL_PICTURE_SRC.length)
				logotype = this.getImageSources(data, 'DETAIL_PICTURE');

			if (logotype && logotype.src_2x)
			{
				logoNode.setAttribute('style',
					'background-image: url("' + logotype.src_1x + '");' +
					'background-image: -webkit-image-set(url("' + logotype.src_1x + '") 1x, url("' + logotype.src_2x + '") 2x)'
				);
			}
			else
			{
				logotype = logotype && logotype.src_1x || this.defaultBasketItemLogo;
				logoNode.setAttribute('style', 'background-image: url("' + logotype + '");');
			}

			if (this.params.HIDE_DETAIL_PAGE_URL !== 'Y' && data.DETAIL_PAGE_URL && data.DETAIL_PAGE_URL.length)
			{
				logoNode = BX.create('A', {
					props: {href: data.DETAIL_PAGE_URL},
					children: [logoNode]
				});
			}

			return BX.create('DIV', {
				props: {className: 'bx-soa-item-img-block'},
				children: [logoNode]
			});
		},

		createBasketItemContent: function(data)
		{
			console.log('createBasketItemContent');
			var itemName = data.NAME || '',
				titleHtml = this.htmlspecialcharsEx(itemName),
				props = data.PROPS || [],
				propsNodes = [];

			if (this.params.HIDE_DETAIL_PAGE_URL !== 'Y' && data.DETAIL_PAGE_URL && data.DETAIL_PAGE_URL.length)
			{
				titleHtml = '<a href="' + data.DETAIL_PAGE_URL + '">' + titleHtml + '</a>';
			}

			if (this.options.showPropsInBasket && props.length)
			{
				for (var i in props)
				{
					if (props.hasOwnProperty(i))
					{
						var name = props[i].NAME || '',
							value = props[i].VALUE || '';

						propsNodes.push(
							BX.create('DIV', {
								props: {className: 'bx-soa-item-td-title'},
								style: {textAlign: 'left'},
								text: name
							})
						);
						propsNodes.push(
							BX.create('DIV', {
								props: {className: 'bx-soa-item-td-text'},
								style: {textAlign: 'left'},
								text: value
							})
						);
					}
				}
			}

			return BX.create('DIV', {
				props: {className: 'bx-soa-item-content'},
				children: propsNodes.length ? [
					BX.create('DIV', {props: {className: 'bx-soa-item-title'}, html: titleHtml}),
					BX.create('DIV', {props: {className: 'bx-scu-container'}, children: propsNodes})
				] : [
					BX.create('DIV', {props: {className: 'bx-soa-item-title'}, html: titleHtml})
				]
			});
		},

		createBasketItemColumn: function(column, allData, active)
		{
			console.log('createBasketItemColumn');
			if (!column || !allData)
				return;

			var data = allData.columns[column.id] ? allData.columns : allData.data,
				toRight = BX.util.in_array(column.id, ["QUANTITY", "PRICE_FORMATED", "DISCOUNT_PRICE_PERCENT_FORMATED", "SUM"]),
				textNode = BX.create('DIV', {props: {className: 'bx-soa-item-td-text'}}),
				logotype, img;

			if (column.id === 'PRICE_FORMATED')
			{
				textNode.appendChild(BX.create('STRONG', {props: {className: 'bx-price'}, html: data.PRICE_FORMATED}));
				if (parseFloat(data.DISCOUNT_PRICE) > 0)
				{
					textNode.appendChild(BX.create('BR'));
					textNode.appendChild(BX.create('STRONG', {
						props: {className: 'bx-price-old'},
						html: data.BASE_PRICE_FORMATED
					}));
				}

				if (this.options.showPriceNotesInBasket && active)
				{
					textNode.appendChild(BX.create('BR'));
					textNode.appendChild(BX.create('SMALL', {text: data.NOTES}));
				}
			}
			else if (column.id === 'SUM')
			{
				textNode.appendChild(BX.create('STRONG', {props: {className: 'bx-price all'}, html: data.SUM}));
				if (parseFloat(data.DISCOUNT_PRICE) > 0)
				{
					textNode.appendChild(BX.create('BR'));
					textNode.appendChild(BX.create('STRONG', {
						props: {className: 'bx-price-old'},
						html: data.SUM_BASE_FORMATED
					}));
				}
			}
			else if (column.id === 'DISCOUNT')
			{
				textNode.appendChild(BX.create('STRONG', {props: {className: 'bx-price'}, text: data.DISCOUNT_PRICE_PERCENT_FORMATED}));
			}
			else if (column.id === 'DETAIL_PICTURE')
			{
				logotype = this.getImageSources(allData.data, column.id),
				img = BX.create('IMG', {props: {src: logotype && logotype.src_1x || this.defaultBasketItemLogo}});

				if (logotype && logotype.src_1x && logotype.src_orig)
				{
					BX.bind(img, 'click', BX.delegate(function(e){this.popupShow(e, logotype.src_orig);}, this));
				}

				textNode.appendChild(img);
			}
			else if (BX.util.in_array(column.id, ["QUANTITY", "WEIGHT_FORMATED", "DISCOUNT_PRICE_PERCENT_FORMATED"]))
			{
				textNode.appendChild(BX.create('SPAN', {html: data[column.id]}));
			}
			else if (column.id === 'PREVIEW_TEXT')
			{
				if (data['PREVIEW_TEXT_TYPE'] === 'html')
				{
					textNode.appendChild(BX.create('SPAN', {html: data['PREVIEW_TEXT'] || ''}));
				}
				else
				{
					textNode.appendChild(BX.create('SPAN', {text: data['PREVIEW_TEXT'] || ''}));
				}
			}
			else
			{
				var columnData = data[column.id], val = [];
				if (BX.type.isArray(columnData))
				{
					for (var i in columnData)
					{
						if (columnData.hasOwnProperty(i))
						{
							if (columnData[i].type == 'image')
								val.push(this.getImageContainer(columnData[i].value, columnData[i].source));
							else if (columnData[i].type == 'linked')
							{
								textNode.appendChild(BX.create('SPAN', {html: columnData[i].value_format}));
								textNode.appendChild(BX.create('BR'));
							}
							else if (columnData[i].value)
							{
								textNode.appendChild(BX.create('SPAN', {html: columnData[i].value}));
								textNode.appendChild(BX.create('BR'));
							}
						}
					}

					if (val.length)
					{
						textNode.appendChild(
							BX.create('DIV', {
								props: {className: 'bx-scu-list'},
								children: [BX.create('UL', {props: {className: 'bx-scu-itemlist'}, children: val})]
							})
						);
					}
				}
				else if (columnData)
				{
					textNode.appendChild(BX.create('SPAN', {html: BX.util.htmlspecialchars(columnData)}));
				}
			}

			return BX.create('DIV', {
				props: {className: 'bx-soa-item-td bx-soa-item-properties' + (toRight ? ' bx-text-right' : '')},
				children: [
					BX.create('DIV', {
						props: {className: 'bx-soa-item-td-title visible-xs visible-sm'},
						text: column.name
					}),
					textNode
				]
			});
		},

		createBasketItemHiddenColumn: function(column, allData)
		{
			console.log('createBasketItemHiddenColumn');
			if (!column || !allData)
				return;

			var data = allData.columns[column.id] ? allData.columns : allData.data,
				textNode = BX.create('TD', {props: {className: 'bx-soa-info-text'}}),
				logotype, img, i;

			if (column.id === 'PROPS')
			{
				var propsNodes = [], props = allData.data.PROPS;
				if (props && props.length)
				{
					for (i in props)
					{
						if (props.hasOwnProperty(i))
						{
							var name = props[i].NAME || '',
								value = props[i].VALUE || '';

							if (value.length == 0)
								continue;

							propsNodes.push(
								BX.create('TR', {
									props: {className: 'bx-soa-info-line'},
									children: [
										BX.create('TD', {props: {className: 'bx-soa-info-title'}, text: name + ':'}),
										BX.create('TD', {props: {className: 'bx-soa-info-text'}, html: BX.util.htmlspecialchars(value)})
									]
								})
							);
						}
					}

					return propsNodes;
				}
				else return;
			}
			else if (column.id === 'PRICE_FORMATED')
			{
				textNode.appendChild(BX.create('STRONG', {props: {className: 'bx-price'}, html: data.PRICE_FORMATED}));
				if (parseFloat(data.DISCOUNT_PRICE) > 0)
				{
					textNode.appendChild(BX.create('BR'));
					textNode.appendChild(BX.create('STRONG', {
						props: {className: 'bx-price-old'},
						html: data.BASE_PRICE_FORMATED
					}));
				}
			}
			else if (column.id === 'SUM')
				textNode.appendChild(BX.create('STRONG', {props: {className: 'bx-price all'}, text: data.SUM}));
			else if (column.id === 'DISCOUNT')
				textNode.appendChild(BX.create('STRONG', {props: {className: 'bx-price'}, text: data.DISCOUNT_PRICE_PERCENT_FORMATED}));
			else if (column.id === 'DETAIL_PICTURE' || column.id === 'PREVIEW_PICTURE')
			{
				logotype = this.getImageSources(allData.data, column.id),
				img = BX.create('IMG', {props: {src: logotype && logotype.src_1x || this.defaultBasketItemLogo}, style: {maxWidth: '50%'}});

				if (logotype && logotype.src_1x && logotype.src_orig)
				{
					BX.bind(img, 'click', BX.delegate(function(e){this.popupShow(e, logotype.src_orig);}, this));
				}

				textNode.appendChild(img);
			}
			else if (BX.util.in_array(column.id, ["QUANTITY", "WEIGHT_FORMATED", "DISCOUNT_PRICE_PERCENT_FORMATED"]))
			{
				textNode.appendChild(BX.create('SPAN', {html: data[column.id]}));
			}
			else if (column.id === 'PREVIEW_TEXT')
			{
				if (data['PREVIEW_TEXT_TYPE'] === 'html')
				{
					textNode.appendChild(BX.create('SPAN', {html: data['PREVIEW_TEXT'] || ''}));
				}
				else
				{
					textNode.appendChild(BX.create('SPAN', {text: data['PREVIEW_TEXT'] || ''}));
				}
			}
			else
			{
				var columnData = data[column.id], val = [];
				if (BX.type.isArray(columnData))
				{
					for (i in columnData)
					{
						if (columnData.hasOwnProperty(i))
						{
							if (columnData[i].type == 'image')
								val.push(this.getImageContainer(columnData[i].value, columnData[i].source));
							else if (columnData[i].type == 'linked')
							{
								textNode.appendChild(BX.create('SPAN', {html: columnData[i].value_format}));
								textNode.appendChild(BX.create('BR'));
							}
							else if (columnData[i].value)
							{
								textNode.appendChild(BX.create('SPAN', {html: columnData[i].value}));
								textNode.appendChild(BX.create('BR'));
							}
							else return;
						}
					}

					if (val.length)
					{
						textNode.appendChild(
							BX.create('DIV', {
								props: {className: 'bx-scu-list'},
								children: [BX.create('UL', {props: {className: 'bx-scu-itemlist'}, children: val})]
							})
						);
					}

				}
				else if (columnData)
				{
					textNode.appendChild(BX.create('SPAN', {html: BX.util.htmlspecialchars(columnData)}));
				}
				else
				{
					return;
				}
			}

			return BX.create('TR', {
				props: {className: 'bx-soa-info-line'},
				children: [
					BX.create('TD', {
						props: {className: 'bx-soa-info-title'},
						text: column.name + ':'
					}),
					textNode
				]
			});
		},

		popupShow: function(e, url, source)
		{
			console.log('popupShow');
			if (this.popup)
				this.popup.destroy();

			var that = this;
			this.popup = new BX.PopupWindow('bx-soa-image-popup', null, {
				lightShadow: true,
				offsetTop: 0,
				offsetLeft: 0,
				closeIcon: {top: '3px', right: '10px'},
				autoHide: true,
				bindOptions: {position: "bottom"},
				closeByEsc: true,
				zIndex: 100,
				events: {
					onPopupShow: function() {
						BX.create("IMG", {
							props: {src: source || url},
							events: {
								load: function() {
									var content = BX('bx-soa-image-popup-content');
									if (content)
									{
										var windowSize = BX.GetWindowInnerSize(),
											ratio = this.isMobile ? 0.5 : 0.9,
											contentHeight, contentWidth;

										BX.cleanNode(content);
										content.appendChild(this);

										contentHeight = content.offsetHeight;
										contentWidth = content.offsetWidth;

										if (contentHeight > windowSize.innerHeight * ratio)
										{
											content.style.height = windowSize.innerHeight * ratio + 'px';
											content.style.width = contentWidth * (windowSize.innerHeight * ratio / contentHeight) + 'px';
											contentHeight = content.offsetHeight;
											contentWidth = content.offsetWidth;
										}

										if (contentWidth > windowSize.innerWidth * ratio)
										{
											content.style.width = windowSize.innerWidth * ratio + 'px';
											content.style.height = contentHeight * (windowSize.innerWidth * ratio / contentWidth) + 'px';
										}

										content.style.height = content.offsetHeight + 'px';
										content.style.width = content.offsetWidth + 'px';

										that.popup.adjustPosition();
									}
								}
							}
						});
					},
					onPopupClose: function() {
						this.destroy();
					}
				},
				content: BX.create('DIV', {
					props: {id: 'bx-soa-image-popup-content'},
					children: [BX.create('IMG', {props: {src: this.templateFolder + "/images/loader.gif"}})]
				})
			});
			this.popup.show();
		},

		getImageContainer: function(link, source)
		{
			console.log('getImageContainer');
			return BX.create('LI', {
				props: {className: 'bx-img-item'},
				children: [
					BX.create('DIV', {
						props: {className: 'bx-scu-itemColorBlock'},
						children: [
							BX.create('DIV', {
								props: {className: 'bx-img-itemColor'},
								style: {backgroundImage: 'url("' + link + '")'}
							})
						],
						events: {
							click: BX.delegate(function(e){this.popupShow(e, link, source)}, this)
						}
					})
				]
			});
		},

		editCoupons: function(basketItemsNode)
		{
			console.log('editCoupons');
			var couponsList = this.getCouponsList(true),
				couponsLabel = this.getCouponsLabel(true),
				couponsBlock = BX.create('DIV', {
					props: {className: 'bx-soa-coupon-block'},
					children: [
						BX.create('DIV', {
							props: {className: 'bx-soa-coupon-input'},
							children: [
								BX.create('INPUT', {
									props: {
										className: 'form-control bx-ios-fix',
										type: 'text'
									},
									events: {
										change: BX.delegate(function(event){
											var newCoupon = BX.getEventTarget(event);
											if (newCoupon && newCoupon.value)
											{
												this.sendRequest('enterCoupon', newCoupon.value);
												newCoupon.value = '';
											}
										}, this)
									}
								})
							]
						}),
						BX.create('SPAN', {props: {className: 'bx-soa-coupon-item'}, children: couponsList})
					]
				});

			basketItemsNode.appendChild(
				BX.create('DIV', {
					props: {className: 'bx-soa-coupon'},
					children: [
						couponsLabel,
						couponsBlock
					]
				})
			);
		},

		editCouponsFade: function(basketItemsNode)
		{
			console.log('editCouponsFade');
			if (this.result.COUPON_LIST.length < 1)
				return;

			var couponsList = this.getCouponsList(false),
				couponsLabel, couponsBlock;

			if (couponsList.length)
			{
				couponsLabel = this.getCouponsLabel(false);
				couponsBlock = BX.create('DIV', {
					props: {className: 'bx-soa-coupon-block'},
					children: [
						BX.create('DIV', {
							props: {className: 'bx-soa-coupon-list'},
							children: [
								BX.create('DIV', {
									props: {className: 'bx-soa-coupon-item'},
									children: [couponsLabel].concat(couponsList)
								})
							]
						})
					]
				});

				basketItemsNode.appendChild(
					BX.create('DIV', {
						props: {className: 'bx-soa-coupon bx-soa-coupon-item-fixed'},
						children: [couponsBlock]
					})
				);
			}
		},

		getCouponsList: function(active)
		{
			console.log('getCouponsList');
			var couponsList = [], i;

			for (i = 0; i < this.result.COUPON_LIST.length; i++)
			{
				if (active || (!active && this.result.COUPON_LIST[i].JS_STATUS == 'APPLIED'))
				{
					couponsList.push(this.getCouponNode({
						text: this.result.COUPON_LIST[i].COUPON,
						desc: this.result.COUPON_LIST[i].JS_CHECK_CODE,
						status: this.result.COUPON_LIST[i].JS_STATUS
					}, active));
				}
			}

			return couponsList;
		},

		getCouponNode: function(coupon, active)
		{
			console.log('getCouponNode');
			var couponName = BX.util.htmlspecialchars(coupon.text) || '',
				couponDesc = coupon.desc && coupon.desc.length
					? coupon.desc.charAt(0).toUpperCase() + coupon.desc.slice(1)
					: BX.message('SOA_NOT_FOUND'),
				couponStatus = coupon.status || 'BAD',
				couponItem, tooltip;

			switch (couponStatus.toUpperCase())
			{
				case 'ENTERED': couponItem = 'used'; tooltip = 'warning'; break;
				case 'BAD': couponItem = tooltip = 'danger'; break;
				default: couponItem = tooltip  = 'success';
			}

			return BX.create('STRONG', {
				attrs: {
					'data-coupon': couponName,
					className: 'bx-soa-coupon-item-' + couponItem
				},
				children: active ? [
					couponName || '',
					BX.create('SPAN', {
						props: {className: 'bx-soa-coupon-remove'},
						events: {
							click: BX.delegate(function(e){
								var target = e.target || e.srcElement,
									coupon = BX.findParent(target, {tagName: 'STRONG'});

								if (coupon && coupon.getAttribute('data-coupon'))
								{
									this.sendRequest('removeCoupon', coupon.getAttribute('data-coupon'))
								}
							}, this)
						}
					}),
					BX.create('SPAN', {
						props: {
							className: 'bx-soa-tooltip bx-soa-tooltip-coupon bx-soa-tooltip-' + tooltip + ' tooltip top'
						},
						children: [
							BX.create('SPAN', {props: {className: 'tooltip-arrow'}}),
							BX.create('SPAN', {props: {className: 'tooltip-inner'}, text: couponDesc})
						]
					})
				] : [couponName]
			});
		},

		getCouponsLabel: function(active)
		{
			console.log('getCouponsLabel');
			return BX.create('DIV', {
				props: {className: 'bx-soa-coupon-label'},
				children: active
					? [BX.create('LABEL', {html: this.params.MESS_USE_COUPON + ':'})]
					: [this.params.MESS_COUPON + ':']
			});
		},

		addCoupon: function(coupon)
		{
			console.log('addCoupon');
			var couponListNodes = this.orderBlockNode.querySelectorAll('.bx-soa-coupon:not(.bx-soa-coupon-item-fixed) .bx-soa-coupon-item');

			for (var i = 0; i < couponListNodes.length; i++)
			{
				if (couponListNodes[i].querySelector('[data-coupon="' + BX.util.htmlspecialchars(coupon) + '"]'))
					break;

				couponListNodes[i].appendChild(this.getCouponNode({text: coupon}, true, 'bx-soa-coupon-item-danger'));
			}
		},

		removeCoupon: function(coupon)
		{
			console.log('removeCoupon');
			var couponNodes = this.orderBlockNode.querySelectorAll('[data-coupon="' + BX.util.htmlspecialchars(coupon) + '"]'), i;

			for (i in couponNodes)
			{
				if (couponNodes.hasOwnProperty(i))
				{
					BX.remove(couponNodes[i]);
				}
			}
		},

		editRegionBlock: function()
		{
			console.log('editRegionBlock');
			if (!this.regionBlockNode || !this.regionHiddenBlockNode || !this.result.PERSON_TYPE)
				return;
			var node = this.regionBlockNode.querySelector('.calculate__input.calculate__input--select');

			this.getPersonTypeControl(node);

			this.getProfilesControl(node);
		},

		editFadeRegionBlock: function()
		{
			console.log('editFadeRegionBlock');
			var regionContent = this.regionBlockNode.querySelector('.bx-soa-section-content'), newContent;
			if (this.initialized.region)
			{
				this.regionHiddenBlockNode.appendChild(regionContent);
			}
			else
			{
				this.editActiveRegionBlock(false);
				BX.remove(BX.lastChild(this.regionBlockNode));
			}

			newContent = this.getNewContainer(true);
			this.regionBlockNode.appendChild(newContent);
			this.editFadeRegionContent(newContent);
		},

		editFadeRegionContent: function(node)
		{
			console.log('editFadeRegionContent');
			if (!node || !this.locationsInitialized)
				return;

			var selectedPersonType = this.getSelectedPersonType(),
				errorNode = this.regionHiddenBlockNode.querySelector('.alert.alert-danger'),
				addedHtml = '', props = [], locationProperty,
				input, zipValue = '', zipProperty,
				fadeParamName, i, k, locationString, validRegionErrors;

			BX.cleanNode(node);

			if (errorNode)
				node.appendChild(errorNode.cloneNode(true));

			if (selectedPersonType && selectedPersonType.NAME && this.result.PERSON_TYPE.length > 1)
			{
				addedHtml += '<strong>' + this.params.MESS_PERSON_TYPE + ':</strong> '
					+ BX.util.htmlspecialchars(selectedPersonType.NAME) + '<br>';
			}

			if (selectedPersonType)
			{
				fadeParamName = 'PROPS_FADE_LIST_' + selectedPersonType.ID;
				props = this.params[fadeParamName] || [];
			}

			for (i in this.result.ORDER_PROP.properties)
			{
				if (this.result.ORDER_PROP.properties.hasOwnProperty(i))
				{
					if (this.result.ORDER_PROP.properties[i].IS_LOCATION == 'Y'
						&& this.result.ORDER_PROP.properties[i].ID == this.deliveryLocationInfo.loc)
					{
						locationProperty = this.result.ORDER_PROP.properties[i];
					}
					else if (this.result.ORDER_PROP.properties[i].IS_ZIP == 'Y'
						&& this.result.ORDER_PROP.properties[i].ID == this.deliveryLocationInfo.zip)
					{
						zipProperty = this.result.ORDER_PROP.properties[i];
						for (k = 0; k < props.length; k++)
						{
							if (props[k] == zipProperty.ID)
							{
								input = BX('zipProperty');
								zipValue = input && input.value && input.value.length ? input.value : BX.message('SOA_NOT_SPECIFIED');
								break;
							}
						}
					}
				}
			}

			locationString = this.getLocationString(this.regionHiddenBlockNode);
			if (locationProperty && locationString.length)
				addedHtml += '<strong>' + BX.util.htmlspecialchars(locationProperty.NAME) + ':</strong> '
					+ BX.util.htmlspecialchars(locationString) + '<br>';

			if (zipProperty && zipValue.length)
				addedHtml += '<strong>' + BX.util.htmlspecialchars(zipProperty.NAME) + ':</strong> '
					+ BX.util.htmlspecialchars(zipValue);

			node.innerHTML += addedHtml;

			if (this.regionBlockNode.getAttribute('data-visited') == 'true')
			{
				validRegionErrors = this.isValidRegionBlock();

				if (validRegionErrors.length)
				{
					BX.addClass(this.regionBlockNode, 'bx-step-error');
					this.showError(this.regionBlockNode, validRegionErrors);
				}
				else
					BX.removeClass(this.regionBlockNode, 'bx-step-error');
			}

			BX.bind(node.querySelector('.alert.alert-danger'), 'click', BX.proxy(this.showByClick, this));
			BX.bind(node.querySelector('.alert.alert-warning'), 'click', BX.proxy(this.showByClick, this));
		},

		getSelectedPersonType: function()
		{
			console.log('getSelectedPersonType');
			var personTypeInput, currentPersonType, personTypeId, i,
				personTypeLength = this.result.PERSON_TYPE.length;

			if (personTypeLength == 1)
			{
				personTypeInput = this.regionBlockNode.querySelector('input[type=hidden][name=PERSON_TYPE]');
				if (!personTypeInput)
					personTypeInput = this.regionHiddenBlockNode.querySelector('input[type=hidden][name=PERSON_TYPE]');
			}
			else if (personTypeLength == 2)
			{
				personTypeInput = this.regionBlockNode.querySelector('input[type=radio][name=PERSON_TYPE]:checked');
				if (!personTypeInput)
					personTypeInput = this.regionHiddenBlockNode.querySelector('input[type=radio][name=PERSON_TYPE]:checked');
			}
			else
			{
				personTypeInput = this.regionBlockNode.querySelector('select[name=PERSON_TYPE] > option:checked');
				if (!personTypeInput)
					personTypeInput = this.regionHiddenBlockNode.querySelector('select[name=PERSON_TYPE] > option:checked');
			}

			if (personTypeInput)
			{
				personTypeId = personTypeInput.value;

				for (i in this.result.PERSON_TYPE)
				{
					if (this.result.PERSON_TYPE[i].ID == personTypeId)
					{
						currentPersonType = this.result.PERSON_TYPE[i];
						break;
					}
				}
			}
			return currentPersonType;
		},

		getDeliveryLocationInput: function(node)
		{
			console.log('getDeliveryLocationInput');
			var currentProperty, locationId, altId, location, k, altProperty,
				labelHtml, currentLocation, insertedLoc,
				labelTextHtml, label, input, altNode;

			for (k in this.result.ORDER_PROP.properties)
			{
				if (this.result.ORDER_PROP.properties.hasOwnProperty(k))
				{
					currentProperty = this.result.ORDER_PROP.properties[k];
					if (currentProperty.IS_LOCATION == 'Y')
					{
						locationId = currentProperty.ID;
						altId = parseInt(currentProperty.INPUT_FIELD_LOCATION);
						break;
					}
				}
			}

			location = this.locations[locationId];
			if (location && location[0] && location[0].output)
			{
				this.regionBlockNotEmpty = true;

				labelHtml = '<label class="bx-soa-custom-label" for="soa-property-' + parseInt(locationId) + '">'
					+ (currentProperty.REQUIRED == 'Y' ? '<span class="bx-authform-starrequired">*</span> ' : '')
					+ BX.util.htmlspecialchars(currentProperty.NAME)
					+ (currentProperty.DESCRIPTION.length ? ' <small>(' + BX.util.htmlspecialchars(currentProperty.DESCRIPTION) + ')</small>' : '')
					+ '</label>';

				currentLocation = location[0].output;
				insertedLoc = BX.create('DIV', {
					attrs: {'data-property-id-row': locationId},
					props: {className: 'form-group bx-soa-location-input-container'},
					style: {visibility: 'hidden'},
					html:  labelHtml + currentLocation.HTML
				});
				node.appendChild(insertedLoc);
				node.appendChild(BX.create('INPUT', {
					props: {
						type: 'hidden',
						name: 'RECENT_DELIVERY_VALUE',
						value: location[0].lastValue
					}
				}));

				for (k in currentLocation.SCRIPT)
					if (currentLocation.SCRIPT.hasOwnProperty(k))
						BX.evalGlobal(currentLocation.SCRIPT[k].JS);
			}

			if (location && location[0] && location[0].showAlt && altId > 0)
			{
				for (k in this.result.ORDER_PROP.properties)
				{
					if (parseInt(this.result.ORDER_PROP.properties[k].ID) == altId)
					{
						altProperty = this.result.ORDER_PROP.properties[k];
						break;
					}
				}
			}

			if (altProperty)
			{
				altNode = BX.create('DIV', {
					attrs: {'data-property-id-row': altProperty.ID},
					props: {className: "form-group bx-soa-location-input-container"}
				});

				labelTextHtml = altProperty.REQUIRED == 'Y' ? '<span class="bx-authform-starrequired">*</span> ' : '';
				labelTextHtml += BX.util.htmlspecialchars(altProperty.NAME);

				label = BX.create('LABEL', {
					attrs: {for: 'altProperty'},
					props: {className: 'bx-soa-custom-label'},
					html: labelTextHtml
				});

				input = BX.create('INPUT', {
					props: {
						id: 'altProperty',
						type: 'text',
						placeholder: altProperty.DESCRIPTION,
						autocomplete: 'city',
						className: 'form-control bx-soa-customer-input bx-ios-fix',
						name: 'ORDER_PROP_' + altProperty.ID,
						value: altProperty.VALUE
					}
				});

				altNode.appendChild(label);
				altNode.appendChild(input);
				node.appendChild(altNode);

				this.bindValidation(altProperty.ID, altNode);
			}

			this.getZipLocationInput(node);

			if (location && location[0])
			{
				node.appendChild(
					BX.create('DIV', {
						props: {className: 'bx-soa-reference'},
						html: this.params.MESS_REGION_REFERENCE
					})
				);
			}
		},

		getLocationString: function(node)
		{
			console.log('getLocationString');
			if (!node)
				return '';

			var locationInputNode = node.querySelector('.bx-ui-sls-route'),
				locationString = '',
				locationSteps, i, altLoc;

			if (locationInputNode && locationInputNode.value && locationInputNode.value.length)
				locationString = locationInputNode.value;
			else
			{
				locationSteps = node.querySelectorAll('.bx-ui-combobox-fake.bx-combobox-fake-as-input');
				for (i = locationSteps.length; i--;)
				{
					if (locationSteps[i].innerHTML.indexOf('...') >= 0)
						continue;

					if (locationSteps[i].innerHTML.indexOf('---') >= 0)
					{
						altLoc = BX('altProperty');
						if (altLoc && altLoc.value.length)
							locationString += altLoc.value;

						continue;
					}

					if (locationString.length)
						locationString += ', ';

					locationString += locationSteps[i].innerHTML;
				}

				if (locationString.length == 0)
					locationString = BX.message('SOA_NOT_SPECIFIED');
			}

			return locationString;
		},

		getZipLocationInput: function(node)
		{
			console.log('getZipLocationInput');
			var zipProperty, i, propsItemNode, labelTextHtml, label, input;

			for (i in this.result.ORDER_PROP.properties)
			{
				if (this.result.ORDER_PROP.properties.hasOwnProperty(i) && this.result.ORDER_PROP.properties[i].IS_ZIP == 'Y')
				{
					zipProperty = this.result.ORDER_PROP.properties[i];
					break;
				}
			}

			if (zipProperty)
			{
				this.regionBlockNotEmpty = true;

				propsItemNode = BX.create('DIV', {props: {className: "form-group bx-soa-location-input-container"}});
				propsItemNode.setAttribute('data-property-id-row', zipProperty.ID);

				labelTextHtml = zipProperty.REQUIRED == 'Y' ? '<span class="bx-authform-starrequired">*</span> ' : '';
				labelTextHtml += BX.util.htmlspecialchars(zipProperty.NAME);

				label = BX.create('LABEL', {
					attrs: {'for': 'zipProperty'},
					props: {className: 'bx-soa-custom-label'},
					html: labelTextHtml
				});
				input = BX.create('INPUT', {
					props: {
						id: 'zipProperty',
						type: 'text',
						placeholder: zipProperty.DESCRIPTION,
						autocomplete: 'zip',
						className: 'form-control bx-soa-customer-input bx-ios-fix',
						name: 'ORDER_PROP_' + zipProperty.ID,
						value: zipProperty.VALUE
					}
				});

				propsItemNode.appendChild(label);
				propsItemNode.appendChild(input);
				node.appendChild(propsItemNode);
				node.appendChild(
					BX.create('input', {
						props: {
							id: 'ZIP_PROPERTY_CHANGED',
							name: 'ZIP_PROPERTY_CHANGED',
							type: 'hidden',
							value: this.result.ZIP_PROPERTY_CHANGED || 'N'
						}
					})
				);

				this.bindValidation(zipProperty.ID, propsItemNode);
			}
		},

		getPersonTypeSortedArray: function(objPersonType)
		{
			console.log('getPersonTypeSortedArray');
			var personTypes = [], k;

			for (k in objPersonType)
			{
				if (objPersonType.hasOwnProperty(k))
				{
					personTypes.push(objPersonType[k]);
				}
			}

			return personTypes.sort(function(a, b){return parseInt(a.SORT) - parseInt(b.SORT)});
		},

		getPersonTypeControl: function(node)
		{
			console.log('getPersonTypeControl');
			if (!this.result.PERSON_TYPE)
				return;

			this.result.PERSON_TYPE = this.getPersonTypeSortedArray(this.result.PERSON_TYPE);

			var personTypesCount = this.result.PERSON_TYPE.length,
				currentType, oldPersonTypeId, i,
				input, options = [], label, delimiter = false;
			for (i in this.result.PERSON_TYPE)
			{
				if (this.result.PERSON_TYPE.hasOwnProperty(i))
				{
					currentType = this.result.PERSON_TYPE[i];
					node.appendChild(BX.create('OPTION', {
						props: {
							value: currentType.ID
						},
						text: currentType.NAME,
						attrs: {
							selected: currentType.CHECKED == 'Y'
						}
					}));

					if (currentType.CHECKED == 'Y')
						oldPersonTypeId = currentType.ID;
				}

			}

			this.regionBlockNotEmpty = true;

			if (oldPersonTypeId)
			{
				this.regionBlockNode.appendChild(
					BX.create('INPUT', {
						props: {
							type: 'hidden',
							name: 'PERSON_TYPE_OLD',
							value: oldPersonTypeId

						}
					})
				);
			}
		},

		getProfilesControl: function(node)
		{
			console.log('getProfilesControl');
			var profilesLength = BX.util.object_keys(this.result.USER_PROFILES).length,
				i, label, options = [],
				profileChangeInput, input;
			if (profilesLength)
			{
				if (
					this.params.ALLOW_USER_PROFILES === 'Y'
					&& (profilesLength > 1 || this.params.ALLOW_NEW_PROFILE === 'Y')
				)
				{
					this.regionBlockNotEmpty = true;

					label = BX.create('LABEL', {props: {className: 'bx-soa-custom-label'}, html: this.params.MESS_SELECT_PROFILE});

					for (i in this.result.USER_PROFILES)
					{
						if (this.result.USER_PROFILES.hasOwnProperty(i))
						{
							options.unshift(
								BX.create('OPTION', {
									props: {
										value: this.result.USER_PROFILES[i].ID,
										selected: this.result.USER_PROFILES[i].CHECKED === 'Y'
									},
									html: this.result.USER_PROFILES[i].NAME
								})
							);
						}
					}

					if (this.params.ALLOW_NEW_PROFILE === 'Y')
					{
						options.unshift(BX.create('OPTION', {props: {value: 0}, text: BX.message('SOA_PROP_NEW_PROFILE')}));
					}

					profileChangeInput = BX.create('INPUT', {
						props: {
							type: 'hidden',
							value: 'N',
							id: 'profile_change',
							name: 'profile_change'
						}
					});
					input = BX.create('SELECT', {
						props: {className: 'form-control', name: 'PROFILE_ID'},
						children: options,
						events:{
							change: BX.delegate(function(){
								BX('profile_change').value = 'Y';
								this.sendRequest();
							}, this)
						}
					});

					node.appendChild(
						BX.create('DIV', {
							props: {className: 'form-group bx-soa-location-input-container'},
							children: [label, profileChangeInput, input]
						})
					);
				}
				else
				{
					for (i in this.result.USER_PROFILES)
					{
						if (
							this.result.USER_PROFILES.hasOwnProperty(i)
							&& this.result.USER_PROFILES[i].CHECKED === 'Y'
						)
						{
							node.appendChild(
								BX.create('INPUT', {
									props: {
										name: 'PROFILE_ID',
										type: 'hidden',
										value: this.result.USER_PROFILES[i].ID}
								})
							);
						}
					}
				}
			}
		},

		editPaySystemBlock: function(active)
		{
			console.log('editPaySystemBlock');
			if (!this.paySystemBlockNode || !this.paySystemHiddenBlockNode || !this.result.PAY_SYSTEM)
				return;

			this.editActivePaySystemBlock(true);
		},

		editActivePaySystemBlock: function(activeNodeMode)
		{
			console.log('editActivePaySystemBlock');
			var node = this.paySystemBlockNode,
				paySystemContent, paySystemNode;

			
			paySystemContent = node.querySelector('.section-content');
			
			this.editPaySystemItems(paySystemContent);
			
		},

		editFadePaySystemBlock: function()
		{
			console.log('editFadePaySystemBlock');
			var paySystemContent = this.paySystemBlockNode.querySelector('.bx-soa-section-content'), newContent;

			if (this.initialized.paySystem)
			{
				this.paySystemHiddenBlockNode.appendChild(paySystemContent);
			}
			else
			{
				this.editActivePaySystemBlock(false);
				BX.remove(BX.lastChild(this.paySystemBlockNode));
			}

			newContent = this.getNewContainer(true);
			this.paySystemBlockNode.appendChild(newContent);

			this.editFadePaySystemContent(newContent);

			if (this.params.SHOW_COUPONS_PAY_SYSTEM == 'Y')
				this.editCouponsFade(newContent);
		},

		editPaySystemItems: function(paySystemNode)
		{
			console.log('editPaySystemItems');
			if (!this.result.PAY_SYSTEM || this.result.PAY_SYSTEM.length <= 0)
				return;

			var paySystemItemNode, i;
			for (i = 0; i < this.paySystemPagination.currentPage.length; i++)
			{
				paySystemItemNode = this.createPaySystemItem(this.paySystemPagination.currentPage[i]);
				paySystemNode.appendChild(paySystemItemNode);
			}
		},

		createPaySystemItem: function(item)
		{
			console.log('createPaySystemItem');
			var checked = item.CHECKED == 'Y',
				logotype, logoNode, radioButton,
				paySystemId = parseInt(item.ID),
				title, label, itemNode;
			console.log(item)
			logoNode = BX.create('SPAN');
			logoNode.innerHTML = item["NAME"];
			radioButton = BX.create("INPUT", {
						props: {
							id: 'ID_PAY_SYSTEM_ID_' + paySystemId,
							name: 'PAY_SYSTEM_ID',
							type: 'radio',
							value: paySystemId,
							checked: checked
						}
					});

			itemNode = BX.create('LABEL', {
				props: {className: 'calculate__order-pay-option order-pay'},
				children: [radioButton, logoNode],
				/*events: {
					click: BX.proxy(this.selectPaySystem, this)
				}*/
			});
			if(item.CHECKED == 'Y'){
				BX.addClass(itemNode, 'active');
			}

			return itemNode;
		},

		editPaySystemInfo: function(paySystemNode)
		{
			console.log('editPaySystemInfo');
			if (!this.result.PAY_SYSTEM || (this.result.PAY_SYSTEM.length == 0 && this.result.PAY_FROM_ACCOUNT != 'Y'))
				return;

			var paySystemInfoContainer = BX.create('DIV', {
					props: {
						className: (this.result.PAY_SYSTEM.length == 0 ? 'col-sm-12' : 'col-sm-5') + ' bx-soa-pp-desc-container'
					}
				}),
				innerPs, extPs, delimiter, currentPaySystem,
				logotype, logoNode, subTitle, label, title, price;

			BX.cleanNode(paySystemInfoContainer);

			if (this.result.PAY_FROM_ACCOUNT == 'Y')
				innerPs = this.getInnerPaySystem(paySystemInfoContainer);

			currentPaySystem = this.getSelectedPaySystem();
			if (currentPaySystem)
			{
				logoNode = BX.create('DIV', {props: {className: 'bx-soa-pp-company-image'}});
				logotype = this.getImageSources(currentPaySystem, 'PSA_LOGOTIP');
				if (logotype && logotype.src_2x)
				{
					logoNode.setAttribute('style',
						'background-image: url("' + logotype.src_1x + '");' +
						'background-image: -webkit-image-set(url("' + logotype.src_1x + '") 1x, url("' + logotype.src_2x + '") 2x)'
					);
				}
				else
				{
					logotype = logotype && logotype.src_1x || this.defaultPaySystemLogo;
					logoNode.setAttribute('style', 'background-image: url("' + logotype + '");');
				}

				if (this.params.SHOW_PAY_SYSTEM_INFO_NAME == 'Y')
				{
					subTitle = BX.create('DIV', {
						props: {className: 'bx-soa-pp-company-subTitle'},
						text: currentPaySystem.NAME
					});
				}

				label = BX.create('DIV', {
					props: {className: 'bx-soa-pp-company-logo'},
					children: [
						BX.create('DIV', {
							props: {className: 'bx-soa-pp-company-graf-container'},
							children: [logoNode]
						})
					]
				});

				title = BX.create('DIV', {
					props: {className: 'bx-soa-pp-company-block'},
					children: [BX.create('DIV', {props: {className: 'bx-soa-pp-company-desc'}, html: currentPaySystem.DESCRIPTION})]
				});

				if (currentPaySystem.PRICE && parseFloat(currentPaySystem.PRICE) > 0)
				{
					price = BX.create('UL', {
						props: {className: 'bx-soa-pp-list'},
						children: [
							BX.create('LI', {
								children: [
									BX.create('DIV', {props: {className: 'bx-soa-pp-list-termin'}, html: this.params.MESS_PRICE + ':'}),
									BX.create('DIV', {props: {className: 'bx-soa-pp-list-description'}, text: '~' + currentPaySystem.PRICE_FORMATTED})
								]
							})
						]
					});
				}

				extPs = BX.create('DIV', {children: [subTitle, label, title, price]});
			}

			if (innerPs && extPs)
				delimiter = BX.create('HR', {props: {className: 'bxe-light'}});

			paySystemInfoContainer.appendChild(
				BX.create('DIV', {
					props: {className: 'bx-soa-pp-company'},
					children: [innerPs, delimiter, extPs]
				})
			);
			paySystemNode.appendChild(paySystemInfoContainer);
		},

		getInnerPaySystem: function()
		{
			console.log('getInnerPaySystem');
			if (!this.result.CURRENT_BUDGET_FORMATED || !this.result.PAY_CURRENT_ACCOUNT || !this.result.INNER_PAY_SYSTEM)
				return;

			var accountOnly = this.params.ONLY_FULL_PAY_FROM_ACCOUNT && (this.params.ONLY_FULL_PAY_FROM_ACCOUNT == 'Y'),
				isSelected = this.result.PAY_CURRENT_ACCOUNT && (this.result.PAY_CURRENT_ACCOUNT == 'Y'),
				paySystem = this.result.INNER_PAY_SYSTEM,
				logotype, logoNode,subTitle, label, title, hiddenInput, htmlString, innerPsDesc;

			if (this.params.SHOW_PAY_SYSTEM_INFO_NAME == 'Y')
			{
				subTitle = BX.create('DIV', {
					props: {className: 'bx-soa-pp-company-subTitle'},
					text: paySystem.NAME
				});
			}

			logoNode = BX.create('DIV', {props: {className: 'bx-soa-pp-company-image'}});
			logotype = this.getImageSources(paySystem, 'LOGOTIP');
			if (logotype && logotype.src_2x)
			{
				logoNode.setAttribute('style',
					'background-image: url("' + logotype.src_1x + '");' +
					'background-image: -webkit-image-set(url("' + logotype.src_1x + '") 1x, url("' + logotype.src_2x + '") 2x)'
				);
			}
			else
			{
				logotype = logotype && logotype.src_1x || this.defaultPaySystemLogo;
				logoNode.setAttribute('style', 'background-image: url("' + logotype + '");');
			}

			label = BX.create('DIV', {
				props: {className: 'bx-soa-pp-company-logo'},
				children: [
					BX.create('DIV', {
						props: {className: 'bx-soa-pp-company-graf-container'},
						children: [
							BX.create('INPUT', {
								props: {
									type: 'checkbox',
									className: 'bx-soa-pp-company-checkbox',
									name: 'PAY_CURRENT_ACCOUNT',
									value: 'Y',
									checked: isSelected
								}
							}),
							logoNode
						],
						events: {
							click: BX.proxy(this.selectPaySystem, this)
						}
					})
				]
			});

			if (paySystem.DESCRIPTION && paySystem.DESCRIPTION.length)
			{
				title = BX.create('DIV', {
					props: {className: 'bx-soa-pp-company-block'},
					children: [BX.create('DIV', {props: {className: 'bx-soa-pp-company-desc'}, html: paySystem.DESCRIPTION})]
				});
			}

			hiddenInput = BX.create('INPUT', {
				props: {
					type: 'hidden',
					name: 'PAY_CURRENT_ACCOUNT',
					value: 'N'
				}
			});

			htmlString = this.params.MESS_INNER_PS_BALANCE + ' <b class="wsnw">' + this.result.CURRENT_BUDGET_FORMATED
				+ '</b><br>' + (accountOnly ? BX.message('SOA_PAY_ACCOUNT3') : '');
			innerPsDesc = BX.create('DIV', {props: {className: 'bx-soa-pp-company-desc'}, html: htmlString});

			return BX.create('DIV', {
				props: {className: 'bx-soa-pp-inner-ps' + (isSelected ? ' bx-selected' : '')},
				children: [hiddenInput, subTitle, label, title, innerPsDesc]
			});
		},

		editFadePaySystemContent: function(node)
		{
			console.log('editFadePaySystemContent');
			var selectedPaySystem = this.getSelectedPaySystem(),
				errorNode = this.paySystemHiddenBlockNode.querySelector('div.alert.alert-danger'),
				warningNode = this.paySystemHiddenBlockNode.querySelector('div.alert.alert-warning.alert-show'),
				addedHtml = '', logotype, imgSrc;

			if (errorNode)
				node.appendChild(errorNode.cloneNode(true));
			else
				this.getErrorContainer(node);

			if (warningNode && warningNode.innerHTML)
				node.appendChild(warningNode.cloneNode(true));

			if (this.isSelectedInnerPayment())
			{
				logotype = this.getImageSources(this.result.INNER_PAY_SYSTEM, 'LOGOTIP');
				imgSrc = logotype && logotype.src_1x || this.defaultPaySystemLogo;

				addedHtml += '<div class="bx-soa-pp-company-selected">';
				addedHtml += '<img src="' + imgSrc + '" style="height:18px;" alt="">';
				addedHtml += '<strong>' + this.result.INNER_PAY_SYSTEM.NAME + '</strong><br>';
				addedHtml += '</div>';
			}

			if (selectedPaySystem && selectedPaySystem.NAME)
			{
				logotype = this.getImageSources(selectedPaySystem, 'PSA_LOGOTIP');
				imgSrc = logotype && logotype.src_1x || this.defaultPaySystemLogo;

				addedHtml += '<div class="bx-soa-pp-company-selected">';
				addedHtml += '<img src="' + imgSrc + '" style="height:18px;" alt="">';
				addedHtml += '<strong>' + BX.util.htmlspecialchars(selectedPaySystem.NAME) + '</strong>';
				addedHtml += '</div>';
			}

			if (!addedHtml.length)
				addedHtml = '<strong>' + BX.message('SOA_PS_SELECT_ERROR') + '</strong>';

			node.innerHTML += addedHtml;

			node.appendChild(BX.create('DIV', {style: {clear: 'both'}}));
			BX.bind(node.querySelector('.alert.alert-danger'), 'click', BX.proxy(this.showByClick, this));
			BX.bind(node.querySelector('.alert.alert-warning'), 'click', BX.proxy(this.showByClick, this));
		},

		getSelectedPaySystem: function()
		{
			console.log('getSelectedPaySystem');
			var paySystemCheckbox = this.paySystemBlockNode.querySelector('input[type=checkbox][name=PAY_SYSTEM_ID]:checked'),
				currentPaySystem = null, paySystemId, i;

			if (!paySystemCheckbox)
				paySystemCheckbox = this.paySystemHiddenBlockNode.querySelector('input[type=checkbox][name=PAY_SYSTEM_ID]:checked');

			if (!paySystemCheckbox)
				paySystemCheckbox = this.paySystemHiddenBlockNode.querySelector('input[type=hidden][name=PAY_SYSTEM_ID]');

			if (paySystemCheckbox)
			{
				paySystemId = paySystemCheckbox.value;

				for (i = 0; i < this.result.PAY_SYSTEM.length; i++)
				{
					if (this.result.PAY_SYSTEM[i].ID == paySystemId)
					{
						currentPaySystem = this.result.PAY_SYSTEM[i];
						break;
					}
				}
			}

			return currentPaySystem;
		},

		isSelectedInnerPayment: function()
		{
			console.log('isSelectedInnerPayment');
			var innerPaySystemCheckbox = this.paySystemBlockNode.querySelector('input[type=checkbox][name=PAY_CURRENT_ACCOUNT]');

			if (!innerPaySystemCheckbox)
				innerPaySystemCheckbox = this.paySystemHiddenBlockNode.querySelector('input[type=checkbox][name=PAY_CURRENT_ACCOUNT]');

			return innerPaySystemCheckbox && innerPaySystemCheckbox.checked;
		},

		selectPaySystem: function(event)
		{
			console.log('selectPaySystem');
			if (!this.orderBlockNode || !event)
				return;

			var target = event.target || event.srcElement,
				innerPaySystemSection = this.paySystemBlockNode.querySelector('div.bx-soa-pp-inner-ps'),
				innerPaySystemCheckbox = this.paySystemBlockNode.querySelector('input[type=checkbox][name=PAY_CURRENT_ACCOUNT]'),
				fullPayFromInnerPaySystem = this.result.TOTAL && parseFloat(this.result.TOTAL.ORDER_TOTAL_LEFT_TO_PAY) === 0;

			var innerPsAction = BX.hasClass(target, 'bx-soa-pp-inner-ps') ? target : BX.findParent(target, {className: 'bx-soa-pp-inner-ps'}),
				actionSection = BX.hasClass(target, 'bx-soa-pp-company') ? target : BX.findParent(target, {className: 'bx-soa-pp-company'}),
				actionInput, selectedSection;

			if (innerPsAction)
			{
				if (target.nodeName == 'INPUT')
					innerPaySystemCheckbox.checked = !innerPaySystemCheckbox.checked;

				if (innerPaySystemCheckbox.checked)
				{
					BX.removeClass(innerPaySystemSection, 'bx-selected');
					innerPaySystemCheckbox.checked = false;
				}
				else
				{
					BX.addClass(innerPaySystemSection, 'bx-selected');
					innerPaySystemCheckbox.checked = true;
				}
			}
			else if (actionSection)
			{
				if (BX.hasClass(actionSection, 'bx-selected'))
					return BX.PreventDefault(event);

				if (innerPaySystemCheckbox && innerPaySystemCheckbox.checked && fullPayFromInnerPaySystem)
				{
					BX.addClass(actionSection, 'bx-selected');
					actionInput = actionSection.querySelector('input[type=checkbox]');
					actionInput.checked = true;
					BX.removeClass(innerPaySystemSection, 'bx-selected');
					innerPaySystemCheckbox.checked = false;
				}
				else
				{
					selectedSection = this.paySystemBlockNode.querySelector('.bx-soa-pp-company.bx-selected');
					BX.addClass(actionSection, 'bx-selected');
					actionInput = actionSection.querySelector('input[type=checkbox]');
					actionInput.checked = true;

					if (selectedSection)
					{
						BX.removeClass(selectedSection, 'bx-selected');
						selectedSection.querySelector('input[type=checkbox]').checked = false;
					}
				}
			}

			this.sendRequest();
		},

		editDeliveryBlock: function(active)
		{
			console.log('editDeliveryBlock');
			if (!this.deliveryBlockNode || !this.deliveryHiddenBlockNode || !this.result.DELIVERY)
				return;

			if (active)
				this.editActiveDeliveryBlock(true);
			else
				this.editFadeDeliveryBlock();

			this.checkPickUpShow();

			this.initialized.delivery = true;
		},

		editActiveDeliveryBlock: function(activeNodeMode)
		{
			console.log('editActiveDeliveryBlock');
			var node = activeNodeMode ? this.deliveryBlockNode : this.deliveryHiddenBlockNode,
				deliveryContent, deliveryNode;

			if (this.initialized.delivery)
			{
				BX.remove(BX.lastChild(node));
				node.appendChild(BX.firstChild(this.deliveryHiddenBlockNode));
			}
			else
			{
				deliveryContent = node.querySelector('.bx-soa-section-content');
				if (!deliveryContent)
				{
					deliveryContent = this.getNewContainer();
					node.appendChild(deliveryContent);
				}
				else
					BX.cleanNode(deliveryContent);

				this.getErrorContainer(deliveryContent);

				deliveryNode = BX.create('DIV', {props: {className: 'bx-soa-pp row'}});
				this.editDeliveryItems(deliveryNode);
				deliveryContent.appendChild(deliveryNode);
				this.editDeliveryInfo(deliveryNode);

				if (this.params.SHOW_COUPONS_DELIVERY == 'Y')
					this.editCoupons(deliveryContent);

				this.getBlockFooter(deliveryContent);
			}
		},

		editDeliveryItems: function(deliveryNode)
		{
			console.log('editDeliveryItems');
			if (!this.result.DELIVERY || this.result.DELIVERY.length <= 0)
				return;

			var deliveryItemsContainer = BX.create('DIV', {props: {className: 'col-sm-7 bx-soa-pp-item-container'}}),
				deliveryItemNode, k;

			for (k = 0; k < this.deliveryPagination.currentPage.length; k++)
			{
				deliveryItemNode = this.createDeliveryItem(this.deliveryPagination.currentPage[k]);
				deliveryItemsContainer.appendChild(deliveryItemNode);
			}

			if (this.deliveryPagination.show)
				this.showPagination('delivery', deliveryItemsContainer);

			deliveryNode.appendChild(deliveryItemsContainer);
		},

		editDeliveryInfo: function(deliveryNode)
		{
			console.log('editDeliveryInfo');
			if (!this.result.DELIVERY)
				return;

			var deliveryInfoContainer = BX.create('DIV', {props: {className: 'col-sm-5 bx-soa-pp-desc-container'}}),
				currentDelivery, logotype, name, logoNode,
				subTitle, label, title, price, period,
				clear, infoList, extraServices, extraServicesNode;

			BX.cleanNode(deliveryInfoContainer);
			currentDelivery = this.getSelectedDelivery();

			logoNode = BX.create('DIV', {props: {className: 'bx-soa-pp-company-image'}});
			logotype = this.getImageSources(currentDelivery, 'LOGOTIP');
			if (logotype && logotype.src_2x)
			{
				logoNode.setAttribute('style',
					'background-image: url("' + logotype.src_1x + '");' +
					'background-image: -webkit-image-set(url("' + logotype.src_1x + '") 1x, url("' + logotype.src_2x + '") 2x)'
				);
			}
			else
			{
				logotype = logotype && logotype.src_1x || this.defaultDeliveryLogo;
				logoNode.setAttribute('style', 'background-image: url("' + logotype + '");');
			}

			name = this.params.SHOW_DELIVERY_PARENT_NAMES != 'N' ? currentDelivery.NAME : currentDelivery.OWN_NAME;

			if (this.params.SHOW_DELIVERY_INFO_NAME == 'Y')
				subTitle = BX.create('DIV', {props: {className: 'bx-soa-pp-company-subTitle'}, text: name});

			label = BX.create('DIV', {
				props: {className: 'bx-soa-pp-company-logo'},
				children: [
					BX.create('DIV', {
						props: {className: 'bx-soa-pp-company-graf-container'},
						children: [logoNode]
					})
				]
			});
			title = BX.create('DIV', {
				props: {className: 'bx-soa-pp-company-block'},
				children: [
					BX.create('DIV', {props: {className: 'bx-soa-pp-company-desc'}, html: currentDelivery.DESCRIPTION}),
					currentDelivery.CALCULATE_DESCRIPTION
						? BX.create('DIV', {props: {className: 'bx-soa-pp-company-desc'}, html: currentDelivery.CALCULATE_DESCRIPTION})
						: null
				]
			});

			if (currentDelivery.PRICE >= 0)
			{
				price = BX.create('LI', {
					children: [
						BX.create('DIV', {
							props: {className: 'bx-soa-pp-list-termin'},
							html: this.params.MESS_PRICE + ':'
						}),
						BX.create('DIV', {
							props: {className: 'bx-soa-pp-list-description'},
							children: this.getDeliveryPriceNodes(currentDelivery)
						})
					]
				});
			}

			if (currentDelivery.PERIOD_TEXT && currentDelivery.PERIOD_TEXT.length)
			{
				period = BX.create('LI', {
					children: [
						BX.create('DIV', {props: {className: 'bx-soa-pp-list-termin'}, html: this.params.MESS_PERIOD + ':'}),
						BX.create('DIV', {props: {className: 'bx-soa-pp-list-description'}, html: currentDelivery.PERIOD_TEXT})
					]
				});
			}

			clear = BX.create('DIV', {style: {clear: 'both'}});
			infoList = BX.create('UL', {props: {className: 'bx-soa-pp-list'}, children: [price, period]});
			extraServices = this.getDeliveryExtraServices(currentDelivery);

			if (extraServices.length)
			{
				extraServicesNode = BX.create('DIV', {
					props: {className: 'bx-soa-pp-company-block'},
					children: extraServices
				});
			}

			deliveryInfoContainer.appendChild(
				BX.create('DIV', {
					props: {className: 'bx-soa-pp-company'},
					children: [subTitle, label, title, clear, extraServicesNode, infoList]
				})
			);
			deliveryNode.appendChild(deliveryInfoContainer);

			if (this.params.DELIVERY_NO_AJAX != 'Y')
				this.deliveryCachedInfo[currentDelivery.ID] = currentDelivery;
		},

		getDeliveryPriceNodes: function(delivery)
		{
			console.log('getDeliveryPriceNodes');
			var priceNodesArray;

			if (typeof delivery.DELIVERY_DISCOUNT_PRICE !== 'undefined'
				&& parseFloat(delivery.DELIVERY_DISCOUNT_PRICE) != parseFloat(delivery.PRICE))
			{
				if (parseFloat(delivery.DELIVERY_DISCOUNT_PRICE) > parseFloat(delivery.PRICE))
					priceNodesArray = [delivery.DELIVERY_DISCOUNT_PRICE_FORMATED];
				else
					priceNodesArray = [
						delivery.DELIVERY_DISCOUNT_PRICE_FORMATED,
						BX.create('BR'),
						BX.create('SPAN', {props: {className: 'bx-price-old'}, html: delivery.PRICE_FORMATED})
					];
			}
			else
			{
				priceNodesArray = [delivery.PRICE_FORMATED];
			}

			return priceNodesArray;
		},

		getDeliveryExtraServices: function(delivery)
		{
			console.log('getDeliveryExtraServices');
			var extraServices = [], brake = false,
				i, currentService, serviceNode, serviceName, input;

			for (i in delivery.EXTRA_SERVICES)
			{
				if (!delivery.EXTRA_SERVICES.hasOwnProperty(i))
					continue;

				currentService = delivery.EXTRA_SERVICES[i];

				if (!currentService.canUserEditValue)
					continue;

				if (currentService.editControl.indexOf('this.checked') == -1)
				{
					serviceName = BX.create('LABEL', {
						html: BX.util.htmlspecialchars(currentService.name)
						+ (currentService.price ? ' (' + currentService.priceFormatted + ')' : '')
					});

					if (i == 0)
						brake = true;

					serviceNode = BX.create('DIV', {
						props: {className: 'form-group bx-soa-pp-field'},
						html: currentService.editControl
						+ (currentService.description && currentService.description.length
							? '<div class="bx-soa-service-small">' + BX.util.htmlspecialchars(currentService.description) + '</div>'
							: '')
					});

					BX.prepend(serviceName, serviceNode);
					input = serviceNode.querySelector('input[type=text]');
					if (!input)
						input = serviceNode.querySelector('select');

					if (input)
						BX.addClass(input, 'form-control');
				}
				else
				{
					serviceNode = BX.create('DIV', {
						props: {className: 'checkbox'},
						children: [
							BX.create('LABEL', {
								html: currentService.editControl + BX.util.htmlspecialchars(currentService.name)
								+ (currentService.price ? ' (' + currentService.priceFormatted + ')' : '')
								+ (currentService.description && currentService.description.length
									? '<div class="bx-soa-service-small">' + BX.util.htmlspecialchars(currentService.description) + '</div>'
									: '')
							})
						]
					});
				}

				extraServices.push(serviceNode);
			}

			brake && extraServices.unshift(BX.create('BR'));

			return extraServices;
		},

		editFadeDeliveryBlock: function()
		{
			console.log('editFadeDeliveryBlock');
			var deliveryContent = this.deliveryBlockNode.querySelector('.bx-soa-section-content'), newContent;

			if (this.initialized.delivery)
			{
				this.deliveryHiddenBlockNode.appendChild(deliveryContent);
			}
			else
			{
				this.editActiveDeliveryBlock(false);
				BX.remove(BX.lastChild(this.deliveryBlockNode));
			}

			newContent = this.getNewContainer(true);
			this.deliveryBlockNode.appendChild(newContent);

			this.editFadeDeliveryContent(newContent);

			if (this.params.SHOW_COUPONS_DELIVERY == 'Y')
				this.editCouponsFade(newContent);
		},

		createDeliveryItem: function(item)
		{
			console.log('createDeliveryItem');
			var checked = item.CHECKED == 'Y',
				deliveryId = parseInt(item.ID),
				labelNodes = [
					BX.create('INPUT', {
						props: {
							id: 'ID_DELIVERY_ID_' + deliveryId,
							name: 'DELIVERY_ID',
							type: 'checkbox',
							className: 'bx-soa-pp-company-checkbox',
							value: deliveryId,
							checked: checked
						}
					})
				],
				deliveryCached = this.deliveryCachedInfo[deliveryId],
				logotype, label, title, itemNode, logoNode;

			logoNode = BX.create('DIV', {props: {className: 'bx-soa-pp-company-image'}});
			logotype = this.getImageSources(item, 'LOGOTIP');
			if (logotype && logotype.src_2x)
			{
				logoNode.setAttribute('style',
					'background-image: url("' + logotype.src_1x + '");' +
					'background-image: -webkit-image-set(url("' + logotype.src_1x + '") 1x, url("' + logotype.src_2x + '") 2x)'
				);
			}
			else
			{
				logotype = logotype && logotype.src_1x || this.defaultDeliveryLogo;
				logoNode.setAttribute('style', 'background-image: url("' + logotype + '");');
			}
			labelNodes.push(logoNode);

			if (item.PRICE >= 0 || typeof item.DELIVERY_DISCOUNT_PRICE !== 'undefined')
			{
				labelNodes.push(
					BX.create('DIV', {
						props: {className: 'bx-soa-pp-delivery-cost'},
						html: typeof item.DELIVERY_DISCOUNT_PRICE !== 'undefined'
							? item.DELIVERY_DISCOUNT_PRICE_FORMATED
							: item.PRICE_FORMATED})
				);
			}
			else if (deliveryCached && (deliveryCached.PRICE >= 0 || typeof deliveryCached.DELIVERY_DISCOUNT_PRICE !== 'undefined'))
			{
				labelNodes.push(
					BX.create('DIV', {
						props: {className: 'bx-soa-pp-delivery-cost'},
						html: typeof deliveryCached.DELIVERY_DISCOUNT_PRICE !== 'undefined'
							? deliveryCached.DELIVERY_DISCOUNT_PRICE_FORMATED
							: deliveryCached.PRICE_FORMATED})
				);
			}

			label = BX.create('DIV', {
				props: {
					className: 'bx-soa-pp-company-graf-container'
					+ (item.CALCULATE_ERRORS || deliveryCached && deliveryCached.CALCULATE_ERRORS ? ' bx-bd-waring' : '')},
				children: labelNodes
			});

			if (this.params.SHOW_DELIVERY_LIST_NAMES == 'Y')
			{
				title = BX.create('DIV', {
					props: {className: 'bx-soa-pp-company-smalltitle'},
					text: this.params.SHOW_DELIVERY_PARENT_NAMES != 'N' ? item.NAME : item.OWN_NAME
				});
			}

			itemNode = BX.create('DIV', {
				props: {className: 'bx-soa-pp-company col-lg-4 col-sm-4 col-xs-6'},
				children: [label, title],
				events: {click: BX.proxy(this.selectDelivery, this)}
			});
			checked && BX.addClass(itemNode, 'bx-selected');

			if (checked && this.result.LAST_ORDER_DATA.PICK_UP)
				this.lastSelectedDelivery = deliveryId;

			return itemNode;
		},

		editFadeDeliveryContent: function(node)
		{
			console.log('editFadeDeliveryContent');
			var selectedDelivery = this.getSelectedDelivery(),
				name = this.params.SHOW_DELIVERY_PARENT_NAMES != 'N' ? selectedDelivery.NAME : selectedDelivery.OWN_NAME,
				errorNode = this.deliveryHiddenBlockNode.querySelector('div.alert.alert-danger'),
				warningNode = this.deliveryHiddenBlockNode.querySelector('div.alert.alert-warning.alert-show'),
				extraService, logotype, imgSrc, arNodes, i;

			if (errorNode && errorNode.innerHTML)
				node.appendChild(errorNode.cloneNode(true));
			else
				this.getErrorContainer(node);

			if (warningNode && warningNode.innerHTML)
				node.appendChild(warningNode.cloneNode(true));

			if (selectedDelivery && selectedDelivery.NAME)
			{
				logotype = this.getImageSources(selectedDelivery, 'LOGOTIP');
				imgSrc = logotype && logotype.src_1x || this.defaultDeliveryLogo;
				arNodes = [
					BX.create('IMG', {props: {src: imgSrc, alt: ''}, style: {height: '18px'}}),
					BX.create('STRONG', {text: name})
				];

				if (this.params.DELIVERY_FADE_EXTRA_SERVICES == 'Y' && BX.util.object_keys(selectedDelivery.EXTRA_SERVICES).length)
				{
					arNodes.push(BX.create('BR'));

					for (i in selectedDelivery.EXTRA_SERVICES)
					{
						if (selectedDelivery.EXTRA_SERVICES.hasOwnProperty(i))
						{
							extraService = selectedDelivery.EXTRA_SERVICES[i];
							if (extraService.value && extraService.value != 'N' && extraService.canUserEditValue)
							{
								arNodes.push(BX.create('BR'));
								arNodes.push(BX.create('STRONG', {text: extraService.name + ': '}));
								arNodes.push(extraService.viewControl);
							}
						}
					}
				}

				node.appendChild(
					BX.create('DIV', {
						props: {className: 'col-sm-9 bx-soa-pp-company-selected'},
						children: arNodes
					})
				);
				node.appendChild(
					BX.create('DIV', {
						props: {className: 'col-sm-3 bx-soa-pp-price'},
						children: this.getDeliveryPriceNodes(selectedDelivery)
					})
				);
			}
			else
				node.appendChild(BX.create('STRONG', {text: BX.message('SOA_DELIVERY_SELECT_ERROR')}));

			node.appendChild(BX.create('DIV', {style: {clear: 'both'}}));
			BX.bind(node.querySelector('.alert.alert-danger'), 'click', BX.proxy(this.showByClick, this));
			BX.bind(node.querySelector('.alert.alert-warning'), 'click', BX.proxy(this.showByClick, this));
		},

		selectDelivery: function(event)
		{
			console.log('selectDelivery');
			if (!this.orderBlockNode)
				return;

			var target = event.target || event.srcElement,
				actionSection =  BX.hasClass(target, 'bx-soa-pp-company') ? target : BX.findParent(target, {className: 'bx-soa-pp-company'}),
				selectedSection = this.deliveryBlockNode.querySelector('.bx-soa-pp-company.bx-selected'),
				actionInput, selectedInput;

			if (BX.hasClass(actionSection, 'bx-selected'))
				return BX.PreventDefault(event);

			if (actionSection)
			{
				actionInput = actionSection.querySelector('input[type=checkbox]');
				BX.addClass(actionSection, 'bx-selected');
				actionInput.checked = true;
			}
			if (selectedSection)
			{
				selectedInput = selectedSection.querySelector('input[type=checkbox]');
				BX.removeClass(selectedSection, 'bx-selected');
				selectedInput.checked = false;
			}

			this.sendRequest();
		},

		getSelectedDelivery: function()
		{
			console.log('getSelectedDelivery');
			var deliveryCheckbox = this.deliveryBlockNode.querySelector('input[type=checkbox][name=DELIVERY_ID]:checked'),
				currentDelivery = false,
				deliveryId, i;

			if (!deliveryCheckbox)
				deliveryCheckbox = this.deliveryHiddenBlockNode.querySelector('input[type=checkbox][name=DELIVERY_ID]:checked');

			if (!deliveryCheckbox)
				deliveryCheckbox = this.deliveryHiddenBlockNode.querySelector('input[type=hidden][name=DELIVERY_ID]');

			if (deliveryCheckbox)
			{
				deliveryId = deliveryCheckbox.value;

				for (i in this.result.DELIVERY)
				{
					if (this.result.DELIVERY[i].ID == deliveryId)
					{
						currentDelivery = this.result.DELIVERY[i];
						break;
					}
				}
			}

			return currentDelivery;
		},

		editPickUpBlock: function(active)
		{
			console.log('editPickUpBlock');
			if (!this.pickUpBlockNode || !this.pickUpHiddenBlockNode || !BX.hasClass(this.pickUpBlockNode, 'bx-active') || !this.result.DELIVERY)
				return;

			this.initialized.pickup = false;

			if (active)
				this.editActivePickUpBlock(true);
			else
				this.editFadePickUpBlock();

			this.initialized.pickup = true;
		},

		editActivePickUpBlock: function(activeNodeMode)
		{
			console.log('editActivePickUpBlock');
			var node = activeNodeMode ? this.pickUpBlockNode : this.pickUpHiddenBlockNode,
				pickUpContent, pickUpContentCol;

			if (this.initialized.pickup)
			{
				BX.remove(BX.lastChild(node));
				node.appendChild(BX.firstChild(this.pickUpHiddenBlockNode));

				if (
					this.params.SHOW_NEAREST_PICKUP === 'Y'
					&& this.maps
					&& !this.maps.maxWaitTimeExpired
				)
				{
					this.maps.maxWaitTimeExpired = true;
					this.initPickUpPagination();
					this.editPickUpList(true);
					this.pickUpFinalAction();
				}

				if (this.maps && !this.pickUpMapFocused)
				{
					this.pickUpMapFocused = true;
					setTimeout(BX.proxy(this.maps.pickUpMapFocusWaiter, this.maps), 200);
				}
			}
			else
			{
				pickUpContent = node.querySelector('.bx-soa-section-content');
				if (!pickUpContent)
				{
					pickUpContent = this.getNewContainer();
					node.appendChild(pickUpContent);
				}
				BX.cleanNode(pickUpContent);

				pickUpContentCol = BX.create('DIV', {props: {className: 'col-xs-12'}});
				this.editPickUpMap(pickUpContentCol);
				this.editPickUpLoader(pickUpContentCol);

				pickUpContent.appendChild(
					BX.create('DIV', {
						props: {className: 'bx_soa_pickup row'},
						children: [pickUpContentCol]
					})
				);

				if (this.params.SHOW_PICKUP_MAP != 'Y' || this.params.SHOW_NEAREST_PICKUP != 'Y')
				{
					this.initPickUpPagination();
					this.editPickUpList(true);
					this.pickUpFinalAction();
				}

				this.getBlockFooter(pickUpContent);
			}
		},

		editFadePickUpBlock: function()
		{
			console.log('editFadePickUpBlock');
			var pickUpContent = this.pickUpBlockNode.querySelector('.bx-soa-section-content'), newContent;

			if (this.initialized.pickup)
			{
				this.pickUpHiddenBlockNode.appendChild(pickUpContent);
			}
			else
			{
				this.editActivePickUpBlock(false);
				BX.remove(BX.lastChild(this.pickUpBlockNode));
			}

			newContent = this.getNewContainer();
			this.pickUpBlockNode.appendChild(newContent);

			this.editFadePickUpContent(newContent);
		},

		editFadePickUpContent: function(pickUpContainer)
		{
			console.log('editFadePickUpContent');
			var selectedPickUp = this.getSelectedPickUp(), html = '', logotype, imgSrc;

			if (selectedPickUp)
			{
				if (this.params.SHOW_STORES_IMAGES == 'Y')
				{
					logotype = this.getImageSources(selectedPickUp, 'IMAGE_ID');
					imgSrc = logotype.src_1x || this.defaultStoreLogo;

					html += '<img src="' + imgSrc + '" class="bx-soa-pickup-preview-img">';
				}

				html += '<strong>' + BX.util.htmlspecialchars(selectedPickUp.TITLE) + '</strong>';
				if (selectedPickUp.ADDRESS)
					html += '<br><strong>' + BX.message('SOA_PICKUP_ADDRESS') + ':</strong> ' + BX.util.htmlspecialchars(selectedPickUp.ADDRESS);

				if (selectedPickUp.PHONE)
					html += '<br><strong>' + BX.message('SOA_PICKUP_PHONE') + ':</strong> ' + BX.util.htmlspecialchars(selectedPickUp.PHONE);

				if (selectedPickUp.SCHEDULE)
					html += '<br><strong>' + BX.message('SOA_PICKUP_WORK') + ':</strong> ' + BX.util.htmlspecialchars(selectedPickUp.SCHEDULE);

				if (selectedPickUp.DESCRIPTION)
					html += '<br><strong>' + BX.message('SOA_PICKUP_DESC') + ':</strong> ' + BX.util.htmlspecialchars(selectedPickUp.DESCRIPTION);

				pickUpContainer.innerHTML = html;

				if (this.params.SHOW_STORES_IMAGES == 'Y')
				{
					BX.bind(pickUpContainer.querySelector('.bx-soa-pickup-preview-img'), 'click', BX.delegate(function(e){
						this.popupShow(e, logotype && logotype.src_orig || imgSrc);
					}, this));
				}
			}
		},

		getPickUpInfoArray: function(storeIds)
		{
			console.log('getPickUpInfoArray');
			if (!storeIds || storeIds.length <= 0)
				return [];

			var arr = [], i;

			for (i = 0; i < storeIds.length; i++)
				if (this.result.STORE_LIST[storeIds[i]])
					arr.push(this.result.STORE_LIST[storeIds[i]]);

			return arr;
		},

		getSelectedPickUp: function()
		{
			console.log('getSelectedPickUp');
			var pickUpInput = BX('BUYER_STORE'),
				currentPickUp, pickUpId,
				allStoresList = this.result.STORE_LIST,
				stores, i;

			if (pickUpInput)
			{
				pickUpId = pickUpInput.value;
				currentPickUp = allStoresList[pickUpId];

				if (!currentPickUp)
				{
					stores = this.getSelectedDelivery().STORE;
					if (stores)
					{
						for (i in stores)
						{
							if (stores.hasOwnProperty(i))
							{
								currentPickUp = allStoresList[stores[i]];
								pickUpInput.setAttribute('value', stores[i]);
								break;
							}
						}
					}
				}
			}

			return currentPickUp;
		},

		/**
		 * Checking delivery for pick ups. Displaying/hiding pick up block node.
		 */
		geoLocationSuccessCallback: function(result)
		{
			console.log('geoLocationSuccessCallback');
			var activeStores,
				currentDelivery = this.getSelectedDelivery();

			if (currentDelivery && currentDelivery.STORE)
			{
				activeStores = this.getPickUpInfoArray(currentDelivery.STORE);
			}

			if (activeStores && activeStores.length >= this.options.pickUpMap.minToShowNearestBlock)
			{
				this.editPickUpRecommendList(result.geoObjects.get(0));
			}

			this.initPickUpPagination();
			this.editPickUpList(true);
			this.pickUpFinalAction();
		},

		geoLocationFailCallback: function()
		{
			console.log('geoLocationFailCallback');
			this.initPickUpPagination();
			this.editPickUpList(true);
			this.pickUpFinalAction();
		},

		initMaps: function()
		{
			console.log('initMaps');
			this.maps = BX.Sale.OrderAjaxComponent.Maps.init(this);
			if (this.maps)
			{
				this.mapsReady = true;
				this.resizeMapContainers();

				if (this.params.SHOW_PICKUP_MAP === 'Y' && BX('pickUpMap'))
				{
					var currentDelivery = this.getSelectedDelivery();
					if (currentDelivery && currentDelivery.STORE && currentDelivery.STORE.length)
					{
						var activeStores = this.getPickUpInfoArray(currentDelivery.STORE);
					}

					if (activeStores && activeStores.length)
					{
						var selected = this.getSelectedPickUp();
						this.maps.initializePickUpMap(selected);

						if (this.params.SHOW_NEAREST_PICKUP === 'Y')
						{
							this.maps.showNearestPickups(BX.proxy(this.geoLocationSuccessCallback, this), BX.proxy(this.geoLocationFailCallback, this));
						}

						this.maps.buildBalloons(activeStores);
					}
				}

				if (this.params.SHOW_MAP_IN_PROPS === 'Y' && BX('propsMap'))
				{
					var propsMapData = this.getPropertyMapData();
					this.maps.initializePropsMap(propsMapData);
				}
			}
		},

		getPropertyMapData: function()
		{
			console.log('getPropertyMapData');
			var currentProperty, locationId, k;
			var data = this.options.propertyMap.defaultMapPosition;

			for (k in this.result.ORDER_PROP.properties)
			{
				if (this.result.ORDER_PROP.properties.hasOwnProperty(k))
				{
					currentProperty = this.result.ORDER_PROP.properties[k];
					if (currentProperty.IS_LOCATION == 'Y')
					{
						locationId = currentProperty.ID;
						break;
					}
				}
			}

			if (this.locations[locationId] && this.locations[locationId][0] && this.locations[locationId][0].coordinates)
			{
				currentProperty = this.locations[locationId][0].coordinates;

				var long = parseFloat(currentProperty.LONGITUDE),
					lat = parseFloat(currentProperty.LATITUDE);

				if (!isNaN(long) && !isNaN(lat) && long != 0 && lat != 0)
				{
					data.lon = long;
					data.lat = lat;
				}
			}

			return data;
		},

		resizeMapContainers: function()
		{
			console.log('resizeMapContainers');
			var pickUpMapContainer = BX('pickUpMap'),
				propertyMapContainer = BX('propsMap'),
				resizeBy = this.propsBlockNode,
				width, height;

			if (resizeBy && (pickUpMapContainer || propertyMapContainer))
			{
				width = resizeBy.clientWidth;
				height = parseInt(width / 16 * 9);

				if (this.params.SHOW_PICKUP_MAP === 'Y' && pickUpMapContainer)
				{
					pickUpMapContainer.style.height = height + 'px';
				}

				if (this.params.SHOW_MAP_IN_PROPS === 'Y' && propertyMapContainer)
				{
					propertyMapContainer.style.height = height + 'px';
				}
			}
		},

		editPickUpMap: function(pickUpContent)
		{
			console.log('editPickUpMap');
			pickUpContent.appendChild(BX.create('DIV', {
				props: {id: 'pickUpMap'},
				style: {width: '100%', marginBottom: '10px'}
			}));
		},

		editPickUpLoader: function(pickUpContent)
		{
			console.log('editPickUpLoader');
			pickUpContent.appendChild(
				BX.create('DIV', {
					props: {id: 'pickUpLoader', className: 'text-center'},
					children: [BX.create('IMG', {props: {src: this.templateFolder + '/images/loader.gif'}})]
				})
			);
		},

		editPickUpList: function(isNew)
		{
			console.log('editPickUpList');
			if (!this.pickUpPagination.currentPage || !this.pickUpPagination.currentPage.length)
				return;

			BX.remove(BX('pickUpLoader'));

			var pickUpList = BX.create('DIV', {props: {className: 'bx-soa-pickup-list main'}}),
				buyerStoreInput = BX('BUYER_STORE'),
				selectedStore,
				container, i, found = false,
				recommendList, selectedDelivery, currentStore, storeNode;

			if (buyerStoreInput)
				selectedStore = buyerStoreInput.value;

			recommendList = this.pickUpBlockNode.querySelector('.bx-soa-pickup-list.recommend');
			if (!recommendList)
				recommendList = this.pickUpHiddenBlockNode.querySelector('.bx-soa-pickup-list.recommend');

			if (!recommendList || !recommendList.querySelector('.bx-soa-pickup-list-item.bx-selected'))
			{
				selectedDelivery = this.getSelectedDelivery();
				if (selectedDelivery && selectedDelivery.STORE)
				{
					for (i = 0; i < selectedDelivery.STORE.length; i++)
						if (selectedDelivery.STORE[i] == selectedStore)
							found = true;
				}
			}
			else
				found = true;

			for (i = 0; i < this.pickUpPagination.currentPage.length; i++)
			{
				currentStore = this.pickUpPagination.currentPage[i];

				if (currentStore.ID == selectedStore || parseInt(selectedStore) == 0 || !found)
				{
					selectedStore = buyerStoreInput.value = currentStore.ID;
					found = true;
				}

				storeNode = this.createPickUpItem(currentStore, {selected: currentStore.ID == selectedStore});
				pickUpList.appendChild(storeNode);
			}

			if (!!isNew)
			{
				container = this.pickUpHiddenBlockNode.querySelector('.bx_soa_pickup>.col-xs-12');
				if (!container)
					container = this.pickUpBlockNode.querySelector('.bx_soa_pickup>.col-xs-12');

				container.appendChild(
					BX.create('DIV', {
						props: {className: 'bx-soa-pickup-subTitle'},
						html: this.params.MESS_PICKUP_LIST
					})
				);
				container.appendChild(pickUpList);
			}
			else
			{
				container = this.pickUpBlockNode.querySelector('.bx-soa-pickup-list.main');
				BX.insertAfter(pickUpList, container);
				BX.remove(container);
			}

			this.pickUpPagination.show && this.showPagination('pickUp', pickUpList);
		},

		pickUpFinalAction: function()
		{
			console.log('pickUpFinalAction');
			var selectedDelivery = this.getSelectedDelivery(),
				deliveryChanged;

			if (selectedDelivery)
			{
				deliveryChanged = this.lastSelectedDelivery !== parseInt(selectedDelivery.ID);
				this.lastSelectedDelivery = parseInt(selectedDelivery.ID);
			}

			if (deliveryChanged && this.pickUpBlockNode.id !== this.activeSectionId)
			{
				if (this.pickUpBlockNode.id !== this.activeSectionId)
				{
					this.editFadePickUpContent(BX.lastChild(this.pickUpBlockNode));
				}

				BX.removeClass(this.pickUpBlockNode, 'bx-step-completed');
			}

			this.maps && this.maps.pickUpFinalAction();
		},

		getStoreInfoHtml: function(currentStore)
		{
			console.log('getStoreInfoHtml');
			var html = '';

			if (currentStore.ADDRESS)
				html += BX.message('SOA_PICKUP_ADDRESS') + ': ' + BX.util.htmlspecialchars(currentStore.ADDRESS) + '<br>';

			if (currentStore.PHONE)
				html += BX.message('SOA_PICKUP_PHONE') + ': ' + BX.util.htmlspecialchars(currentStore.PHONE) + '<br>';

			if (currentStore.SCHEDULE)
				html += BX.message('SOA_PICKUP_WORK') + ': ' + BX.util.htmlspecialchars(currentStore.SCHEDULE) + '<br>';

			if (currentStore.DESCRIPTION)
				html += BX.message('SOA_PICKUP_DESC') + ': ' + BX.util.htmlspecialchars(currentStore.DESCRIPTION) + '<br>';

			return html;
		},

		createPickUpItem: function(currentStore, options)
		{
			console.log('createPickUpItem');
			options = options || {};

			var imgClassName = 'bx-soa-pickup-l-item-detail',
				buttonClassName = 'bx-soa-pickup-l-item-btn',
				logoNode, logotype, html, storeNode, imgSrc;

			if (this.params.SHOW_STORES_IMAGES === 'Y')
			{
				logotype = this.getImageSources(currentStore, 'IMAGE_ID');
				imgSrc = logotype && logotype.src_1x || this.defaultStoreLogo;
				logoNode = BX.create('IMG', {
					props: {
						src: imgSrc,
						className: 'bx-soa-pickup-l-item-img'
					},
					events: {
						click: BX.delegate(function(e){
							this.popupShow(e, logotype && logotype.src_orig || imgSrc);
						}, this)
					}
				});
			}
			else
			{
				imgClassName += ' no-image';
				buttonClassName += ' no-image';
			}

			html = this.getStoreInfoHtml(currentStore);
			storeNode = BX.create('DIV', {
				props: {className: 'bx-soa-pickup-list-item', id: 'store-' + currentStore.ID},
				children: [
					BX.create('DIV', {
						props: {className: 'bx-soa-pickup-l-item-adress'},
						children: options.distance ? [
							BX.util.htmlspecialchars(currentStore.ADDRESS),
							' ( ~' + options.distance + ' ' + BX.message('SOA_DISTANCE_KM') + ' ) '
						] : [BX.util.htmlspecialchars(currentStore.ADDRESS)]
					}),
					BX.create('DIV', {
						props: {className: imgClassName},
						children: [
							logoNode,
							BX.create('DIV', {props: {className: 'bx-soa-pickup-l-item-name'}, text: currentStore.TITLE}),
							BX.create('DIV', {props: {className: 'bx-soa-pickup-l-item-desc'}, html: html})
						]
					}),
					BX.create('DIV', {
						props: {className: buttonClassName},
						children: [
							BX.create('A', {
								props: {href: '', className: 'btn btn-sm btn-default'},
								html: this.params.MESS_SELECT_PICKUP,
								events: {
									click: BX.delegate(function(event){
										this.selectStore(event);
										this.clickNextAction(event)
									}, this)
								}
							})
						]
					})
				],
				events: {
					click: BX.proxy(this.selectStore, this)
				}
			});

			if (options.selected)
				BX.addClass(storeNode, 'bx-selected');

			return storeNode;
		},

		editPickUpRecommendList: function(geoLocation)
		{
			console.log('editPickUpRecommendList');
			if (!this.maps || !this.maps.canUseRecommendList() || !geoLocation)
			{
				return;
			}

			BX.remove(BX('pickUpLoader'));

			var recommendList = BX.create('DIV', {props: {className: 'bx-soa-pickup-list recommend'}}),
				buyerStoreInput = BX('BUYER_STORE'),
				selectedDelivery = this.getSelectedDelivery();

			var i, currentStore, currentStoreId, distance, storeNode, container;

			var recommendedStoreIds = this.maps.getRecommendedStoreIds(geoLocation);
			for (i = 0; i < recommendedStoreIds.length; i++)
			{
				currentStoreId = recommendedStoreIds[i];
				currentStore = this.getPickUpInfoArray([currentStoreId])[0];

				if (i === 0 && parseInt(selectedDelivery.ID) !== this.lastSelectedDelivery)
				{
					buyerStoreInput.value = parseInt(currentStoreId);
				}

				distance = this.maps.getDistance(geoLocation, currentStoreId);
				storeNode = this.createPickUpItem(currentStore, {
					selected: buyerStoreInput.value === currentStoreId,
					distance: distance
				});
				recommendList.appendChild(storeNode);

				if (selectedDelivery.STORE_MAIN)
				{
					selectedDelivery.STORE_MAIN.splice(selectedDelivery.STORE_MAIN.indexOf(currentStoreId), 1);
				}
			}

			container = this.pickUpHiddenBlockNode.querySelector('.bx_soa_pickup>.col-xs-12');
			if (!container)
			{
				container = this.pickUpBlockNode.querySelector('.bx_soa_pickup>.col-xs-12');
			}

			container.appendChild(
				BX.create('DIV', {
					props: {className: 'bx-soa-pickup-subTitle'},
					html: this.params.MESS_NEAREST_PICKUP_LIST
				})
			);
			container.appendChild(recommendList);
		},

		selectStore: function(event)
		{
			console.log('selectStore');
			var storeItem,
				storeInput = BX('BUYER_STORE'),
				selectedPickUp, storeItemId, i, k, page,
				target, h1, h2;

			if (BX.type.isString(event))
			{
				storeItem = BX('store-' + event);
				if (!storeItem)
				{
					for (i = 0; i < this.pickUpPagination.pages.length; i++)
					{
						page = this.pickUpPagination.pages[i];
						for (k = 0; k < page.length; k++)
						{
							if (page[k].ID == event)
							{
								this.showPickUpItemsPage(++i);
								break;
							}
						}
					}
					storeItem = BX('store-' + event);
				}
			}
			else
			{
				target = event.target || event.srcElement;
				storeItem = BX.hasClass(target, 'bx-soa-pickup-list-item')
					? target
					: BX.findParent(target, {className: 'bx-soa-pickup-list-item'});
			}

			if (storeItem && storeInput)
			{
				if (BX.hasClass(storeItem, 'bx-selected'))
					return;

				selectedPickUp = this.pickUpBlockNode.querySelector('.bx-selected');
				storeItemId = storeItem.id.substr('store-'.length);

				BX.removeClass(selectedPickUp, 'bx-selected');

				h1 = storeItem.clientHeight;
				storeItem.style.overflow = 'hidden';
				BX.addClass(storeItem, 'bx-selected');
				h2 = storeItem.clientHeight;
				storeItem.style.height = h1 + 'px';

				new BX.easing({
					duration: 300,
					start: {height: h1, opacity: 0},
					finish: {height: h2, opacity: 100},
					transition: BX.easing.transitions.quad,
					step: function(state){
						storeItem.style.height = state.height + "px";
					},
					complete: function(){
						storeItem.removeAttribute('style');
					}
				}).animate();

				storeInput.setAttribute('value', storeItemId);
				this.maps && this.maps.selectBalloon(storeItemId);
			}
		},

		getDeliverySortedArray: function(objDelivery)
		{
			console.log('getDeliverySortedArray');
			var deliveries = [],
				problemDeliveries = [],
				sortFunc = function(a, b){
					var sort = parseInt(a.SORT) - parseInt(b.SORT);
					if (sort === 0)
					{
						return a.OWN_NAME.toLowerCase() > b.OWN_NAME.toLowerCase()
							? 1
							: (a.OWN_NAME.toLowerCase() < b.OWN_NAME.toLowerCase() ? -1 : 0);
					}
					else
					{
						return sort;
					}
				},
				k;

			for (k in objDelivery)
			{
				if (objDelivery.hasOwnProperty(k))
				{
					if (this.params.SHOW_NOT_CALCULATED_DELIVERIES === 'L' && objDelivery[k].CALCULATE_ERRORS)
					{
						problemDeliveries.push(objDelivery[k]);
					}
					else
					{
						deliveries.push(objDelivery[k]);
					}
				}
			}

			deliveries.sort(sortFunc);
			problemDeliveries.sort(sortFunc);

			return deliveries.concat(problemDeliveries);
		},

		editPropsBlock: function()
		{
			if (!this.propsBlockNode || !this.propsHiddenBlockNode || !this.result.ORDER_PROP)
				return;

			this.editActivePropsBlock();
		},

		editActivePropsBlock: function()
		{
			var node = this.propsBlockNode,
				propsContent, propsNode, selectedDelivery, showPropMap = false, i, validationErrors;
			
			this.editPropsItems(node);
			validationErrors = this.isValidPropertiesBlock(true);
			BX.remove(node);
		},

		editFadePropsBlock: function()
		{
			console.log('editFadePropsBlock');
			var propsContent = this.propsBlockNode.querySelector('.bx-soa-section-content'), newContent;

			if (this.initialized.props)
			{
				this.propsHiddenBlockNode.appendChild(propsContent);
			}
			else
			{
				this.editActivePropsBlock(false);
				BX.remove(BX.lastChild(this.propsBlockNode));
			}

			newContent = this.getNewContainer();
			this.propsBlockNode.appendChild(newContent);

			this.editFadePropsContent(newContent);
		},

		editFadePropsContent: function(node)
		{
			console.log('editFadePropsContent');
			if (!node || !this.locationsInitialized)
				return;

			var errorNode = this.propsHiddenBlockNode.querySelector('.alert'),
				personType = this.getSelectedPersonType(),
				fadeParamName, props,
				group, property, groupIterator, propsIterator, i, validPropsErrors;

			BX.cleanNode(node);

			if (errorNode)
				node.appendChild(errorNode.cloneNode(true));

			if (personType)
			{
				fadeParamName = 'PROPS_FADE_LIST_' + personType.ID;
				props = this.params[fadeParamName];
			}

			if (!props || props.length === 0)
			{
				node.innerHTML += '<strong>' + BX.message('SOA_ORDER_PROPS') + '</strong>';
			}
			else
			{
				groupIterator = this.fadedPropertyCollection.getGroupIterator();
				while (group = groupIterator())
				{
					propsIterator = group.getIterator();
					while (property = propsIterator())
					{
						for (i = 0; i < props.length; i++)
							if (props[i] == property.getId() && property.getSettings()['IS_ZIP'] != 'Y')
								this.getPropertyRowNode(property, node, true);
					}
				}
			}

			if (this.propsBlockNode.getAttribute('data-visited') === 'true')
			{
				validPropsErrors = this.isValidPropertiesBlock();
				if (validPropsErrors.length)
					this.showError(this.propsBlockNode, validPropsErrors);
			}

			BX.bind(node.querySelector('.alert.alert-danger'), 'click', BX.proxy(this.showByClick, this));
			BX.bind(node.querySelector('.alert.alert-warning'), 'click', BX.proxy(this.showByClick, this));
		},

		editPropsItems: function(propsNode)
		{
			if (!this.result.ORDER_PROP || !this.propertyCollection)
				return;
			var group, property, groupIterator = this.propertyCollection.getGroupIterator(), propsIterator, properties = [];

			while (group = groupIterator())
			{
				propsIterator =  group.getIterator();
				while (property = propsIterator())
				{
					properties.push(property);
				}
				properties.sort(function(a, b){return parseInt(a.getSettings().SORT) - parseInt(b.getSettings().SORT)});
				for(property in properties){
					this.getPropertyRowNode(properties[property], propsNode);
				}
			}
		},

		getPropertyRowNode: function(property, propsItemsContainer)
		{
			var textHtml = '',
				propertyType = property.getType() || '',
				propertyDesc = property.getDescription() || '',
				label, className;

			textHtml += '<div class="calculate__input-name">'+BX.util.htmlspecialchars(property.getName());
			if (property.isRequired())
				textHtml += '*';
			if (propertyDesc.length && propertyType != 'STRING' && propertyType != 'NUMBER' && propertyType != 'DATE')
				textHtml += ' <small>(' + BX.util.htmlspecialchars(propertyDesc) + ')</small>';
			textHtml += '</div>';
			textHtml += this.getIcon(property);
			className = property.getSettings().PERSON_TYPE_ID === "1"?'calculate__item calculate__label-input':'calculate__item calculate__label-input calculate__item--corporate';
			label = BX.create('LABEL', {
				props: {className: className},
				attrs: {'data-property-id-row':property.getId()},
				html: textHtml
			});
			if(propertyType !== 'FILE')
				BX.insertBefore(label, propsItemsContainer);
			let regionLabel = this.regionBlockNode.querySelector('.calculate__input-select-btn-load');
			switch (propertyType)
			{
				case 'LOCATION':
					this.insertLocationProperty(property, label);
					break;
				case 'DATE':
					this.insertDateProperty(property, label);
					break;
				case 'FILE':
					this.insertFileProperty(property, regionLabel);
					break;
				case 'STRING':
					this.insertStringProperty(property, label);
					break;
				case 'ENUM':
					this.insertEnumProperty(property, label);
					break;
				case 'Y/N':
					this.insertYNProperty(property, label);
					break;
				case 'NUMBER':
					this.insertNumberProperty(property, label);
			}
			if(property.getSettings().PERSON_TYPE_ID == 2){
				label.style.display = "flex";
			}
		},

		getIcon(property)
		{
			console.log('getIcon');
			var settings = property.getSettings();
			switch(settings.CODE){
				case "name":
					return '<svg viewBox="0 0 13 19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.66216 9C6.56081 8.98985 6.43919 8.98985 6.3277 9C3.91554 8.91883 2 6.94025 2 4.50507C2 2.01917 4.00676 0 6.5 0C8.98311 0 11 2.01917 11 4.50507C10.9899 6.94025 9.07432 8.91883 6.66216 9Z" /><path d="M1.72984 12.2734C-0.576613 13.7765 -0.576613 16.2259 1.72984 17.7196C4.35081 19.4268 8.64919 19.4268 11.2702 17.7196C13.5766 16.2166 13.5766 13.7672 11.2702 12.2734C8.65872 10.5755 4.36034 10.5755 1.72984 12.2734Z" /></svg>';
				case "phone":
					return '<svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.97 16.33C19.97 16.69 19.89 17.06 19.72 17.42C19.55 17.78 19.33 18.12 19.04 18.44C18.55 18.98 18.01 19.37 17.4 19.62C16.8 19.87 16.15 20 15.45 20C14.43 20 13.34 19.76 12.19 19.27C11.04 18.78 9.89 18.12 8.75 17.29C7.6 16.45 6.51 15.52 5.47 14.49C4.44 13.45 3.51 12.36 2.68 11.22C1.86 10.08 1.2 8.94 0.72 7.81C0.24 6.67 0 5.58 0 4.54C0 3.86 0.12 3.21 0.36 2.61C0.6 2 0.98 1.44 1.51 0.94C2.15 0.31 2.85 0 3.59 0C3.87 0 4.15 0.0600001 4.4 0.18C4.66 0.3 4.89 0.48 5.07 0.74L7.39 4.01C7.57 4.26 7.7 4.49 7.79 4.71C7.88 4.92 7.93 5.13 7.93 5.32C7.93 5.56 7.86 5.8 7.72 6.03C7.59 6.26 7.4 6.5 7.16 6.74L6.4 7.53C6.29 7.64 6.24 7.77 6.24 7.93C6.24 8.01 6.25 8.08 6.27 8.16C6.3 8.24 6.33 8.3 6.35 8.36C6.53 8.69 6.84 9.12 7.28 9.64C7.73 10.16 8.21 10.69 8.73 11.22C9.27 11.75 9.79 12.24 10.32 12.69C10.84 13.13 11.27 13.43 11.61 13.61C11.66 13.63 11.72 13.66 11.79 13.69C11.87 13.72 11.95 13.73 12.04 13.73C12.21 13.73 12.34 13.67 12.45 13.56L13.21 12.81C13.46 12.56 13.7 12.37 13.93 12.25C14.16 12.11 14.39 12.04 14.64 12.04C14.83 12.04 15.03 12.08 15.25 12.17C15.47 12.26 15.7 12.39 15.95 12.56L19.26 14.91C19.52 15.09 19.7 15.3 19.81 15.55C19.91 15.8 19.97 16.05 19.97 16.33Z" /></svg>';
				case "email":
					return '<svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18.3346 7.17301V13.958C18.3347 14.6498 18.07 15.3154 17.5949 15.8182C17.1198 16.321 16.4703 16.623 15.7796 16.6622L15.6263 16.6663H4.3763C3.68452 16.6664 3.01894 16.4017 2.51611 15.9266C2.01327 15.4515 1.7113 14.802 1.67214 14.1113L1.66797 13.958V7.17301L9.7113 11.3863C9.80078 11.4332 9.90029 11.4577 10.0013 11.4577C10.1023 11.4577 10.2018 11.4332 10.2913 11.3863L18.3346 7.17301ZM4.3763 3.33301H15.6263C16.2976 3.33293 16.945 3.58217 17.443 4.0324C17.941 4.48262 18.254 5.10174 18.3213 5.76967L10.0013 10.128L1.6813 5.76967C1.7459 5.12827 2.03718 4.53094 2.50276 4.08506C2.96834 3.63918 3.5777 3.374 4.2213 3.33717L4.3763 3.33301H15.6263H4.3763Z" /></svg>';
				case "snils":
					return '<svg viewBox="0 0 15 19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.49961 0.299805V5.4748C7.49961 5.9323 7.68135 6.37106 8.00485 6.69456C8.32835 7.01806 8.76711 7.1998 9.22461 7.1998H14.3996V16.9748C14.3996 17.4323 14.2179 17.8711 13.8944 18.1946C13.5709 18.5181 13.1321 18.6998 12.6746 18.6998H2.32461C1.86711 18.6998 1.42835 18.5181 1.10485 18.1946C0.78135 17.8711 0.599609 17.4323 0.599609 16.9748V2.0248C0.599609 1.56731 0.78135 1.12855 1.10485 0.805046C1.42835 0.481545 1.86711 0.299805 2.32461 0.299805H7.49961ZM8.64961 0.587305V5.4748C8.64961 5.6273 8.71019 5.77356 8.81802 5.88139C8.92586 5.98922 9.07211 6.0498 9.22461 6.0498H14.1121L8.64961 0.587305Z" /></svg>';
				case "diplom":
					return '<svg viewBox="0 0 21 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13 6.19981V0.799805L10.5 0.799805L10.5 15.1998L18 15.1998C18.663 15.1998 19.2989 14.9469 19.7678 14.4969C20.2366 14.0468 20.5 13.4363 20.5 12.7998L20.5 7.99981L14.875 7.99981C14.3777 7.99981 13.9008 7.81016 13.5492 7.4726C13.1975 7.13503 13 6.6772 13 6.19981ZM9.25 15.1998L3 15.1998C2.33696 15.1998 1.70107 14.9469 1.23223 14.4969C0.763392 14.0468 0.5 13.4363 0.5 12.7998L0.5 3.19981C0.5 2.56329 0.763392 1.95284 1.23223 1.50275C1.70107 1.05266 2.33696 0.799805 3 0.799805L9.25 0.799805L9.25 15.1998ZM14.25 6.19981V0.869405C14.5525 0.953405 14.8312 1.1094 15.0575 1.3266L19.9513 6.02461C20.1775 6.24061 20.34 6.50941 20.4262 6.79981L14.875 6.79981C14.7092 6.79981 14.5503 6.73659 14.4331 6.62407C14.3158 6.51155 14.25 6.35894 14.25 6.19981Z" /></svg>';
				case "inn":
					return '<svg viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.30078 11.5004L7.47578 11.5004C7.93328 11.5004 8.37204 11.3186 8.69554 10.9951C9.01904 10.6716 9.20078 10.2329 9.20078 9.77539L9.20078 4.60039L18.9758 4.60039C19.4333 4.60039 19.872 4.78213 20.1955 5.10563C20.519 5.42913 20.7008 5.86789 20.7008 6.32539L20.7008 16.6754C20.7008 17.1329 20.519 17.5716 20.1955 17.8951C19.872 18.2186 19.4333 18.4004 18.9758 18.4004L4.02578 18.4004C3.56828 18.4004 3.12952 18.2187 2.80602 17.8952C2.48252 17.5716 2.30078 17.1329 2.30078 16.6754L2.30078 11.5004ZM2.58828 10.3504L7.47578 10.3504C7.62828 10.3504 7.77453 10.2898 7.88237 10.182C7.9902 10.0741 8.05078 9.92789 8.05078 9.77539L8.05078 4.88789L2.58828 10.3504Z" /></svg>';
				case "company":
					return '<svg viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.7008 9.5L5.30078 9.5L5.30078 12L19.7008 12L19.7008 4.5C19.7008 3.83696 19.4479 3.20108 18.9978 2.73223C18.5478 2.26339 17.9373 2 17.3008 2L12.5008 2L12.5008 7.625C12.5008 8.12228 12.3111 8.5992 11.9736 8.95083C11.636 9.30246 11.1782 9.5 10.7008 9.5ZM19.7008 13.25L19.7008 19.5C19.7008 20.163 19.4479 20.7989 18.9978 21.2678C18.5478 21.7366 17.9373 22 17.3008 22L7.70078 22C7.06426 22 6.45381 21.7366 6.00372 21.2678C5.55364 20.7989 5.30078 20.163 5.30078 19.5L5.30078 13.25L19.7008 13.25ZM10.7008 8.25L5.37038 8.25C5.45438 7.9475 5.61038 7.66875 5.82758 7.4425L10.5256 2.54875C10.7416 2.3225 11.0104 2.16 11.3008 2.07375L11.3008 7.625C11.3008 7.79076 11.2376 7.94973 11.125 8.06694C11.0125 8.18415 10.8599 8.25 10.7008 8.25Z" /></svg>';
				default:
					return "";	
			}
		},

		insertLocationProperty: function(property, propsItemNode, disabled)
		{
			console.log('insertLocationProperty');
			var propRow, propNodes, locationString, currentLocation, insertedLoc, propContainer, i, k, values = [];

			if (property.getId() in this.locations)
			{
				if (disabled)
				{
					propRow = this.propsHiddenBlockNode.querySelector('[data-property-id-row="' + property.getId() + '"]');
					if (propRow)
					{
						propNodes = propRow.querySelectorAll('div.bx-soa-loc');
						for (i = 0; i < propNodes.length; i++)
						{
							locationString = this.getLocationString(propNodes[i]);
							values.push(locationString.length ? BX.util.htmlspecialchars(locationString) : BX.message('SOA_NOT_SELECTED'));
						}
					}
					propsItemNode.innerHTML += values.join('<br>');
				}
				else
				{
					propContainer = BX.create('DIV', {props: {className: 'soa-property-container'}});
					propRow = this.locations[property.getId()];
					for (i = 0; i < propRow.length; i ++)
					{
						currentLocation = propRow[i] ? propRow[i].output : {};
						insertedLoc = BX.create('DIV', {props: {className: 'bx-soa-loc'}, html: currentLocation.HTML});

						if (property.isMultiple())
							insertedLoc.style.marginBottom = this.locationsTemplate == 'search' ? '5px' : '20px';

						propContainer.appendChild(insertedLoc);

						for (k in currentLocation.SCRIPT)
						{
							if (currentLocation.SCRIPT.hasOwnProperty(k))
								BX.evalGlobal(currentLocation.SCRIPT[k].JS);
						}
					}

					if (property.isMultiple())
					{
						propContainer.appendChild(
							BX.create('DIV', {
								attrs: {'data-prop-id': property.getId()},
								props: {className: 'btn btn-sm btn-default'},
								text: BX.message('ADD_DEFAULT'),
								events: {
									click: BX.proxy(this.addLocationProperty, this)
								}
							})
						);
					}

					propsItemNode.appendChild(propContainer);
				}
			}
		},

		addLocationProperty: function(e)
		{
			console.log('addLocationProperty');
			var target = e.target || e.srcElement,
				propId = target.getAttribute('data-prop-id'),
				lastProp = BX.previousSibling(target),
				insertedLoc, k, input, index = 0,
				prefix = 'sls-',
				randomStr = BX.util.getRandomString(5);

			if (BX.hasClass(lastProp, 'bx-soa-loc'))
			{
				if (this.locationsTemplate == 'search')
				{
					input = lastProp.querySelector('input[type=text][class=dropdown-field]');
					if (input)
						index = parseInt(input.name.substring(input.name.indexOf('[') + 1, input.name.indexOf(']'))) + 1;
				}
				else
				{
					input = lastProp.querySelectorAll('input[type=hidden]');
					if (input.length)
					{
						input = input[input.length - 1];
						index = parseInt(input.name.substring(input.name.indexOf('[') + 1, input.name.indexOf(']'))) + 1;
					}
				}
			}

			if (this.cleanLocations[propId])
			{
				insertedLoc = BX.create('DIV', {
					props: {className: 'bx-soa-loc'},
					style: {marginBottom: this.locationsTemplate == 'search' ? '5px' : '20px'},
					html: this.cleanLocations[propId].HTML.split('#key#').join(index).replace(/sls-\d{5}/g, prefix + randomStr)
				});
				target.parentNode.insertBefore(insertedLoc, target);

				BX.saleOrderAjax.addPropertyDesc({
					id: propId + '_' + index,
					attributes: {
						id: propId + '_' + index,
						type: 'LOCATION',
						valueSource: 'form'
					}
				});


				for (k in this.cleanLocations[propId].SCRIPT)
					if (this.cleanLocations[propId].SCRIPT.hasOwnProperty(k))
						BX.evalGlobal(this.cleanLocations[propId].SCRIPT[k].JS.split('_key__').join('_' + index).replace(/sls-\d{5}/g, prefix + randomStr));

				BX.saleOrderAjax.initDeferredControl();
			}
		},

		insertDateProperty: function(property, propsItemNode, disabled)
		{
			console.log('insertDateProperty');
			var prop, dateInputs, values, i,
				propContainer, inputText;

			if (disabled)
			{
				prop = this.propsHiddenBlockNode.querySelector('div[data-property-id-row="' + property.getId() + '"]');
				if (prop)
				{
					values = [];
					dateInputs = prop.querySelectorAll('input[type=text]');

					for (i = 0; i < dateInputs.length; i++)
						if (dateInputs[i].value && dateInputs[i].value.length)
							values.push(dateInputs[i].value);

					propsItemNode.innerHTML += this.valuesToString(values);
				}
			}
			else
			{
				propContainer = BX.create('DIV', {props: {className: 'soa-property-container'}});
				property.appendTo(propContainer);
				propsItemNode.appendChild(propContainer);
				inputText = propContainer.querySelectorAll('input[type=text]');

				for (i = 0; i < inputText.length; i++)
					this.alterDateProperty(property.getSettings(), inputText[i]);

				this.alterProperty(property.getSettings(), propContainer);
				this.bindValidation(property.getId(), propContainer);
			}
		},

		insertFileProperty: function(property, propsItemNode)
		{
			var propertyDesc = property.getDescription() || '';
			var prop, fileLinks, values, i, html,
				saved, propContainer;
			var input = BX.create("INPUT", {
				attrs:{
					type:"file",
					name:"ORDER_PROP_"+property.getId(),
					placeholder: propertyDesc
				}
			});
			propsItemNode.appendChild(input);
			propsItemNode.style.display = "flex";
			this.alterProperty(property.getSettings(), propsItemNode);

		},

		insertStringProperty: function(property, propsItemNode)
		{
			var propertyDesc = property.getDescription() || '';
			var prop, inputs, values, i, propContainer;
			property.appendTo(propsItemNode);
			propsItemNode.querySelector("input").setAttribute("placeholder", propertyDesc);
			this.alterProperty(property.getSettings(), propsItemNode);
			this.bindValidation(property.getId(), propsItemNode);
		},

		insertEnumProperty: function(property, propsItemNode, disabled)
		{
			console.log('insertEnumProperty');
			var prop, inputs, values, i, propContainer;

			if (disabled)
			{
				prop = this.propsHiddenBlockNode.querySelector('div[data-property-id-row="' + property.getId() + '"]');
				if (prop)
				{
					values = [];
					inputs = prop.querySelectorAll('input[type=radio]');
					if (inputs.length)
					{
						for (i = 0; i < inputs.length; i++)
						{
							if (inputs[i].checked)
								values.push(inputs[i].nextSibling.nodeValue);
						}
					}
					inputs = prop.querySelectorAll('option');
					if (inputs.length)
					{
						for (i = 0; i < inputs.length; i++)
						{
							if (inputs[i].selected)
								values.push(inputs[i].innerHTML);
						}
					}

					propsItemNode.innerHTML += this.valuesToString(values);
				}
			}
			else
			{
				propContainer = BX.create('DIV', {props: {className: 'soa-property-container'}});
				property.appendTo(propContainer);
				propsItemNode.appendChild(propContainer);
				this.bindValidation(property.getId(), propContainer);
			}
		},

		insertYNProperty: function(property, propsItemNode, disabled)
		{
			console.log('insertYNProperty');
			var prop, inputs, values, i, propContainer;

			if (disabled)
			{
				prop = this.propsHiddenBlockNode.querySelector('div[data-property-id-row="' + property.getId() + '"]');
				if (prop)
				{
					values = [];
					inputs = prop.querySelectorAll('input[type=checkbox]');

					for (i = 0; i < inputs.length; i+=2)
						values.push(inputs[i].checked ? BX.message('SOA_YES') : BX.message('SOA_NO'));

					propsItemNode.innerHTML += this.valuesToString(values);
				}
			}
			else
			{
				propContainer = BX.create('DIV', {props: {className: 'soa-property-container'}});
				property.appendTo(propContainer);
				propsItemNode.appendChild(propContainer);
				this.alterProperty(property.getSettings(), propContainer);
				this.bindValidation(property.getId(), propContainer);
			}
		},

		insertNumberProperty: function(property, propsItemNode, disabled)
		{
			console.log('insertNumberProperty');
			var prop, inputs, values, i, propContainer;

			if (disabled)
			{
				prop = this.propsHiddenBlockNode.querySelector('div[data-property-id-row="' + property.getId() + '"]');
				if (prop)
				{
					values = [];
					inputs = prop.querySelectorAll('input[type=text]');

					for (i = 0; i < inputs.length; i++)
						if (inputs[i].value.length)
							values.push(inputs[i].value);

					propsItemNode.innerHTML += this.valuesToString(values);
				}
			}
			else
			{
				propContainer = BX.create('DIV', {props: {className: 'soa-property-container'}});
				property.appendTo(propContainer);
				propsItemNode.appendChild(propContainer);
				this.alterProperty(property.getSettings(), propContainer);
				this.bindValidation(property.getId(), propContainer);
			}
		},

		valuesToString: function(values)
		{
			console.log('valuesToString');
			var str = values.join(', ');

			return str.length ? BX.util.htmlspecialchars(str) : BX.message('SOA_NOT_SELECTED');
		},

		alterProperty: function(settings, propContainer)
		{
			console.log('alterProperty');
			var divs = BX.findChildren(propContainer, {tagName: 'DIV'}),
				i, textNode, inputs, del, add,
				fileInputs, accepts, fileTitles;
			textNode = propContainer.querySelector('input[type=text]');
			if (textNode)
			{
				textNode.id = 'soa-property-' + settings.ID;
				if (settings.IS_ADDRESS == 'Y')
					textNode.setAttribute('autocomplete', 'address');
				if (settings.IS_EMAIL == 'Y')
					textNode.setAttribute('autocomplete', 'email');
				if (settings.IS_PAYER == 'Y')
					textNode.setAttribute('autocomplete', 'name');
				if (settings.IS_PHONE == 'Y')
					textNode.setAttribute('autocomplete', 'tel');

				if (settings.PATTERN && settings.PATTERN.length)
				{
					textNode.removeAttribute('pattern');
				}
				textNode.setAttribute('class', "calculate__input calculate__input--text");
			}
			
			if (settings.TYPE == 'FILE')
			{
				if (settings.ACCEPT && settings.ACCEPT.length)
				{
					fileInputs = propContainer.querySelectorAll('input[type=file]');
					accepts = this.getFileAccepts(settings.ACCEPT);
					for (i = 0; i < fileInputs.length; i++)
						fileInputs[i].setAttribute('accept', accepts);
				}

				fileTitles = propContainer.querySelectorAll('a');
				for (i = 0; i < fileTitles.length; i++)
				{
					BX.bind(fileTitles[i], 'click', function(e){
						var target = e.target || e.srcElement,
							fileInput = target && target.nextSibling && target.nextSibling.nextSibling;

						if (fileInput)
							BX.fireEvent(fileInput, 'change');
					});
				}
			}

		},

		alterDateProperty: function(settings, inputText)
		{
			console.log('alterDateProperty');
			var parentNode = BX.findParent(inputText, {tagName: 'DIV'}),
				addon;

			BX.addClass(parentNode, 'input-group');
			addon = BX.create('DIV', {
				props: {className: 'input-group-addon'},
				children: [BX.create('I', {props: {className: 'bx-calendar'}})]
			});
			BX.insertAfter(addon, inputText);
			BX.remove(parentNode.querySelector('input[type=button]'));
			BX.bind(addon, 'click', BX.delegate(function(e){
				var target = e.target || e.srcElement,
					parentNode = BX.findParent(target, {tagName: 'DIV', className: 'input-group'});

				BX.calendar({
					node: parentNode.querySelector('.input-group-addon'),
					field: parentNode.querySelector('input[type=text]').name,
					form: '',
					bTime: settings.TIME == 'Y',
					bHideTime: false
				});
			}, this));
		},

		isValidForm: function()
		{
			console.log('isValidForm');
			if (!this.options.propertyValidation)
				return true;

			var regionErrors = this.isValidRegionBlock(),
				propsErrors = this.isValidPropertiesBlock(),
				navigated = false, tooltips, i;

			if (regionErrors.length)
			{
				navigated = true;
				this.animateScrollTo(this.regionBlockNode, 800, 50);
			}

			if (propsErrors.length && !navigated)
			{
				if (this.activeSectionId == this.propsBlockNode.id)
				{
					tooltips = this.propsBlockNode.querySelectorAll('div.tooltip');
					for (i = 0; i < tooltips.length; i++)
					{
						if (tooltips[i].getAttribute('data-state') == 'opened')
						{
							this.animateScrollTo(BX.findParent(tooltips[i], {className: 'form-group bx-soa-customer-field'}), 800, 50);
							break;
						}
					}
				}
				else{
					this.animateScrollTo(this.propsBlockNode, 800, 50);
					BX('notice').querySelector('.notice__text').innerHTML = propsErrors[0];
					BX.addClass(BX('notice'), 'open');
			        if (!this.noticeCloseDuration) {
			            this.noticeCloseDuration = true;
			            this.noticeClose = setTimeout(function () {
			            	BX.removeClass(BX('notice'), "open");
			                this.noticeCloseDuration = false;
			            }, 2000);
			        }
				}
			}

			if (regionErrors.length)
			{
				this.showError(this.regionBlockNode, regionErrors);
				BX.addClass(this.regionBlockNode, 'bx-step-error');
			}

			if (propsErrors.length)
			{
				if (this.activeSectionId !== this.propsBlockNode.id)
					this.showError(this.propsBlockNode, propsErrors);

				BX.addClass(this.propsBlockNode, 'bx-step-error');
			}

			return !(regionErrors.length + propsErrors.length);
		},

		isValidRegionBlock: function()
		{
			console.log('isValidRegionBlock');
			if (!this.options.propertyValidation)
				return [];

			var regionProps = this.orderBlockNode.querySelectorAll('.bx-soa-location-input-container[data-property-id-row]'),
				regionErrors = [],
				id, arProperty, data, i;

			for (i = 0; i < regionProps.length; i++)
			{
				id = regionProps[i].getAttribute('data-property-id-row');
				arProperty = this.validation.properties[id];
				data = this.getValidationData(arProperty, regionProps[i]);

				regionErrors = regionErrors.concat(this.isValidProperty(data, true));
			}

			return regionErrors;
		},

		isValidPropertiesBlock: function(excludeLocation)
		{
			console.log('isValidPropertiesBlock');
			if (!this.options.propertyValidation)
				return [];

			var props = this.orderBlockNode.querySelectorAll('.calculate__label-input[data-property-id-row]'),
				propsErrors = [],
				id, propContainer, arProperty, data, i;
			for (i = 0; i < props.length; i++)
			{
				id = props[i].getAttribute('data-property-id-row');

				if (!!excludeLocation && this.locations[id])
					continue;

				arProperty = this.validation.properties[id];
				data = this.getValidationData(arProperty, props[i]);
				propsErrors = propsErrors.concat(this.isValidProperty(data, true));
			}

			return propsErrors;
		},

		isValidProperty: function(data, fieldName)
		{
			console.log('isValidProperty');
			var propErrors = [], inputErrors, i;

			if (!data || !data.inputs)
				return propErrors;

			for (i = 0; i < data.inputs.length; i++)
			{
				inputErrors = data.func(data.inputs[i], !!fieldName);
				if (inputErrors.length)
					propErrors[i] = inputErrors.join('<br>');
			}

			this.showValidationResult(data.inputs, propErrors);

			return propErrors;
		},

		bindValidation: function(id, propContainer)
		{
			console.log('bindValidation');
			if (!this.validation.properties || !this.validation.properties[id])
				return;
			var arProperty = this.validation.properties[id],
				data = this.getValidationData(arProperty, propContainer),
				i, k;
			if (data && data.inputs && data.action)
			{
				for (i = 0; i < data.inputs.length; i++)
				{
					if (BX.type.isElementNode(data.inputs[i])){
						BX.bind(data.inputs[i], data.action, BX.delegate(function(){
							this.isValidProperty(data);
						}, this));
						BX.bind(data.inputs[i], "keyup", BX.delegate(function(){
							this.hasText(data);
						}, this));
					}
					else
						for (k = 0; k < data.inputs[i].length; k++)
							BX.bind(data.inputs[i][k], data.action, BX.delegate(function(){
								this.isValidProperty(data);
							}, this));
				}
			}
		},

		hasText: function(data)
		{
			console.log('hasText');
			let input = data.inputs[0];
			let label = input.closest(".calculate__label-input");
			let className = label.getAttribute("class");
			className = className.replace(/ required/, "");
			if(BX(input.id).value === ""){
				label.setAttribute("class", className+" required");
			}
			else{
				label.setAttribute("class", className);
			}
		},

		getValidationData: function(arProperty, propContainer)
		{
			console.log('getValidationData');
			if (!arProperty || !propContainer)
				return;

			var data = {}, inputs;

			switch (arProperty.TYPE)
			{
				case 'STRING':
					data.action = 'change';
					data.func = BX.delegate(function(input, fieldName){
						return this.validateString(input, arProperty, fieldName);
					}, this);

					inputs = propContainer.querySelectorAll('input[type=text]');
					if (inputs.length)
					{
						data.inputs = inputs;
						break;
					}
					inputs = propContainer.querySelectorAll('textarea');
					if (inputs.length)
						data.inputs = inputs;
					break;
				case 'LOCATION':
					data.func = BX.delegate(function(input, fieldName){
						return this.validateLocation(input, arProperty, fieldName);
					}, this);

					inputs = propContainer.querySelectorAll('input.bx-ui-sls-fake[type=text]');
					if (inputs.length)
					{
						data.inputs = inputs;
						data.action = 'keyup';
						break;
					}
					inputs = propContainer.querySelectorAll('div.bx-ui-slst-pool');
					if (inputs.length)
					{
						data.inputs = inputs;
					}
					break;
				case 'Y/N':
					data.inputs = propContainer.querySelectorAll('input[type=checkbox]');
					data.action = 'change';
					data.func = BX.delegate(function(input, fieldName){
						return this.validateCheckbox(input, arProperty, fieldName);
					}, this);
					break;
				case 'NUMBER':
					data.inputs = propContainer.querySelectorAll('input[type=text]');
					data.action = 'blur';
					data.func = BX.delegate(function(input, fieldName){
						return this.validateNumber(input, arProperty, fieldName);
					}, this);
					break;
				case 'ENUM':
					inputs = propContainer.querySelectorAll('input[type=radio]');
					if (!inputs.length)
						inputs = propContainer.querySelectorAll('input[type=checkbox]');

					if (inputs.length)
					{
						data.inputs = [inputs];
						data.action = 'change';
						data.func = BX.delegate(function(input, fieldName){
							return this.validateEnum(input, arProperty, fieldName);
						}, this);
						break;
					}

					inputs = propContainer.querySelectorAll('option');
					if (inputs.length)
					{
						data.inputs = [inputs];
						data.action = 'click';
						data.func = BX.delegate(function(input, fieldName){
							return this.validateSelect(input, arProperty, fieldName);
						}, this);
					}
					break;
				case 'FILE':
					data.inputs = propContainer.querySelectorAll('input[type=file]');
					data.action = 'change';
					data.func = BX.delegate(function(input, fieldName){
						return this.validateFile(input, arProperty, fieldName);
					}, this);
					break;
				case 'DATE':
					data.inputs = propContainer.querySelectorAll('input[type=text]');
					data.action = 'change';
					data.func = BX.delegate(function(input, fieldName){
						return this.validateDate(input, arProperty, fieldName);
					}, this);
					break;
			}

			return data;
		},

		showErrorTooltip: function(tooltipId, targetNode, text)
		{
			console.log('showErrorTooltip');
			if (!tooltipId || !targetNode || !text)
				return;

			var tooltip = BX('tooltip-' + tooltipId),
				tooltipInner, quickLocation;

			text = this.uniqueText(text, '<br>');

			if (tooltip)
			{
				tooltipInner = tooltip.querySelector('div.tooltip-inner');
			}
			else
			{
				tooltipInner = BX.create('DIV', {props: {className: 'tooltip-inner'}});
				tooltip = BX.create('DIV', {
					props: {
						id: 'tooltip-' + tooltipId,
						className: 'bx-soa-tooltip bx-soa-tooltip-static bx-soa-tooltip-danger tooltip top'
					},
					children: [
						BX.create('DIV', {props: {className: 'tooltip-arrow'}}),
						tooltipInner
					]
				});

				quickLocation = targetNode.parentNode.querySelector('div.quick-locations');
				if (quickLocation)
					targetNode = quickLocation;

				BX.insertAfter(tooltip, targetNode);
			}

			tooltipInner.innerHTML = text;

			if (tooltip.getAttribute('data-state') != 'opened')
			{
				tooltip.setAttribute('data-state', 'opened');
				tooltip.style.opacity = 0;
				tooltip.style.display = 'block';

				new BX.easing({
					duration: 150,
					start: {opacity: 0},
					finish: {opacity: 100},
					transition: BX.easing.transitions.quad,
					step: function(state){
						tooltip.style.opacity = state.opacity / 100;
					}
				}).animate();
			}
		},

		closeErrorTooltip: function(tooltipId)
		{
			console.log('closeErrorTooltip');
			var tooltip = BX('tooltip-' + tooltipId);
			if (tooltip)
			{
				tooltip.setAttribute('data-state', 'closed');

				new BX.easing({
					duration: 150,
					start: {opacity: 100},
					finish: {opacity: 0},
					transition: BX.easing.transitions.quad,
					step: function(state){
						tooltip.style.opacity = state.opacity / 100;
					},
					complete: function(){
						tooltip.style.display = 'none';
					}
				}).animate();
			}
		},

		showValidationResult: function(inputs, errors)
		{
			console.log('showValidationResult');
			if (!inputs || !inputs.length || !errors)
				return;

			for (let i = 0; i < inputs.length; i++)
			{
				if(errors[i]){
					let label = inputs[i].closest('.calculate__label-input');
					let className = label.getAttribute("class");
					className += " required";
					label.setAttribute("class", className);
				}
			}

		},

		validateString: function(input, arProperty, fieldName)
		{
			console.log('validateString');
			if (!input || !arProperty)
				return [];

			var value = input.value,
				errors = [],
				name = BX.util.htmlspecialchars(arProperty.NAME),
				field = !!fieldName ? BX.message('SOA_FIELD') + ' "' + name + '"' : BX.message('SOA_FIELD'),
				re;

			if (arProperty.MULTIPLE === 'Y')
				return errors;

			if (arProperty.REQUIRED === 'Y' && value.length === 0)
				errors.push(field + ' ' + BX.message('SOA_REQUIRED'));

			if (value.length)
			{
				if (arProperty.MINLENGTH && arProperty.MINLENGTH > value.length)
					errors.push(BX.message('SOA_MIN_LENGTH') + ' "' + name + '" ' + BX.message('SOA_LESS') + ' ' + arProperty.MINLENGTH + ' ' + BX.message('SOA_SYMBOLS'));

				if (arProperty.MAXLENGTH && arProperty.MAXLENGTH < value.length)
					errors.push(BX.message('SOA_MAX_LENGTH') + ' "' + name + '" ' + BX.message('SOA_MORE') + ' ' + arProperty.MAXLENGTH + ' ' + BX.message('SOA_SYMBOLS'));

				if (arProperty.IS_EMAIL === 'Y')
				{
					input.value = value = BX.util.trim(value);
					if (value.length)
					{
						re = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
						if (!re.test(value))
						{
							errors.push(BX.message('SOA_INVALID_EMAIL'));
						}
					}
				}

				if (value.length > 0 && arProperty.PATTERN && arProperty.PATTERN.length)
				{
					re = new RegExp(arProperty.PATTERN);
					if (!re.test(value))
						errors.push(field + ' ' + BX.message('SOA_INVALID_PATTERN'));
				}
			}

			return errors;
		},

		validateLocation: function(input, arProperty, fieldName)
		{
			console.log('validateLocation');
			if (!input || !arProperty)
				return [];

			var parent = BX.findParent(input, {tagName: 'DIV', className: 'form-group'}),
				value = this.getLocationString(parent),
				errors = [],
				field = !!fieldName ? BX.message('SOA_FIELD') + ' "' + BX.util.htmlspecialchars(arProperty.NAME) + '"' : BX.message('SOA_FIELD');

			if (arProperty.MULTIPLE == 'Y' && arProperty.IS_LOCATION !== 'Y')
				return errors;

			if (arProperty.REQUIRED == 'Y' && (value.length == 0 || value == BX.message('SOA_NOT_SPECIFIED')))
				errors.push(field + ' ' + BX.message('SOA_REQUIRED'));

			return errors;
		},

		validateCheckbox: function(input, arProperty, fieldName)
		{
			console.log('validateCheckbox');
			if (!input || !arProperty)
				return [];

			var errors = [],
				field = !!fieldName ? BX.message('SOA_FIELD') + ' "' + BX.util.htmlspecialchars(arProperty.NAME) + '"' : BX.message('SOA_FIELD');

			if (arProperty.MULTIPLE == 'Y')
				return errors;

			if (arProperty.REQUIRED == 'Y' && !input.checked)
				errors.push(field + ' ' + BX.message('SOA_REQUIRED'));

			return errors;
		},

		validateNumber: function(input, arProperty, fieldName)
		{
			console.log('validateNumber');
			if (!input || !arProperty)
				return [];

			var value = input.value,
				errors = [],
				name = BX.util.htmlspecialchars(arProperty.NAME),
				field = !!fieldName ? BX.message('SOA_FIELD') + ' "' + name + '"' : BX.message('SOA_FIELD'),
				num, del;

			if (arProperty.MULTIPLE == 'Y')
				return errors;

			if (arProperty.REQUIRED == 'Y' && value.length == 0)
				errors.push(field + ' ' + BX.message('SOA_REQUIRED'));

			if (value.length)
			{
				if (!/[0-9]|\./.test(value))
					errors.push(field + ' ' + BX.message('SOA_NOT_NUMERIC'));

				if (arProperty.MIN && parseFloat(arProperty.MIN) > parseFloat(value))
					errors.push(BX.message('SOA_MIN_VALUE') + ' "' + name + '" ' + parseFloat(arProperty.MIN));

				if (arProperty.MAX && parseFloat(arProperty.MAX) < parseFloat(value))
					errors.push(BX.message('SOA_MAX_VALUE') + ' "' + name + '" ' + parseFloat(arProperty.MAX));

				if (arProperty.STEP && parseFloat(arProperty.STEP) > 0)
				{
					num = Math.abs(parseFloat(value) - (arProperty.MIN && parseFloat(arProperty.MIN) > 0 ? parseFloat(arProperty.MIN) : 0));
					del = (num / parseFloat(arProperty.STEP)).toPrecision(12);
					if (del != parseInt(del))
						errors.push(field + ' ' + BX.message('SOA_NUM_STEP') + ' ' + arProperty.STEP);
				}
			}

			return errors;
		},

		validateEnum: function(inputs, arProperty, fieldName)
		{
			console.log('validateEnum');
			if (!inputs || !arProperty)
				return [];

			var values = [], errors = [], i,
				field = !!fieldName ? BX.message('SOA_FIELD') + ' "' + BX.util.htmlspecialchars(arProperty.NAME) + '"' : BX.message('SOA_FIELD');

			if (arProperty.MULTIPLE == 'Y')
				return errors;

			for (i = 0; i < inputs.length; i++)
				if (inputs[i].checked || inputs[i].selected)
					values.push(i);

			if (arProperty.REQUIRED == 'Y' && values.length == 0)
				errors.push(field + ' ' + BX.message('SOA_REQUIRED'));

			return errors;
		},

		validateSelect: function(inputs, arProperty, fieldName)
		{
			console.log('validateSelect');
			if (!inputs || !arProperty)
				return [];

			var values = [], errors = [], i,
				field = !!fieldName ? BX.message('SOA_FIELD') + ' "' + BX.util.htmlspecialchars(arProperty.NAME) + '"' : BX.message('SOA_FIELD');

			if (arProperty.MULTIPLE == 'Y')
				return errors;

			for (i = 0; i < inputs.length; i++)
				if (inputs[i].selected)
					values.push(i);

			if (arProperty.REQUIRED == 'Y' && values.length == 0)
				errors.push(field + ' ' + BX.message('SOA_REQUIRED'));

			return errors;
		},

		validateFile: function(inputs, arProperty, fieldName)
		{
			console.log('validateFile');
			if (!inputs || !arProperty)
				return [];

			var errors = [], i,
				files = inputs.files || [],
				field = !!fieldName ? BX.message('SOA_FIELD') + ' "' + BX.util.htmlspecialchars(arProperty.NAME) + '"' : BX.message('SOA_FIELD'),
				defaultValue = inputs.previousSibling.value,
				file, fileName, splittedName, fileExtension;

			if (arProperty.MULTIPLE == 'Y')
				return errors;

			if (
				arProperty.REQUIRED == 'Y' && files.length == 0 && defaultValue == ''
				&& (!arProperty.DEFAULT_VALUE || !arProperty.DEFAULT_VALUE.length)
			)
			{
				errors.push(field + ' ' + BX.message('SOA_REQUIRED'));
			}
			else
			{
				for (i = 0; i < files.length; i++)
				{
					file = files[i];
					fileName = BX.util.htmlspecialchars(file.name);
					splittedName = file.name.split('.');
					fileExtension = splittedName.length > 1 ? splittedName[splittedName.length - 1].toLowerCase() : '';

					if (arProperty.ACCEPT.length > 0 && (fileExtension.length == 0 || arProperty.ACCEPT.indexOf(fileExtension) == '-1'))
						errors.push(BX.message('SOA_BAD_EXTENSION') + ' "' + fileName + '" (' + BX.util.htmlspecialchars(arProperty.ACCEPT) + ')');

					if (file.size > parseInt(arProperty.MAXSIZE))
						errors.push(BX.message('SOA_MAX_SIZE') + ' "' + fileName + '" (' + this.getSizeString(arProperty.MAXSIZE, 1) + ')');
				}
			}

			return errors;
		},

		validateDate: function(input, arProperty, fieldName)
		{
			console.log('validateDate');
			if (!input || !arProperty)
				return [];

			var value = input.value,
				errors = [],
				name = BX.util.htmlspecialchars(arProperty.NAME),
				field = !!fieldName ? BX.message('SOA_FIELD') + ' "' + name + '"' : BX.message('SOA_FIELD');

			if (arProperty.MULTIPLE == 'Y')
				return errors;

			if (arProperty.REQUIRED == 'Y' && value.length == 0)
				errors.push(field + ' ' + BX.message('SOA_REQUIRED'));

			return errors;
		},

		editPropsMap: function(propsNode)
		{
			console.log('editPropsMap');
			var propsMapContainer = BX.create('DIV', {props: {className: 'col-sm-12'}, style: {marginBottom: '10px'}}),
				map = BX.create('DIV', {props: {id: 'propsMap'}, style: {width: '100%'}});

			propsMapContainer.appendChild(map);
			propsNode.appendChild(propsMapContainer);
		},

		editPropsComment: function(propsNode)
		{
			console.log('editPropsComment');
			var propsCommentContainer, label, input, div;

			propsCommentContainer = BX.create('DIV', {props: {className: 'col-sm-12'}});
			label = BX.create('LABEL', {
				attrs: {for: 'orderDescription'},
				props: {className: 'bx-soa-customer-label'},
				html: this.params.MESS_ORDER_DESC
			});
			input = BX.create('TEXTAREA', {
				props: {
					id: 'orderDescription',
					cols: '4',
					className: 'form-control bx-soa-customer-textarea bx-ios-fix',
					name: 'ORDER_DESCRIPTION'
				},
				text: this.result.ORDER_DESCRIPTION ? this.result.ORDER_DESCRIPTION : ''
			});
			div = BX.create('DIV', {
				props: {className: 'form-group bx-soa-customer-field'},
				children: [label, input]
			});

			propsCommentContainer.appendChild(div);
			propsNode.appendChild(propsCommentContainer);
		},

		editTotalBlock: function()
		{
			console.log('editTotalBlock');
			var total = BX("bx-soa-total"),
				totalPriceNode = total.querySelector('.calculate__total-sum--bold');
			totalPriceNode.innerHTML = this.result.TOTAL.ORDER_TOTAL_PRICE_FORMATED; 
			/*if (!this.totalInfoBlockNode || !this.result.TOTAL)
				return;

			var total = this.result.TOTAL,
				priceHtml, params = {},
				discText, valFormatted, i,
				showOrderButton = this.params.SHOW_TOTAL_ORDER_BUTTON === 'Y';

			BX.cleanNode(this.totalInfoBlockNode);

			if (parseFloat(total.ORDER_PRICE) === 0)
			{
				priceHtml = this.params.MESS_PRICE_FREE;
				params.free = true;
			}
			else
			{
				priceHtml = total.ORDER_PRICE_FORMATED;
			}

			if (this.options.showPriceWithoutDiscount)
			{
				priceHtml += '<br><span class="bx-price-old">' + total.PRICE_WITHOUT_DISCOUNT + '</span>';
			}

			this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_SUMMARY'), priceHtml, params));

			if (this.options.showOrderWeight)
			{
				this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_WEIGHT_SUM'), total.ORDER_WEIGHT_FORMATED));
			}

			if (this.options.showTaxList)
			{
				for (i = 0; i < total.TAX_LIST.length; i++)
				{
					valFormatted = total.TAX_LIST[i].VALUE_MONEY_FORMATED || '';
					this.totalInfoBlockNode.appendChild(
						this.createTotalUnit(
							total.TAX_LIST[i].NAME + (!!total.TAX_LIST[i].VALUE_FORMATED ? ' ' + total.TAX_LIST[i].VALUE_FORMATED : '') + ':',
							valFormatted
						)
					);
				}
			}

			params = {};

			if (this.options.showDiscountPrice)
			{
				discText = this.params.MESS_ECONOMY;
				if (total.DISCOUNT_PERCENT_FORMATED && parseFloat(total.DISCOUNT_PERCENT_FORMATED) > 0)
					discText += total.DISCOUNT_PERCENT_FORMATED;

				this.totalInfoBlockNode.appendChild(this.createTotalUnit(discText + ':', total.DISCOUNT_PRICE_FORMATED, {highlighted: true}));
			}

			if (this.options.showPayedFromInnerBudget)
			{
				this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_IT'), total.ORDER_TOTAL_PRICE_FORMATED));
				this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_PAYED'), total.PAYED_FROM_ACCOUNT_FORMATED));
				this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_LEFT_TO_PAY'), total.ORDER_TOTAL_LEFT_TO_PAY_FORMATED, {total: true}));
			}
			else
			{
				this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_IT'), total.ORDER_TOTAL_PRICE_FORMATED, {total: true}));
			}

			if (parseFloat(total.PAY_SYSTEM_PRICE) >= 0 && this.result.DELIVERY.length)
			{
				this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_PAYSYSTEM_PRICE'), '~' + total.PAY_SYSTEM_PRICE_FORMATTED));
			}

			if (!this.result.SHOW_AUTH)
			{
				this.totalInfoBlockNode.appendChild(
					BX.create('DIV', {
						props: {className: 'bx-soa-cart-total-button-container' + (!showOrderButton ? ' visible-xs' : '')},
						children: [
							BX.create('A', {
								props: {
									href: 'javascript:void(0)',
									className: 'btn btn-default btn-lg btn-order-save'
								},
								html: this.params.MESS_ORDER,
								events: {
									click: BX.proxy(this.clickOrderSaveAction, this)
								}
							})

						]
					})
				);
			}

			this.editMobileTotalBlock();*/
		},

		editMobileTotalBlock: function()
		{
			console.log('editMobileTotalBlock');
			if (this.result.SHOW_AUTH)
				BX.removeClass(this.mobileTotalBlockNode, 'visible-xs');
			else
				BX.addClass(this.mobileTotalBlockNode, 'visible-xs');

			BX.cleanNode(this.mobileTotalBlockNode);
			this.mobileTotalBlockNode.appendChild(this.totalInfoBlockNode.cloneNode(true));
			BX.bind(this.mobileTotalBlockNode.querySelector('a.bx-soa-price-not-calc'), 'click', BX.delegate(function(){
				this.animateScrollTo(this.deliveryBlockNode);
			}, this));
			BX.bind(this.mobileTotalBlockNode.querySelector('a.btn-order-save'), 'click', BX.proxy(this.clickOrderSaveAction, this));
		},

		createTotalUnit: function(name, value, params)
		{
			console.log('createTotalUnit');
			var totalValue, className = 'bx-soa-cart-total-line';

			name = name || '';
			value = value || '';
			params = params || {};

			if (params.error)
			{
				totalValue = [BX.create('A', {
					props: {className: 'bx-soa-price-not-calc'},
					html: value,
					events: {
						click: BX.delegate(function(){
							this.animateScrollTo(this.deliveryBlockNode);
						}, this)
					}
				})];
			}
			else if (params.free)
			{
				totalValue = [BX.create('SPAN', {
					props: {className: 'bx-soa-price-free'},
					html: value
				})];
			}
			else
			{
				totalValue = [value];
			}

			if (params.total)
			{
				className += ' bx-soa-cart-total-line-total';
			}

			if (params.highlighted)
			{
				className += ' bx-soa-cart-total-line-highlighted';
			}

			return BX.create('DIV', {
				props: {className: className},
				children: [
					BX.create('SPAN', {props: {className: 'bx-soa-cart-t'}, text: name}),
					BX.create('SPAN', {
						props: {
							className: 'bx-soa-cart-d' + (!!params.total && this.options.totalPriceChanged ? ' bx-soa-changeCostSign' : '')
						},
						children: totalValue
					})
				]
			});
		},

		basketBlockScrollCheckEvent: function(e)
		{
			console.log('basketBlockScrollCheckEvent');
			var target = e.target || e.srcElement,
				scrollLeft = target.scrollLeft,
				scrollRight = target.scrollWidth - (scrollLeft + target.clientWidth),
				parent = target.parentNode;

			if (scrollLeft == 0)
				BX.removeClass(parent, 'bx-soa-table-fade-left');
			else
				BX.addClass(parent, 'bx-soa-table-fade-left');

			if (scrollRight == 0)
				BX.removeClass(parent, 'bx-soa-table-fade-right');
			else
				BX.addClass(parent, 'bx-soa-table-fade-right');
		},

		basketBlockScrollCheck: function()
		{
			console.log('basketBlockScrollCheck');
			var scrollableNodes = this.orderBlockNode.querySelectorAll('div.bx-soa-table-fade'),
				parentNode, parentWidth, tableNode, tableWidth,
				i, scrollNode, scrollLeft, scrollRight, scrollable = false;

			for (i = 0; i < scrollableNodes.length; i++)
			{
				parentNode = scrollableNodes[i];
				tableNode = parentNode.querySelector('div.bx-soa-item-table');
				parentWidth = parentNode.clientWidth;
				tableWidth = tableNode.clientWidth || 0;
				scrollable = scrollable || tableWidth > parentWidth;

				if (scrollable)
				{
					scrollNode = BX.firstChild(parentNode);
					scrollLeft = scrollNode.scrollLeft;
					scrollRight = scrollNode.scrollWidth - (scrollLeft + scrollNode.clientWidth);

					if (scrollLeft == 0)
						BX.removeClass(parentNode, 'bx-soa-table-fade-left');
					else
						BX.addClass(parentNode, 'bx-soa-table-fade-left');

					if (scrollRight == 0)
						BX.removeClass(parentNode, 'bx-soa-table-fade-right');
					else
						BX.addClass(parentNode, 'bx-soa-table-fade-right');

					if (scrollLeft == 0 && scrollRight == 0)
						BX.addClass(parentNode, 'bx-soa-table-fade-right');
				}
				else
					BX.removeClass(parentNode, 'bx-soa-table-fade-left bx-soa-table-fade-right');
			}
		},

		totalBlockScrollCheck: function()
		{
			console.log('totalBlockScrollCheck');
			if (!this.totalInfoBlockNode || !this.totalGhostBlockNode)
				return;

			var scrollTop = BX.GetWindowScrollPos().scrollTop,
				ghostTop = BX.pos(this.totalGhostBlockNode).top,
				ghostBottom = BX.pos(this.orderBlockNode).bottom,
				width;

			if (ghostBottom - this.totalBlockNode.offsetHeight < scrollTop + 20)
				BX.addClass(this.totalInfoBlockNode, 'bx-soa-cart-total-bottom');
			else
				BX.removeClass(this.totalInfoBlockNode, 'bx-soa-cart-total-bottom');

			if (scrollTop > ghostTop && !BX.hasClass(this.totalInfoBlockNode, 'bx-soa-cart-total-fixed'))
			{
				width = this.totalInfoBlockNode.offsetWidth;
				BX.addClass(this.totalInfoBlockNode, 'bx-soa-cart-total-fixed');
				this.totalGhostBlockNode.style.paddingTop = this.totalInfoBlockNode.offsetHeight + 'px';
				this.totalInfoBlockNode.style.width = width + 'px';
			}
			else if (scrollTop < ghostTop && BX.hasClass(this.totalInfoBlockNode, 'bx-soa-cart-total-fixed'))
			{
				BX.removeClass(this.totalInfoBlockNode, 'bx-soa-cart-total-fixed');
				this.totalGhostBlockNode.style.paddingTop = 0;
				this.totalInfoBlockNode.style.width = '';
			}
		},

		totalBlockResizeCheck: function()
		{
			console.log('totalBlockResizeCheck');
			if (!this.totalInfoBlockNode || !this.totalGhostBlockNode)
				return;

			if (BX.hasClass(this.totalInfoBlockNode, 'bx-soa-cart-total-fixed'))
				this.totalInfoBlockNode.style.width = this.totalGhostBlockNode.offsetWidth + 'px';
		},

		totalBlockFixFont: function()
		{
			console.log('totalBlockFixFont');
			var totalNode = this.totalInfoBlockNode.querySelector('.bx-soa-cart-total-line.bx-soa-cart-total-line-total'),
				buttonNode, target, objList = [];

			if (totalNode)
			{
				target = BX.lastChild(totalNode);
				objList.push({
					node: target,
					maxFontSize: 28,
					smallestValue: false,
					scaleBy: target.parentNode
				});
			}

			if (this.params.SHOW_TOTAL_ORDER_BUTTON == 'Y')
			{
				buttonNode = this.totalInfoBlockNode.querySelector('.bx-soa-cart-total-button-container');
				if (buttonNode)
				{
					target = BX.lastChild(buttonNode);
					objList.push({
						node: target,
						maxFontSize: 18,
						smallestValue: false
					});
				}
			}

			if (objList.length)
				BX.FixFontSize.init({objList: objList, onAdaptiveResize: true});
		},

		setAnalyticsDataLayer: function(action, id)
		{
			console.log('setAnalyticsDataLayer');
			if (!this.params.DATA_LAYER_NAME)
				return;

			var info, i;
			var products = [],
				dataVariant, item;

			for (i in this.result.GRID.ROWS)
			{
				if (this.result.GRID.ROWS.hasOwnProperty(i))
				{
					item = this.result.GRID.ROWS[i];
					dataVariant = [];

					for (i = 0; i < item.data.PROPS.length; i++)
					{
						dataVariant.push(item.data.PROPS[i].VALUE);
					}

					products.push({
						'id': item.data.PRODUCT_ID,
						'name': item.data.NAME,
						'price': item.data.PRICE,
						'brand': (item.data[this.params.BRAND_PROPERTY + '_VALUE'] || '').split(', ').join('/'),
						'variant': dataVariant.join('/'),
						'quantity': item.data.QUANTITY
					});
				}
			}

			switch (action)
			{
				case 'checkout':
					info = {
						'event': 'checkout',
						'ecommerce': {
							'checkout': {
								'products': products
							}
						}
					};
					break;
				case 'purchase':
					info = {
						'event': 'purchase',
						'ecommerce': {
							'purchase': {
								'actionField': {
									'id': id,
									'revenue': this.result.TOTAL.ORDER_TOTAL_PRICE,
									'tax': this.result.TOTAL.TAX_PRICE,
									'shipping': this.result.TOTAL.DELIVERY_PRICE
								},
								'products': products
							}
						}
					};
					break;
			}

			window[this.params.DATA_LAYER_NAME] = window[this.params.DATA_LAYER_NAME] || [];
			window[this.params.DATA_LAYER_NAME].push(info);
		},

		isOrderSaveAllowed: function()
		{
			console.log('isOrderSaveAllowed');
			return this.orderSaveAllowed === true;
		},

		allowOrderSave: function()
		{
			console.log('allowOrderSave');
			this.orderSaveAllowed = true;
		},

		disallowOrderSave: function()
		{
			console.log('disallowOrderSave');
			this.orderSaveAllowed = false;
		},

		initUserConsent: function()
		{
			console.log('initUserConsent');
			BX.ready(BX.delegate(function(){
				var control = BX.UserConsent && BX.UserConsent.load(this.orderBlockNode);
				if (control)
				{
					BX.addCustomEvent(control, BX.UserConsent.events.save, BX.proxy(this.doSaveAction, this));
					BX.addCustomEvent(control, BX.UserConsent.events.refused, BX.proxy(this.disallowOrderSave, this));
				}
			}, this));
		}
	};
})();