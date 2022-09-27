<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
include_once($_SERVER['DOCUMENT_ROOT']."/lib/vendor/autoload.php");
use Bitrix\Main;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Controller\PhoneAuth;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;
use Bitrix\Sale;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\DiscountCouponsManager;
use Bitrix\Sale\Location\GeoIp;
use Bitrix\Sale\Location\LocationTable;
use Bitrix\Sale\Order;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\PersonType;
use Bitrix\Sale\Result;
use Bitrix\Sale\Services\Company;
use Bitrix\Sale\Shipment;
use Bitrix\Main\UserTable;
use YooKassa\Client;
$arResult = [];
require_once('./order_ajax.php');
$arParams = $params;
require_once('./order_result.php');
require_once('./amo/amo.php');

define("U_KASSA", 7);
define("REKVIZITY", 8);

global $APPLICATION;

class OrderHandler
{

	/** @var Order $order */
	protected $order;
	/** @var Sale\Basket\Storage $basketStorage */
	protected $basketStorage;
	/** @var Sale\Basket */
	private $calculateBasket;

	protected $action;
	protected $arUserResult;
	protected $isOrderConfirmed;
	protected $arCustomSelectFields = [];
	protected $arElementId = [];
	protected $arSku2Parent = [];
	/** @var Delivery\Services\Base[] $arDeliveryServiceAll */
	protected $arDeliveryServiceAll = [];
	protected $arPaySystemServiceAll = [];
	protected $arActivePaySystems = [];
	protected $arIblockProps = [];
	/** @var  PaySystem\Service $prePaymentService */
	protected $prePaymentService;
	protected $useCatalog;
	/** @var Main\Context $context */
	protected $context;
	protected $checkSession = true;
	protected $isRequestViaAjax;
	protected $wasAuthorized;

	public $arParams;
	public $arResult;
	public $request;
	public $result;

	public static function arrayDiffRecursive($arr1, $arr2)
	{
		$modified = [];

		foreach ($arr1 as $key => $value)
		{
			if (array_key_exists($key, $arr2))
			{
				if (is_array($value) && is_array($arr2[$key]))
				{
					$arDiff = self::arrayDiffRecursive($value, $arr2[$key]);
					if (!empty($arDiff))
					{
						$modified[$key] = $arDiff;
					}
				}
				elseif ($value != $arr2[$key])
				{
					$modified[$key] = $value;
				}
			}
			else
			{
				$modified[$key] = $value;
			}
		}

		return $modified;
	}
	

	public function executeComponent()
	{
		global $APPLICATION;

		$this->context = Main\Application::getInstance()->getContext();
		$this->checkSession = $this->arParams["DELIVERY_NO_SESSION"] == "N" || check_bitrix_sessid();
		$this->isRequestViaAjax = $this->request->isPost() && $this->request->get('via_ajax') == 'Y';
		$isAjaxRequest = $this->request["is_ajax_post"] == "Y";

		if ($isAjaxRequest)
			$APPLICATION->RestartBuffer();

		$this->action = $this->prepareAction();
		Sale\Compatible\DiscountCompatibility::stopUsageCompatible();
		$this->doAction($this->action);
		/*Sale\Compatible\DiscountCompatibility::revertUsageCompatible();

		if (!$isAjaxRequest)
		{
			CJSCore::Init(['fx', 'popup', 'window', 'ajax', 'date']);
		}

		//is included in all cases for old template
		$this->includeComponentTemplate();

		if ($isAjaxRequest)
		{
			$APPLICATION->FinalActions();
			die();
		}*/
	}

	public function generateUserData($userProps = [])
	{		
		$userEmail = isset($userProps['EMAIL']) ? trim((string)$userProps['EMAIL']) : '';
		$newLogin = $userEmail;

		if (empty($userEmail))
		{
			$newEmail = false;
			$normalizedPhone = $this->getNormalizedPhone($userProps['PHONE']);

			if (!empty($normalizedPhone))
			{
				$newLogin = $normalizedPhone;
			}
		}
		else
		{
			$newEmail = $userEmail;
		}
		
		if (empty($newLogin))
		{
			$newLogin = randString(5).mt_rand(0, 99999);
		}

		$pos = mb_strpos($newLogin, '@');
		if ($pos !== false)
		{
			$newLogin = mb_substr($newLogin, 0, $pos);
		}

		if (mb_strlen($newLogin) > 47)
		{
			$newLogin = mb_substr($newLogin, 0, 47);
		}

		$newLogin = str_pad($newLogin, 3, '_');

		$dbUserLogin = CUser::GetByLogin($newLogin);
		if ($userLoginResult = $dbUserLogin->Fetch())
		{
			do
			{
				$newLoginTmp = $newLogin.mt_rand(0, 99999);
				$dbUserLogin = CUser::GetByLogin($newLoginTmp);
			} while ($userLoginResult = $dbUserLogin->Fetch());

			$newLogin = $newLoginTmp;
		}

		$newName = '';
		$newLastName = '';
		$payerName = isset($userProps['PAYER']) ? trim((string)$userProps['PAYER']) : '';

		if (!empty($payerName))
		{
			$arNames = explode(' ', $payerName);

			if (isset($arNames[1]))
			{
				$newName = $arNames[1];
				$newLastName = $arNames[0];
			}
			else
			{
				$newName = $arNames[0];
			}
		}

		$groupIds = [];
		$defaultGroups = Option::get('main', 'new_user_registration_def_group', '');

		if (!empty($defaultGroups))
		{
			$groupIds = explode(',', $defaultGroups);
		}

		$newPassword = \CUser::GeneratePasswordByPolicy($groupIds);
		
		
		return [
			'NEW_EMAIL' => $newEmail,
			'NEW_LOGIN' => $newLogin,
			'NEW_NAME' => $newName,
			'NEW_LAST_NAME' => $newLastName,
			'NEW_PASSWORD' => $newPassword,
			'NEW_PASSWORD_CONFIRM' => $newPassword,
			'GROUP_ID' => $groupIds,
		];
	}

	public function getExternalPayment(Order $order)
	{
		/** @var Payment $payment */
		foreach ($order->getPaymentCollection() as $payment)
		{
			if ($payment->getPaymentSystemId() != PaySystem\Manager::getInnerPaySystemId())
				return $payment;
		}

		return null;
	}

	public function init($params, $request, $arResult){
		$this->arParams = $params;
		$this->request = $request;
		$this->arResult = $arResult;
		$this->result = 0;
		$this->wasAuthorized = false;
	}

	protected function actionExists($action)
	{
		return is_callable([$this, $action.'Action']);
	}

	protected function addLastLocationPropertyValues($orderProperties)
	{
		$currentPersonType = (int)$this->arUserResult['PERSON_TYPE_ID'];
		$lastPersonType = (int)$this->arUserResult['PERSON_TYPE_OLD'];

		if (!empty($lastPersonType) && $currentPersonType !== $lastPersonType)
		{
			$propsByPersonType = [];

			$props = Sale\Property::getList([
				'select' => ['ID', 'PERSON_TYPE_ID', 'IS_LOCATION', 'IS_ZIP', 'DEFAULT_VALUE'],
				'filter' => [
					[
						'LOGIC' => 'OR',
						'=IS_ZIP' => 'Y',
						'=IS_LOCATION' => 'Y',
					],
					[
						'@PERSON_TYPE_ID' => [$currentPersonType, $lastPersonType],
					],
				],
			]);

			foreach ($props as $prop)
			{
				if ($prop['PERSON_TYPE_ID'] == $currentPersonType && !empty($prop['DEFAULT_VALUE']))
				{
					continue;
				}

				if ($prop['IS_LOCATION'] === 'Y')
				{
					$propsByPersonType[$prop['PERSON_TYPE_ID']]['IS_LOCATION'] = $prop['ID'];
				}
				else
				{
					$propsByPersonType[$prop['PERSON_TYPE_ID']]['IS_ZIP'] = $prop['ID'];
				}
			}

			if (!empty($propsByPersonType[$lastPersonType]))
			{
				foreach ($propsByPersonType[$lastPersonType] as $code => $id)
				{
					if (!empty($propsByPersonType[$currentPersonType][$code]))
					{
						$newId = $propsByPersonType[$currentPersonType][$code];

						if (empty($orderProperties[$newId]) && !empty($orderProperties[$id]))
						{
							$orderProperties[$newId] = $orderProperties[$id];
						}
					}
				}
			}
		}

		return $orderProperties;
	}

	protected function addStatistic()
	{
		if (Loader::includeModule("statistic"))
		{
			$event1 = "eStore";
			$event2 = "order_confirm";
			$event3 = $this->order->getId();
			$money = $this->order->getPrice();
			$currency = $this->order->getCurrency();

			$e = $event1."/".$event2."/".$event3;

			if (!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"])))
			{
				CStatistic::Set_Event($event1, $event2, $event3, $goto = "", $money, $currency);
				$_SESSION["ORDER_EVENTS"][] = $e;
			}
		}
	}

	protected function autoRegisterUser()
	{
		$personType = $this->request->get('PERSON_TYPE');
		if ($personType <= 0)
		{
			$personTypes = PersonType::load(SITE_ID);
			foreach ($personTypes as $type)
			{
				$personType = $type['ID'];
				break;
			}

			unset($personTypes, $type);
		}
		$userProps = Sale\PropertyValue::getMeaningfulValues($personType, $this->getPropertyValuesFromRequest());
		$userId = false;
		$saveToSession = false;

		if (
			$this->arParams['ALLOW_APPEND_ORDER'] === 'Y'
			&& (
				Option::get('main', 'new_user_email_uniq_check', '') === 'Y'
				|| Option::get('main', 'new_user_phone_auth', '') === 'Y'
			)
			&& ($userProps['EMAIL'] != '' || $userProps['PHONE'] != '')
		)
		{
			$existingUserId = 0;
			if ($userProps['EMAIL'] != '')
			{
				$res = Bitrix\Main\UserTable::getRow([
					'filter' => [
						'=ACTIVE' => 'Y',
						'=EMAIL' => $userProps['EMAIL'],
						'!=EXTERNAL_AUTH_ID' => $this->getExternalUserTypes()
					],
					'select' => ['ID', 'LOGIN', 'PASSWORD'],
				]);
				if (isset($res['ID']))
				{
					$existingUserId = (int)$res['ID'];
				}
			}
			$this->result = $existingUserId;
			if ($existingUserId > 0)
			{
				$userId = $existingUserId;
				$saveToSession = true;

				if ($this->arParams['IS_LANDING_SHOP'] === 'Y')
				{
					CUser::AppendUserGroup($userId, \Bitrix\Crm\Order\BuyerGroup::getDefaultGroups());
				}
			}
			else
			{
				$userId = $this->registerAndLogIn($userProps);
			}
		}
		elseif ($userProps['EMAIL'] != '' || Option::get('main', 'new_user_email_required', '') === 'N')
		{
			$userId = $this->registerAndLogIn($userProps);
		}

		return [$userId, $saveToSession];
	}

	protected function checkAltLocationProperty(Order $order, $useProfileProperties, array $profileProperties)
	{
		$locationAltPropDisplayManual = $this->request->get('LOCATION_ALT_PROP_DISPLAY_MANUAL');
		$propertyCollection = $order->getPropertyCollection();
		/** @var Sale\PropertyValue $property */
		foreach ($propertyCollection as $property)
		{
			if ($property->isUtil())
				continue;

			if ($property->getType() == 'LOCATION')
			{
				$propertyFields = $property->getProperty();
				if ((int)$propertyFields['INPUT_FIELD_LOCATION'] > 0)
				{
					if ($useProfileProperties)
					{
						$deleteAltProp = empty($profileProperties[$propertyFields['INPUT_FIELD_LOCATION']]);
					}
					else
					{
						$deleteAltProp = !isset($locationAltPropDisplayManual[$propertyFields['ID']])
							|| !(bool)$locationAltPropDisplayManual[$propertyFields['ID']];

						// check if have no city at all then show alternate property
						if (
							isset($locationAltPropDisplayManual[$propertyFields['ID']])
							&& !$this->haveCitiesInTree($this->arUserResult['ORDER_PROP'][$property->getPropertyId()])
						)
						{
							$deleteAltProp = false;
						}
					}

					if ($deleteAltProp)
					{
						unset($this->arUserResult['ORDER_PROP'][$propertyFields['INPUT_FIELD_LOCATION']]);
					}
				}
			}
		}
	}

	protected function checkOrderConsistency(Order $order)
	{
		return $this->getActualQuantityList($order->getBasket()) === $this->arUserResult['QUANTITY_LIST'];
	}

	protected function checkZipProperty(Order $order, $loadFromProfile)
	{
		$propertyCollection = $order->getPropertyCollection();
		$zip = $propertyCollection->getDeliveryLocationZip();
		$location = $propertyCollection->getDeliveryLocation();
		if (!empty($zip) && !empty($location))
		{
			$locId = $location->getField('ORDER_PROPS_ID');
			$locValue = $this->arUserResult['ORDER_PROP'][$locId];

			// need to override flag for zip data from profile
			if ($loadFromProfile)
			{
				$this->arUserResult['ZIP_PROPERTY_CHANGED'] = 'Y';
			}

			$requestLocation = $this->request->get('RECENT_DELIVERY_VALUE');
			// reload zip when user manually choose another location
			if ($requestLocation !== null && $locValue !== $requestLocation)
			{
				$this->arUserResult['ZIP_PROPERTY_CHANGED'] = 'N';
			}

			// don't autoload zip property if user manually changed it
			if ($this->arUserResult['ZIP_PROPERTY_CHANGED'] !== 'Y')
			{
				$res = Sale\Location\Admin\LocationHelper::getZipByLocation($locValue);

				if ($arZip = $res->fetch())
				{
					if (!empty($arZip['XML_ID']))
					{
						$this->arUserResult['ORDER_PROP'][$zip->getField('ORDER_PROPS_ID')] = $arZip['XML_ID'];
					}
				}
			}
		}
	}

	protected function createOrder($userId)
	{
		$order = '';
		$this->makeUserResultArray();

		DiscountCouponsManager::init(DiscountCouponsManager::MODE_CLIENT, ['userId' => $userId]);
		$this->executeEvent('OnSaleComponentOrderOneStepDiscountBefore');

		/** @var Order $order */
		$order = $this->getOrder($userId);
		// $this->arUserResult['RECREATE_ORDER'] - flag for full order recalculation after events manipulations
		if ($this->arUserResult['RECREATE_ORDER'])
			$order = $this->getOrder($userId);

		// $this->arUserResult['CALCULATE_PAYMENT'] - flag for order payments recalculation after events manipulations
		if ($this->arUserResult['CALCULATE_PAYMENT'])
			$this->recalculatePayment($order);
		
		return $order;
	}

	protected function doAction($action)
	{
		if ($this->actionExists($action))
		{
			$this->{$action.'Action'}();
		}
	}

	protected function executeEvent($eventName = '', $order = null)
	{
		$arModifiedResult = $this->arUserResult;
		foreach (GetModuleEvents("sale", $eventName, true) as $arEvent)
			ExecuteModuleEventEx($arEvent, [&$this->arResult, &$arModifiedResult, &$this->arParams, true]);
		if (!empty($order))
			$this->synchronize($arModifiedResult, $order);
	}

	protected function getActualQuantityList(Sale\BasketBase $basket)
	{
		$quantityList = [];

		if (!$basket->isEmpty())
		{
			/** @var Sale\BasketItemBase $basketItem */
			foreach ($basket as $basketItem)
			{
				if ($basketItem->canBuy() && !$basketItem->isDelay())
				{
					$quantityList[$basketItem->getBasketCode()] = $basketItem->getQuantity();
				}
			}
		}

		return $quantityList;
	}

	protected function getExternalUserTypes(): array
	{
		return array_diff(\Bitrix\Main\UserTable::getExternalUserTypes(), ['shop']);
	}

	protected function getFullPropertyList(Order $order)
	{
		$registry = Sale\Registry::getInstance(Sale\Registry::REGISTRY_TYPE_ORDER);
		/** @var \Bitrix\Sale\PropertyBase $propertyClassName */
		$propertyClassName = $registry->getPropertyClassName();

		$result = $propertyClassName::getList([
			'select' => [
				'ID', 'PERSON_TYPE_ID', 'NAME', 'TYPE', 'REQUIRED', 'DEFAULT_VALUE', 'SORT',
				'USER_PROPS', 'IS_LOCATION', 'PROPS_GROUP_ID', 'DESCRIPTION', 'IS_EMAIL', 'IS_PROFILE_NAME',
				'IS_PAYER', 'IS_LOCATION4TAX', 'IS_FILTERED', 'CODE', 'IS_ZIP', 'IS_PHONE', 'IS_ADDRESS',
				'ACTIVE', 'UTIL', 'INPUT_FIELD_LOCATION', 'MULTIPLE', 'SETTINGS',
			],
			'filter' => [
				'=PERSON_TYPE_ID' => $order->getPersonTypeId(),
			],
			'order' => ['SORT' => 'ASC'],
		]);

		return $result->fetchAll();
	}

	public function getInnerPayment(Order $order)
	{
		/** @var Payment $payment */
		foreach ($order->getPaymentCollection() as $payment)
		{
			if ($payment->getPaymentSystemId() == PaySystem\Manager::getInnerPaySystemId())
				return $payment;
		}

		return null;
	}

	protected function getInnerPaySystemInfo(Order $order, $recalculate = false)
	{
		$arResult =& $this->arResult;

		$sumToSpend = 0;
		$arPaySystemServices = [];

		if ($this->arParams['PAY_FROM_ACCOUNT'] === 'Y' && $order->isAllowPay())
		{
			$innerPaySystemId = PaySystem\Manager::getInnerPaySystemId();
			$innerPayment = $order->getPaymentCollection()->getInnerPayment();

			if (!$innerPayment)
			{
				$innerPayment = $order->getPaymentCollection()->createInnerPayment();
			}

			if (!$innerPayment)
			{
				return [0, $arPaySystemServices];
			}

			$this->loadUserAccount($order);
			$userBudget = (float)$arResult['USER_ACCOUNT']['CURRENT_BUDGET'];

			// finding correct inner pay system price ranges to setField()
			$sumRange = Sale\Services\PaySystem\Restrictions\Manager::getPriceRange($innerPayment, $innerPaySystemId);
			if (!empty($sumRange))
			{
				if (
					(empty($sumRange['MIN']) || $sumRange['MIN'] <= $userBudget)
					&& (empty($sumRange['MAX']) || $sumRange['MAX'] >= $userBudget)
				)
				{
					$sumToSpend = $userBudget;
				}

				if (!empty($sumRange['MAX']) && $sumRange['MAX'] <= $userBudget)
				{
					$sumToSpend = $sumRange['MAX'];
				}
			}
			else
			{
				$sumToSpend = $userBudget;
			}

			$sumToSpend = $sumToSpend >= $order->getPrice() ? $order->getPrice() : $sumToSpend;

			if ($this->arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] === 'Y' && $sumToSpend < $order->getPrice())
			{
				$sumToSpend = 0;
			}

			if (!empty($arResult['USER_ACCOUNT']) && $sumToSpend > 0)
			{
				// setting inner payment price
				$innerPayment->setField('SUM', $sumToSpend);
				// getting allowed pay systems by restrictions
				$arPaySystemServices = PaySystem\Manager::getListWithRestrictions($innerPayment);
				// delete inner pay system if restrictions has not passed
				if (!isset($arPaySystemServices[$innerPaySystemId]))
				{
					$innerPayment->delete();
					$sumToSpend = 0;
				}
			}
			else
			{
				$innerPayment->delete();
			}
		}

		if ($sumToSpend > 0)
		{
			$arResult['PAY_FROM_ACCOUNT'] = 'Y';
			$arResult['CURRENT_BUDGET_FORMATED'] = SaleFormatCurrency($arResult['USER_ACCOUNT']['CURRENT_BUDGET'], $order->getCurrency());
		}
		else
		{
			$arResult['PAY_FROM_ACCOUNT'] = 'N';
			unset($arResult['CURRENT_BUDGET_FORMATED']);
		}

		return [$sumToSpend, $arPaySystemServices];
	}

	protected function getLastOrderData(Order $order)
	{
		$lastOrderData = [];

		$registry = Sale\Registry::getInstance(Sale\Registry::REGISTRY_TYPE_ORDER);
		/** @var Order $orderClassName */
		$orderClassName = $registry->getOrderClassName();

		$filter = [
			'filter' => [
				'USER_ID' => $order->getUserId(),
				'LID' => $order->getSiteId(),
			],
			'select' => ['ID'],
			'order' => ['ID' => 'DESC'],
			'limit' => 1,
		];

		if ($arOrder = $orderClassName::getList($filter)->fetch())
		{
			/** @var Order $lastOrder */
			$lastOrder = $orderClassName::load($arOrder['ID']);
			$lastOrderData['PERSON_TYPE_ID'] = $lastOrder->getPersonTypeId();

			if ($payment = $this->getInnerPayment($lastOrder))
				$lastOrderData['PAY_CURRENT_ACCOUNT'] = 'Y';

			if ($payment = $this->getExternalPayment($lastOrder))
				$lastOrderData['PAY_SYSTEM_ID'] = $payment->getPaymentSystemId();
		}

		return $lastOrderData;
	}

	protected function getNormalizedPhone($phone)
	{
		if ($this->arParams['USE_PHONE_NORMALIZATION'] === 'Y')
		{
			$phone = NormalizePhone((string)$phone, 3);
		}

		return $phone;
	}

	protected function getOrder($userId)
	{
		$registry = Sale\Registry::getInstance(Sale\Registry::REGISTRY_TYPE_ORDER);
		/** @var Order $orderClassName */
		$orderClassName = $registry->getOrderClassName();

		$order = $orderClassName::create(SITE_ID, $userId);
		
		$order->isStartField();

		$this->initLastOrderData($order);

		$order->setField('STATUS_ID', Sale\OrderStatus::getInitialStatus());

		$isPersonTypeChanged = $this->initPersonType($order);

		$this->initProperties($order, $isPersonTypeChanged);
		$this->initBasket($order);

		$taxes = $order->getTax();
		$taxes->setDeliveryCalculate($this->arParams['COUNT_DELIVERY_TAX'] === 'Y');

		$order->doFinalAction(true);

		$this->initPayment($order);

		$this->initEntityCompanyIds($order);
		$this->initOrderFields($order);

		// initialization of related properties
		$this->setOrderProperties($order);

		$this->recalculatePayment($order);

		$eventParameters = [
			$order, &$this->arUserResult, $this->request,
			&$this->arParams, &$this->arResult, &$this->arDeliveryServiceAll, &$this->arPaySystemServiceAll,
		];
		foreach (GetModuleEvents('sale', 'OnSaleComponentOrderCreated', true) as $arEvent)
		{
			ExecuteModuleEventEx($arEvent, $eventParameters);
		}

		// no need to calculate deliveries when order is saving
		return $order;
	}

	protected function getPropertyValuesFromRequest()
	{
		$orderProperties = [];

		foreach ($this->request as $k => $v)
		{
			if (mb_strpos($k, "ORDER_PROP_") !== false)
			{
				if (mb_strpos($k, "[]") !== false)
					$orderPropId = intval(mb_substr($k, mb_strlen("ORDER_PROP_"), mb_strlen($k) - 2));
				else
					$orderPropId = intval(mb_substr($k, mb_strlen("ORDER_PROP_")));

				if ($orderPropId > 0)
					$orderProperties[$orderPropId] = $v;
			}
		}

		foreach ($this->request->getFileList() as $k => $arFileData)
		{
			if (mb_strpos($k, "ORDER_PROP_") !== false)
			{
				$orderPropId = intval(mb_substr($k, mb_strlen("ORDER_PROP_")));

				if (is_array($arFileData))
				{
					foreach ($arFileData as $param_name => $value)
					{
						if (is_array($value))
						{
							foreach ($value as $nIndex => $val)
							{
								if ($arFileData["name"][$nIndex] <> '')
								{
									$orderProperties[$orderPropId][$nIndex][$param_name] = $val;
								}

								if (!isset($orderProperties[$orderPropId][$nIndex]['ID']))
								{
									$orderProperties[$orderPropId][$nIndex]['ID'] = '';
								}
							}
						}
						else
						{
							$orderProperties[$orderPropId][$param_name] = $value;

							if (!isset($orderProperties[$orderPropId]['ID']))
							{
								$orderProperties[$orderPropId]['ID'] = '';
							}
						}
					}
				}
			}
		}

		return $orderProperties;
	}

	protected function getValueFromCUser($property)
	{
		global $USER;

		$value = '';

		if ($property['IS_EMAIL'] === 'Y')
		{
			$value = $USER->GetEmail();
		}
		elseif ($property['IS_PAYER'] === 'Y')
		{
			$rsUser = CUser::GetByID($USER->GetID());
			if ($arUser = $rsUser->Fetch())
			{
				$value = CUser::FormatName(
					CSite::GetNameFormat(false),
					[
						'NAME' => $arUser['NAME'],
						'LAST_NAME' => $arUser['LAST_NAME'],
						'SECOND_NAME' => $arUser['SECOND_NAME'],
					],
					false,
					false
				);
			}
		}
		elseif ($property['IS_PHONE'] === 'Y')
		{
			$phoneRow = \Bitrix\Main\UserPhoneAuthTable::getRow([
				'select' => ['PHONE_NUMBER'],
				'filter' => ['=USER_ID' => $USER->GetID()],
			]);

			if ($phoneRow)
			{
				$value = $phoneRow['PHONE_NUMBER'];
			}
			else
			{
				$rsUser = CUser::GetByID($USER->GetID());

				if ($arUser = $rsUser->Fetch())
				{
					if (!empty($arUser['PERSONAL_PHONE']))
					{
						$value = $arUser['PERSONAL_PHONE'];
					}
					elseif (!empty($arUser['PERSONAL_MOBILE']))
					{
						$value = $arUser['PERSONAL_MOBILE'];
					}
				}
			}
		}
		elseif ($property['IS_ADDRESS'] === 'Y')
		{
			$rsUser = CUser::GetByID($USER->GetID());
			if ($arUser = $rsUser->Fetch())
			{
				if (!empty($arUser['PERSONAL_STREET']))
				{
					$value = $arUser['PERSONAL_STREET'];
				}
			}
		}

		return $value;
	}

	protected function haveCitiesInTree($locationCode)
	{
		if (empty($locationCode))
			return false;

		$haveCities = false;
		$location = LocationTable::getRow(['filter' => ['=CODE' => $locationCode]]);

		if (!empty($location))
		{
			if ($location['TYPE_ID'] >= 5)
			{
				$haveCities = true;
			}
			else
			{
				$parameters = [
					'filter' => [
						'>=LEFT_MARGIN' => (int)$location['LEFT_MARGIN'],
						'<=RIGHT_MARGIN' => (int)$location['RIGHT_MARGIN'],
						'>=DEPTH_LEVEL' => (int)$location['DEPTH_LEVEL'],
						'!CITY_ID' => null,
					],
					'count_total' => true,
				];
				$haveCities = LocationTable::getList($parameters)->getCount() > 0;
			}
		}

		return $haveCities;
	}

	protected function initAffiliate()
	{
		$affiliateID = CSaleAffiliate::GetAffiliate();
		if ($affiliateID > 0)
		{
			$dbAffiliate = CSaleAffiliate::GetList([], ["SITE_ID" => $this->getSiteId(), "ID" => $affiliateID]);
			$arAffiliates = $dbAffiliate->Fetch();
			if (count($arAffiliates) > 1)
				$this->order->setField('AFFILIATE_ID', $affiliateID);
		}
	}

	protected function initBasket(Order $order)
	{
		$basket = $this->loadBasket();

		$this->arUserResult['QUANTITY_LIST'] = $this->getActualQuantityList($basket);

		$result = $basket->refresh();
		if ($result->isSuccess())
		{
			$basket->save();
		}

		// right NOW we decide to work only with available basket
		// full basket won't update anymore
		$availableBasket = $basket->getOrderableItems();
		if ($availableBasket->isEmpty())
		{
			$this->showEmptyBasket();
		}

		$order->appendBasket($availableBasket);
	}

	protected function initEntityCompanyIds(Order $order)
	{
		$paymentCollection = $order->getPaymentCollection();
		if ($paymentCollection)
		{
			/** @var Payment $payment */
			foreach ($paymentCollection as $payment)
			{
				if ($payment->isInner())
					continue;

				$payment->setField('COMPANY_ID', Company\Manager::getAvailableCompanyIdByEntity($payment));
				if ($payment->getField('COMPANY_ID') > 0)
				{
					$responsibleGroups = Sale\Internals\CompanyResponsibleGroupTable::getCompanyGroups($payment->getField('COMPANY_ID'));
					if (!empty($responsibleGroups) && is_array($responsibleGroups))
					{
						$usersList = [];

						foreach ($responsibleGroups as $groupId)
						{
							$usersList[] = CGroup::GetGroupUser($groupId);
						}

						$usersList = array_merge(...$usersList);

						if (!empty($usersList) && is_array($usersList))
						{
							$usersList = array_unique($usersList);
							$responsibleUserId = $usersList[array_rand($usersList)];

							/** @var Main\Entity\Event $event */
							$event = new Main\Event('sale', 'OnSaleComponentBeforePaymentSetResponsibleUserId', [
								'ENTITY' => $payment,
								'VALUE' => $responsibleUserId,
							]);
							$event->send();

							if ($event->getResults())
							{
								$result = new Result();
								/** @var Main\EventResult $eventResult */
								foreach ($event->getResults() as $eventResult)
								{
									if ($eventResult->getType() == Main\EventResult::SUCCESS)
									{
										if ($eventResultData = $eventResult->getParameters())
										{
											if (isset($eventResultData['VALUE']) && $eventResultData['VALUE'] != $responsibleUserId)
											{
												$responsibleUserId = $eventResultData['VALUE'];
											}
										}
									}
								}
							}

							$payment->setField('RESPONSIBLE_ID', $responsibleUserId);
						}
					}
				}
			}
		}

		$shipmentCollection = $order->getShipmentCollection();
		if ($shipmentCollection)
		{
			/** @var Shipment $shipment */
			foreach ($shipmentCollection as $shipment)
			{
				if ($shipment->isSystem())
					continue;

				$shipment->setField('COMPANY_ID', Company\Manager::getAvailableCompanyIdByEntity($shipment));

				if ($shipment->getField('COMPANY_ID') > 0)
				{
					$responsibleGroups = Sale\Internals\CompanyResponsibleGroupTable::getCompanyGroups($shipment->getField('COMPANY_ID'));
					if (!empty($responsibleGroups) && is_array($responsibleGroups))
					{
						$usersList = [];

						foreach ($responsibleGroups as $groupId)
						{
							$usersList[] = CGroup::GetGroupUser($groupId);
						}

						$usersList = array_merge(...$usersList);

						if (!empty($usersList) && is_array($usersList))
						{
							$usersList = array_unique($usersList);
							$responsibleUserId = $usersList[array_rand($usersList)];

							/** @var Main\Entity\Event $event */
							$event = new Main\Event('sale', 'OnSaleComponentBeforeShipmentSetResponsibleUserId', [
								'ENTITY' => $shipment,
								'VALUE' => $responsibleUserId,
							]);
							$event->send();

							if ($event->getResults())
							{
								$result = new Result();
								/** @var Main\EventResult $eventResult */
								foreach ($event->getResults() as $eventResult)
								{
									if ($eventResult->getType() == Main\EventResult::SUCCESS)
									{
										if ($eventResultData = $eventResult->getParameters())
										{
											if (isset($eventResultData['VALUE']) && $eventResultData['VALUE'] != $responsibleUserId)
											{
												$responsibleUserId = $eventResultData['VALUE'];
											}
										}
									}
								}
							}

							$shipment->setField('RESPONSIBLE_ID', $responsibleUserId);
						}
					}
				}
			}
		}
	}

	protected function initLastOrderData(Order $order)
	{
		global $USER;
		if (
			($this->request->getRequestMethod() === 'GET' || $this->request->get('do_authorize') === 'Y' || $this->request->get('do_register') === 'Y')
			&& $this->arUserResult['USE_PRELOAD']
			&& $USER->IsAuthorized()
		)
		{
			$showData = [];
			$lastOrderData = $this->getLastOrderData($order);

			if (!empty($lastOrderData))
			{
				if (!empty($lastOrderData['PERSON_TYPE_ID']))
					$this->arUserResult['PERSON_TYPE_ID'] = $showData['PERSON_TYPE_ID'] = $lastOrderData['PERSON_TYPE_ID'];

				if (!empty($lastOrderData['PAY_CURRENT_ACCOUNT']))
					$this->arUserResult['PAY_CURRENT_ACCOUNT'] = $showData['PAY_CURRENT_ACCOUNT'] = $lastOrderData['PAY_CURRENT_ACCOUNT'];

				if (!empty($lastOrderData['PAY_SYSTEM_ID']))
					$this->arUserResult['PAY_SYSTEM_ID'] = $showData['PAY_SYSTEM_ID'] = $lastOrderData['PAY_SYSTEM_ID'];

				if (!empty($lastOrderData['DELIVERY_ID']))
					$this->arUserResult['DELIVERY_ID'] = $showData['DELIVERY_ID'] = $lastOrderData['DELIVERY_ID'];

				if (!empty($lastOrderData['DELIVERY_EXTRA_SERVICES']))
					$this->arUserResult['DELIVERY_EXTRA_SERVICES'] = $showData['DELIVERY_EXTRA_SERVICES'] = $lastOrderData['DELIVERY_EXTRA_SERVICES'];

				if (!empty($lastOrderData['BUYER_STORE']))
					$this->arUserResult['BUYER_STORE'] = $showData['BUYER_STORE'] = $lastOrderData['BUYER_STORE'];

				$this->arUserResult['LAST_ORDER_DATA'] = $showData;
			}
		}
	}

	protected function initOrderFields(Order $order)
	{
		$order->setField("USER_DESCRIPTION", $this->arUserResult['ORDER_DESCRIPTION']);
		$order->setField('COMPANY_ID', Company\Manager::getAvailableCompanyIdByEntity($order));

		if ($order->getField('COMPANY_ID') > 0)
		{
			$responsibleGroups = Sale\Internals\CompanyResponsibleGroupTable::getCompanyGroups($order->getField('COMPANY_ID'));
			if (!empty($responsibleGroups) && is_array($responsibleGroups))
			{
				$usersList = [];

				foreach ($responsibleGroups as $groupId)
				{
					$usersList[] = CGroup::GetGroupUser($groupId);
				}

				$usersList = array_merge(...$usersList);

				if (!empty($usersList) && is_array($usersList))
				{
					$usersList = array_unique($usersList);
					$responsibleUserId = $usersList[array_rand($usersList)];

					/** @var Main\Entity\Event $event */
					$event = new Main\Event('sale', 'OnSaleComponentBeforeOrderSetResponsibleUserId', [
						'ENTITY' => $order,
						'VALUE' => $responsibleUserId,
					]);
					$event->send();

					if ($event->getResults())
					{
						$result = new Result();
						/** @var Main\EventResult $eventResult */
						foreach ($event->getResults() as $eventResult)
						{
							if ($eventResult->getType() == Main\EventResult::SUCCESS)
							{
								if ($eventResultData = $eventResult->getParameters())
								{
									if (isset($eventResultData['VALUE']) && $eventResultData['VALUE'] != $responsibleUserId)
									{
										$responsibleUserId = $eventResultData['VALUE'];
									}
								}
							}
						}
					}

					$order->setField('RESPONSIBLE_ID', $responsibleUserId);
				}

			}
		}

	}

	protected function initPayment(Order $order)
	{
		[$sumToSpend, $innerPaySystemList] = $this->getInnerPaySystemInfo($order);

		if ($sumToSpend > 0)
		{
			$innerPayment = $this->getInnerPayment($order);
			if (!empty($innerPayment))
			{
				if ($this->arUserResult['PAY_CURRENT_ACCOUNT'] === 'Y')
				{
					$innerPayment->setField('SUM', $sumToSpend);
				}
				else
				{
					$innerPayment->delete();
					$innerPayment = null;
				}

				$this->arPaySystemServiceAll = $this->arActivePaySystems = $innerPaySystemList;
			}
		}

		$innerPaySystemId = PaySystem\Manager::getInnerPaySystemId();
		$extPaySystemId = (int)$this->arUserResult['PAY_SYSTEM_ID'];

		$paymentCollection = $order->getPaymentCollection();
		$remainingSum = $order->getPrice() - $paymentCollection->getSum();
		if ($remainingSum > 0 || $order->getPrice() == 0)
		{
			/** @var Payment $extPayment */
			$extPayment = $paymentCollection->createItem();
			$extPayment->setField('SUM', $remainingSum);

			$extPaySystemList = PaySystem\Manager::getListWithRestrictions($extPayment);

			// we already checked restrictions for inner pay system (could be different by price restrictions)
			if (empty($innerPaySystemList[$innerPaySystemId]))
			{
				unset($extPaySystemList[$innerPaySystemId]);
			}
			elseif (empty($extPaySystemList[$innerPaySystemId]))
			{
				$extPaySystemList[$innerPaySystemId] = $innerPaySystemList[$innerPaySystemId];
			}

			$this->arPaySystemServiceAll = $this->arActivePaySystems = $extPaySystemList;

			if ($extPaySystemId !== 0 && array_key_exists($extPaySystemId, $this->arPaySystemServiceAll))
			{
				$selectedPaySystem = $this->arPaySystemServiceAll[$extPaySystemId];
			}
			else
			{
				reset($this->arPaySystemServiceAll);

				if (key($this->arPaySystemServiceAll) == $innerPaySystemId)
				{
					if (count($this->arPaySystemServiceAll) > 1)
					{
						next($this->arPaySystemServiceAll);
					}
					elseif ($sumToSpend > 0)
					{
						$extPayment->delete();
						$extPayment = null;

						/** @var Payment $innerPayment */
						$innerPayment = $this->getInnerPayment($order);
						if (empty($innerPayment))
						{
							$innerPayment = $paymentCollection->getInnerPayment();
							if (!$innerPayment)
							{
								$innerPayment = $paymentCollection->createInnerPayment();
							}
						}

						$sumToPay = $remainingSum > $sumToSpend ? $sumToSpend : $remainingSum;
						$innerPayment->setField('SUM', $sumToPay);
					}
					else
					{
						unset($this->arActivePaySystems[$innerPaySystemId]);
						unset($this->arPaySystemServiceAll[$innerPaySystemId]);
					}
				}

				$selectedPaySystem = current($this->arPaySystemServiceAll);
			}

			if (!empty($selectedPaySystem))
			{
				if ($selectedPaySystem['ID'] != $innerPaySystemId)
				{
					$extPayment->setFields([
						'PAY_SYSTEM_ID' => $selectedPaySystem['ID'],
						'PAY_SYSTEM_NAME' => $selectedPaySystem['NAME'],
					]);

					$this->arUserResult['PAY_SYSTEM_ID'] = $selectedPaySystem['ID'];
				}
			}
			elseif (!empty($extPayment))
			{
				$extPayment->delete();
				$extPayment = null;
			}
		}

		if (!empty($this->arUserResult['PREPAYMENT_MODE']))
		{
			$this->showOnlyPrepaymentPs($this->arUserResult['PAY_SYSTEM_ID']);
		}
	}

	protected function initPersonType(Order $order)
	{
		$arResult =& $this->arResult;
		$personTypeId = intval($this->arUserResult['PERSON_TYPE_ID']);
		$personTypeIdOld = intval($this->arUserResult['PERSON_TYPE_OLD']);

		$personTypes = PersonType::load(SITE_ID);
		foreach ($personTypes as $personType)
		{
			if ($personTypeId === intval($personType["ID"]) || !array_key_exists($personTypeId, $personTypes))
			{
				$personTypeId = intval($personType["ID"]);
				$order->setPersonTypeId($personTypeId);
				$this->arUserResult['PERSON_TYPE_ID'] = $personTypeId;
				$personType["CHECKED"] = "Y";
			}
			$arResult["PERSON_TYPE"][$personType["ID"]] = $personType;
		}

		$this->executeEvent('OnSaleComponentOrderOneStepPersonType', $order);

		return count($arResult["PERSON_TYPE"]) > 1 && ($personTypeId !== $personTypeIdOld);
	}

	protected function initProperties(Order $order, $isPersonTypeChanged)
	{
		$arResult =& $this->arResult;
		$orderProperties = $this->getPropertyValuesFromRequest();
		$orderProperties = $this->addLastLocationPropertyValues($orderProperties);

		$this->initUserProfiles($order, $isPersonTypeChanged);

		$firstLoad = $this->request->getRequestMethod() === 'GET';
		$justAuthorized = $this->request->get('do_authorize') === 'Y'
			|| $this->request->get('do_register') === 'Y'
			|| $this->request->get('SMS_CODE');

		$isProfileChanged = $this->arUserResult['PROFILE_CHANGE'] === 'Y';
		$haveProfileId = (int)$this->arUserResult['PROFILE_ID'] > 0;

		$shouldUseProfile = ($firstLoad || $justAuthorized || $isPersonTypeChanged || $isProfileChanged);
		$willUseProfile = $shouldUseProfile && $haveProfileId;

		$profileProperties = [];

		if ($haveProfileId)
		{
			$profileProperties = Sale\OrderUserProperties::getProfileValues((int)$this->arUserResult['PROFILE_ID']);
		}

		$ipAddress = '';

		if ($this->arParams['SPOT_LOCATION_BY_GEOIP'] === 'Y')
		{
			$ipAddress = \Bitrix\Main\Service\GeoIp\Manager::getRealIp();
		}

		foreach ($this->getFullPropertyList($order) as $property)
		{
			if ($property['USER_PROPS'] === 'Y')
			{
				if ($isProfileChanged && !$haveProfileId)
				{
					$curVal = '';
				}
				elseif (
					$willUseProfile
					|| (
						!isset($orderProperties[$property['ID']])
						&& isset($profileProperties[$property['ID']])
					)
				)
				{
					$curVal = $profileProperties[$property['ID']];
				}
				elseif (isset($orderProperties[$property['ID']]))
				{
					$curVal = $orderProperties[$property['ID']];
				}
				else
				{
					$curVal = '';
				}
			}
			else
			{
				$curVal = isset($orderProperties[$property['ID']]) ? $orderProperties[$property['ID']] : '';
			}
			
			if ($arResult['HAVE_PREPAYMENT'] && !empty($arResult['PREPAY_ORDER_PROPS'][$property['CODE']]))
			{
				if ($property['TYPE'] === 'LOCATION')
				{
					$cityName = ToUpper($arResult['PREPAY_ORDER_PROPS'][$property['CODE']]);
					$arLocation = LocationTable::getList([
						'select' => ['CODE'],
						'filter' => ['NAME.NAME_UPPER' => $cityName],
					])
						->fetch()
					;

					if (!empty($arLocation))
					{
						$curVal = $arLocation['CODE'];
					}
				}
				else
				{
					$curVal = $arResult['PREPAY_ORDER_PROPS'][$property['CODE']];
				}
			}
			
			if ($property['TYPE'] === 'LOCATION' && empty($curVal) && !empty($ipAddress))
			{
				$locCode = GeoIp::getLocationCode($ipAddress);

				if (!empty($locCode))
				{
					$curVal = $locCode;
				}
			}
			elseif ($property['IS_ZIP'] === 'Y' && empty($curVal) && !empty($ipAddress))
			{
				$zip = GeoIp::getZipCode($ipAddress);

				if (!empty($zip))
				{
					$curVal = $zip;
				}
			}
			elseif ($property['IS_PHONE'] === 'Y' && !empty($curVal))
			{
				$curVal = $this->getNormalizedPhone($curVal);
			}

			if (empty($curVal))
			{
				// getting default value for all properties except LOCATION
				// (LOCATION - just for first load or person type change or new profile)
				if ($property['TYPE'] !== 'LOCATION' || !$willUseProfile)
				{
					global $USER;

					if ($shouldUseProfile && $USER->IsAuthorized())
					{
						$curVal = $this->getValueFromCUser($property);
					}

					if (empty($curVal) && !empty($property['DEFAULT_VALUE']))
					{
						$curVal = $property['DEFAULT_VALUE'];
					}
				}
			}
			
			if ($property['TYPE'] === 'LOCATION')
			{
				if (
					(!$shouldUseProfile || $this->request->get('PROFILE_ID') === '0')
					&& $this->request->get('location_type') !== 'code'
				)
				{
					$curVal = CSaleLocation::getLocationCODEbyID($curVal);
				}
			}

			$this->arUserResult['ORDER_PROP'][$property['ID']] = $curVal;
		}
		
		$this->checkZipProperty($order, $willUseProfile);
		$this->checkAltLocationProperty($order, $willUseProfile, $profileProperties);
		
		foreach (GetModuleEvents('sale', 'OnSaleComponentOrderProperties', true) as $arEvent)
		{
			ExecuteModuleEventEx($arEvent, [&$this->arUserResult, $this->request, &$this->arParams, &$this->arResult]);
		}

		$this->setOrderProperties($order);
	}

	protected function initStatGid()
	{
		if (Loader::includeModule("statistic"))
			$this->order->setField('STAT_GID', CStatistic::GetEventParam());
	}

	protected function initUserProfiles(Order $order, $isPersonTypeChanged)
	{
		$arResult =& $this->arResult;

		$justAuthorized = $this->request->get('do_authorize') === 'Y' || $this->request->get('do_register') === 'Y';
		$profileIsNotSelected = $this->arUserResult['PROFILE_CHANGE'] === false || $this->arUserResult['PROFILE_ID'] === false;
		$bFirst = false;

		$dbUserProfiles = CSaleOrderUserProps::GetList(
			['DATE_UPDATE' => 'DESC'],
			[
				'PERSON_TYPE_ID' => $order->getPersonTypeId(),
				'USER_ID' => $order->getUserId(),
			]
		);
		while ($arUserProfiles = $dbUserProfiles->GetNext())
		{
			if (!$bFirst && ($profileIsNotSelected || $isPersonTypeChanged || $justAuthorized))
			{
				$bFirst = true;
				$this->arUserResult['PROFILE_ID'] = (int)$arUserProfiles['ID'];
			}

			if ((int)$this->arUserResult['PROFILE_ID'] === (int)$arUserProfiles['ID'])
			{
				$arUserProfiles['CHECKED'] = 'Y';
			}

			$arResult['ORDER_PROP']['USER_PROFILES'][$arUserProfiles['ID']] = $arUserProfiles;
		}
	}

	protected function makeUserResultArray()
	{
		$request =& $this->request;

		$arUserResult = [
			"PERSON_TYPE_ID" => false,
			"PERSON_TYPE_OLD" => false,
			"PAY_SYSTEM_ID" => false,
			"DELIVERY_ID" => false,
			"ORDER_PROP" => [],
			"DELIVERY_LOCATION" => false,
			"TAX_LOCATION" => false,
			"PAYER_NAME" => false,
			"USER_EMAIL" => false,
			"PROFILE_NAME" => false,
			"PAY_CURRENT_ACCOUNT" => false,
			"CONFIRM_ORDER" => false,
			"FINAL_STEP" => false,
			"ORDER_DESCRIPTION" => false,
			"PROFILE_ID" => false,
			"PROFILE_CHANGE" => false,
			"DELIVERY_LOCATION_ZIP" => false,
			"ZIP_PROPERTY_CHANGED" => 'N',
			"QUANTITY_LIST" => [],
			"USE_PRELOAD" => $this->arParams['USE_PRELOAD'] === 'Y',
		];

		if ($request->isPost())
		{
			if (intval($request->get('PERSON_TYPE')) > 0)
				$arUserResult["PERSON_TYPE_ID"] = intval($request->get('PERSON_TYPE'));

			if (intval($request->get('PERSON_TYPE_OLD')) > 0)
				$arUserResult["PERSON_TYPE_OLD"] = intval($request->get('PERSON_TYPE_OLD'));

			if (empty($arUserResult["PERSON_TYPE_OLD"]) || $arUserResult["PERSON_TYPE_OLD"] == $arUserResult["PERSON_TYPE_ID"])
			{
				$profileId = $request->get('PROFILE_ID');

				if ($profileId !== null)
				{
					$arUserResult['PROFILE_ID'] = (int)$profileId;
				}

				$paySystemId = $request->get('PAY_SYSTEM_ID');
				if (!empty($paySystemId))
					$arUserResult["PAY_SYSTEM_ID"] = intval($paySystemId);

				$deliveryId = $request->get('DELIVERY_ID');
				if (!empty($deliveryId))
					$arUserResult["DELIVERY_ID"] = $deliveryId;

				$buyerStore = $request->get('BUYER_STORE');
				if (!empty($buyerStore))
					$arUserResult["BUYER_STORE"] = intval($buyerStore);

				$deliveryExtraServices = $request->get('DELIVERY_EXTRA_SERVICES');
				if (!empty($deliveryExtraServices))
					$arUserResult["DELIVERY_EXTRA_SERVICES"] = $deliveryExtraServices;

				if ($request->get('ORDER_DESCRIPTION') <> '')
				{
					$arUserResult["~ORDER_DESCRIPTION"] = $request->get('ORDER_DESCRIPTION');
					$arUserResult["ORDER_DESCRIPTION"] = htmlspecialcharsbx($request->get('ORDER_DESCRIPTION'));
				}

				if ($request->get('PAY_CURRENT_ACCOUNT') == "Y")
					$arUserResult["PAY_CURRENT_ACCOUNT"] = "Y";

				if ($request->get('confirmorder') == "Y")
				{
					$arUserResult["CONFIRM_ORDER"] = "Y";
					$arUserResult["FINAL_STEP"] = "Y";
				}

				$arUserResult["PROFILE_CHANGE"] = $request->get('profile_change') == "Y" ? "Y" : "N";
			}

			$arUserResult['ZIP_PROPERTY_CHANGED'] = $this->request->get('ZIP_PROPERTY_CHANGED') === 'Y' ? 'Y' : 'N';
		}

		foreach (GetModuleEvents("sale", 'OnSaleComponentOrderUserResult', true) as $arEvent)
			ExecuteModuleEventEx($arEvent, [&$arUserResult, $this->request, &$this->arParams]);

		$this->arUserResult = $arUserResult;
	}

	protected function loadUserAccount(Order $order)
	{
		if (!isset($this->arResult["USER_ACCOUNT"]))
		{
			$dbUserAccount = CSaleUserAccount::GetList(
				[],
				[
					"USER_ID" => $order->getUserId(),
					"CURRENCY" => $order->getCurrency(),
				]
			);
			$this->arResult["USER_ACCOUNT"] = $dbUserAccount->Fetch();
		}
	}

	protected function needToRegister(): bool
	{
		global $USER;

		if (!$USER->IsAuthorized())
		{
			$isRealUserAuthorized = false;
		}
		else
		{
			$user = UserTable::getList(
				[
					'filter' => [
						'=ID' => (int)$USER->getId(),
						'=ACTIVE' => 'Y',
						'!=EXTERNAL_AUTH_ID' => $this->getExternalUserTypes()
					]
				]
			)->fetchObject();

			if ($user)
			{
				$isRealUserAuthorized = true;
			}
			else
			{
				$isRealUserAuthorized = false;
			}
		}

		if (!$isRealUserAuthorized)
		{
			return true;
		}

		return false;
	}

	protected function prepareAction()
	{
		global $USER;

		$action = $this->request->offsetExists($this->arParams['ACTION_VARIABLE'])
			? $this->request->get($this->arParams['ACTION_VARIABLE'])
			: $this->request->get('action');

		if (empty($action) || !$this->actionExists($action))
		{
			if ($this->request->get('ORDER_ID') == '')
			{
				$action = 'processOrder';
			}
			else
			{
				$action = 'showOrder';
			}
		}

		return $action;
	}

	protected function prepayOrder()
	{
		if ($this->prePaymentService && $this->prePaymentService->isPrePayable() && $this->request->get('paypal') == 'Y')
		{
			/** @var Payment $payment */
			$payment = $this->getExternalPayment($this->order);
			if ($payment)
			{
				$this->prePaymentService->setOrderDataForPrePayment(
					[
						'ORDER_ID' => $this->order->getId(),
						'PAYMENT_ID' => $payment->getId(),
						'ORDER_PRICE' => $payment->getSum(),
						'DELIVERY_PRICE' => $this->order->getDeliveryPrice(),
						'TAX_PRICE' => $this->order->getTaxPrice(),
					]
				);

				$orderData = [];
				/** @var Sale\BasketItem $item */
				foreach ($this->order->getBasket() as $item)
					$orderData['BASKET_ITEMS'][] = $item->getFieldValues();

				$this->prePaymentService->payOrderByPrePayment($orderData);
			}
		}
	}

	protected function recalculatePayment(Order $order)
	{
		$res = $order->getShipmentCollection()->calculateDelivery();

		[$sumToSpend, $innerPaySystemList] = $this->getInnerPaySystemInfo($order, true);

		$innerPayment = $this->getInnerPayment($order);
		if (!empty($innerPayment))
		{
			if ($this->arUserResult['PAY_CURRENT_ACCOUNT'] === 'Y' && $sumToSpend > 0)
			{
				$innerPayment->setField('SUM', $sumToSpend);
			}
			else
			{
				$innerPayment->delete();
				$innerPayment = null;
			}

			if ($sumToSpend > 0)
			{
				$this->arPaySystemServiceAll = $innerPaySystemList;
				$this->arActivePaySystems += $innerPaySystemList;
			}
		}

		/** @var Payment $innerPayment */
		$innerPayment = $this->getInnerPayment($order);
		/** @var Payment $extPayment */
		$extPayment = $this->getExternalPayment($order);

		$remainingSum = empty($innerPayment) ? $order->getPrice() : $order->getPrice() - $innerPayment->getSum();
		if ($remainingSum > 0 || $order->getPrice() == 0)
		{
			$paymentCollection = $order->getPaymentCollection();
			$innerPaySystemId = PaySystem\Manager::getInnerPaySystemId();
			$extPaySystemId = (int)$this->arUserResult['PAY_SYSTEM_ID'];

			if (empty($extPayment))
			{
				$extPayment = $paymentCollection->createItem();
			}

			$extPayment->setField('SUM', $remainingSum);

			$extPaySystemList = PaySystem\Manager::getListWithRestrictions($extPayment);
			// we already checked restrictions for inner pay system (could be different by price restrictions)
			if (empty($innerPaySystemList[$innerPaySystemId]))
			{
				unset($extPaySystemList[$innerPaySystemId]);
			}
			elseif (empty($extPaySystemList[$innerPaySystemId]))
			{
				$extPaySystemList[$innerPaySystemId] = $innerPaySystemList[$innerPaySystemId];
			}

			$this->arPaySystemServiceAll = $extPaySystemList;
			$this->arActivePaySystems += $extPaySystemList;

			if ($extPaySystemId !== 0 && array_key_exists($extPaySystemId, $this->arPaySystemServiceAll))
			{
				$selectedPaySystem = $this->arPaySystemServiceAll[$extPaySystemId];
			}
			else
			{
				reset($this->arPaySystemServiceAll);

				if (key($this->arPaySystemServiceAll) == $innerPaySystemId)
				{
					if (count($this->arPaySystemServiceAll) > 1)
					{
						next($this->arPaySystemServiceAll);
					}
					elseif ($sumToSpend > 0)
					{
						$extPayment->delete();
						$extPayment = null;

						/** @var Payment $innerPayment */
						$innerPayment = $this->getInnerPayment($order);
						if (empty($innerPayment))
						{
							$innerPayment = $paymentCollection->getInnerPayment();
							if (!$innerPayment)
							{
								$innerPayment = $paymentCollection->createInnerPayment();
							}
						}

						$sumToPay = $remainingSum > $sumToSpend ? $sumToSpend : $remainingSum;
						$innerPayment->setField('SUM', $sumToPay);

						if ($order->getPrice() - $paymentCollection->getSum() > 0)
						{
							$this->addWarning(Loc::getMessage('INNER_PAYMENT_BALANCE_ERROR'), self::PAY_SYSTEM_BLOCK);

							$r = new Result();
							$r->addError(new Sale\ResultWarning(
								Loc::getMessage('INNER_PAYMENT_BALANCE_ERROR'),
								'SALE_INNER_PAYMENT_BALANCE_ERROR'
							));

							Sale\EntityMarker::addMarker($order, $innerPayment, $r);
							$innerPayment->setField('MARKED', 'Y');
						}
					}
					else
					{
						unset($this->arActivePaySystems[$innerPaySystemId]);
						unset($this->arPaySystemServiceAll[$innerPaySystemId]);
					}
				}

				$selectedPaySystem = current($this->arPaySystemServiceAll);
			}

			if (!empty($selectedPaySystem))
			{
				if ($selectedPaySystem['ID'] != $innerPaySystemId)
				{
					$codSum = 0;
					$service = PaySystem\Manager::getObjectById($selectedPaySystem['ID']);
					if ($service !== null)
					{
						$codSum = $service->getPaymentPrice($extPayment);
					}

					$extPayment->setFields([
						'PAY_SYSTEM_ID' => $selectedPaySystem['ID'],
						'PAY_SYSTEM_NAME' => $selectedPaySystem['NAME'],
						'PRICE_COD' => $codSum,
					]);

					$this->arUserResult['PAY_SYSTEM_ID'] = $selectedPaySystem['ID'];
				}
			}
			elseif (!empty($extPayment))
			{
				$extPayment->delete();
				$extPayment = null;
			}

			if (!empty($this->arUserResult['PREPAYMENT_MODE']))
			{
				$this->showOnlyPrepaymentPs($this->arUserResult['PAY_SYSTEM_ID']);
			}
		}

		if (!empty($innerPayment) && !empty($extPayment) && $remainingSum == 0)
		{
			$extPayment->delete();
			$extPayment = null;
		}
	}

	protected function registerAndLogIn($userProps)
	{
		$userId = false;
		$userData = $this->generateUserData($userProps);
		$fields = [
			'LOGIN' => $userData['NEW_LOGIN'],
			'NAME' => $userData['NEW_NAME'],
			'LAST_NAME' => $userData['NEW_LAST_NAME'],
			'PASSWORD' => $userData['NEW_PASSWORD'],
			'CONFIRM_PASSWORD' => $userData['NEW_PASSWORD_CONFIRM'],
			'EMAIL' => $userData['NEW_EMAIL'],
			'GROUP_ID' => $userData['GROUP_ID'],
			'ACTIVE' => 'Y',
			'LID' => SITE_ID,
			'PERSONAL_PHONE' => isset($userProps['PHONE']) ? $this->getNormalizedPhone($userProps['PHONE']) : '',
			'PERSONAL_ZIP' => isset($userProps['ZIP']) ? $userProps['ZIP'] : '',
			'PERSONAL_STREET' => isset($userProps['ADDRESS']) ? $userProps['ADDRESS'] : '',
		];

		$user = new CUser;
		$addResult = $user->Add($fields);
		if (intval($addResult) > 0)
		{
			global $USER;

			$userId = intval($addResult);
			$USER->Authorize($addResult);

			if ($USER->IsAuthorized())
			{
				CUser::SendUserInfo($USER->GetID(), SITE_ID, "", true);
				$this->wasAuthorized = true;
			}
		}

		return $userId;
	}

	protected function saveOrder($saveToSession = false)
	{
		$arResult =& $this->arResult;

		$this->initStatGid();
		$this->initAffiliate();

		$res = $this->order->save();
		if ($res->isSuccess())
		{
			$arResult["ORDER_ID"] = $res->getId();
			$arResult["ACCOUNT_NUMBER"] = $this->order->getField('ACCOUNT_NUMBER');

			if ($this->arParams['USER_CONSENT'] === 'Y')
			{
				Main\UserConsent\Consent::addByContext(
					$this->arParams['USER_CONSENT_ID'], 'sale/order', $arResult['ORDER_ID']
				);
			}

			$fUserId = Sale\Fuser::getId();
			$siteId = SITE_ID;
			Sale\BasketComponentHelper::clearFUserBasketPrice($fUserId, $siteId);
			Sale\BasketComponentHelper::clearFUserBasketQuantity($fUserId, $siteId);
		}

		if ($arResult['HAVE_PREPAYMENT'] && empty($arResult['ERROR']))
		{
			$this->prepayOrder();
		}

		if (empty($arResult['ERROR']))
		{
			$this->saveProfileData();
		}

		if (empty($arResult['ERROR']))
		{
			$this->addStatistic();

			if ($saveToSession)
			{
				if (!is_array($_SESSION['SALE_ORDER_ID']))
				{
					$_SESSION['SALE_ORDER_ID'] = [];
				}

				$_SESSION['SALE_ORDER_ID'][] = $res->getId();
			}
		}

		foreach (GetModuleEvents('sale', 'OnSaleComponentOrderOneStepComplete', true) as $arEvent)
		{
			ExecuteModuleEventEx($arEvent, [$arResult['ORDER_ID'], $this->order->getFieldValues(), $this->arParams]);
		}
	}

	protected function saveOrderAjaxAction()
	{
		global $USER;

		$arOrderRes = [];
		if ($this->checkSession)
		{
			$this->isOrderConfirmed = true;
			$saveToSession = false;

			if ($this->needToRegister())
			{
				[$userId, $saveToSession] = $this->autoRegisterUser();
			}
			else
			{
				$userId = $USER->GetID() ? $USER->GetID() : CSaleUser::GetAnonymousUserID();
			}

			$this->order = $this->createOrder($userId);

			$isActiveUser = intval($userId) > 0 && $userId != CSaleUser::GetAnonymousUserID();
			if ($isActiveUser && empty($this->arResult['ERROR']))
			{
				if (!$this->checkOrderConsistency($this->order))
				{
					Sale\EntityMarker::addMarker($this->order, $this->order, $r);
					$this->order->setField('MARKED', 'Y');
				}

				$this->saveOrder($saveToSession);
			}

			if (empty($this->arResult["ERROR"]))
			{
				$arOrderRes["REDIRECT_URL"] = $this->arParams["~CURRENT_PAGE"]."?ORDER_ID=".urlencode($this->arResult["ACCOUNT_NUMBER"]);
				$arOrderRes["ID"] = $this->arResult["ACCOUNT_NUMBER"];
			}
			else
			{
				$arOrderRes['ERROR'] = $this->arResult['ERROR_SORTED'];
			}
		}
		$this->showAjaxAnswer(['order' => $arOrderRes, "id"=>$userId, "price"=>$this->order->getPrice(), "number"=>$this->order->getId()]);
	}

	protected function setOrderProperties(Order $order)
	{
		$propertyCollection = $order->getPropertyCollection();

		$res = $propertyCollection->setValuesFromPost(['PROPERTIES' => $this->arUserResult['ORDER_PROP']], []);

		if ($this->isOrderConfirmed)
		{
			
			/** @var Sale\PropertyValue $propertyValue */
			foreach ($propertyCollection as $propertyValue)
			{
				if ($propertyValue->isUtil())
				{
					continue;
				}

				$res = $propertyValue->verify();

				$res = $propertyValue->checkRequiredValue($propertyValue->getPropertyId(), $propertyValue->getValue());
			}
		}
	}

	protected function saveProfileData()
	{
		$arResult =& $this->arResult;
		$profileId = 0;
		$profileName = '';
		$properties = [];

		if (isset($arResult['ORDER_PROP']) && is_array($arResult['ORDER_PROP']['USER_PROFILES']))
		{
			foreach ($arResult['ORDER_PROP']['USER_PROFILES'] as $profile)
			{
				if ($profile['CHECKED'] === 'Y')
				{
					$profileId = (int)$profile['ID'];
					break;
				}
			}
		}

		$propertyCollection = $this->order->getPropertyCollection();
		if (!empty($propertyCollection))
		{
			if ($profileProp = $propertyCollection->getProfileName())
				$profileName = $profileProp->getValue();

			/** @var Sale\PropertyValue $property */
			foreach ($propertyCollection as $property)
			{
				$properties[$property->getField('ORDER_PROPS_ID')] = $property->getValue();
			}
		}

		CSaleOrderUserProps::DoSaveUserProfile(
			$this->order->getUserId(),
			$profileId,
			$profileName,
			$this->order->getPersonTypeId(),
			$properties,
			$arResult["ERROR"]
		);
	}

	protected function showAjaxAnswer($result)
	{
		global $APPLICATION;

		foreach (GetModuleEvents("sale", 'OnSaleComponentOrderShowAjaxAnswer', true) as $arEvent)
			ExecuteModuleEventEx($arEvent, [&$result]);

		$APPLICATION->RestartBuffer();

		if ($this->request->get('save') != 'Y')
			header('Content-Type: application/json');

		$user = CUser::GetByID($result["id"])->fetch();
		$description = "Заказ №{$this->order->getId()} для {$user["EMAIL"]}";
		$items = [];
		$basketItems = $this->order->getBasket()->getBasketItems();
		foreach($basketItems as $item){
			$items[] = array(
				"description" => $item->getField("NAME"),
				"quantity" => "1",
				"amount" => array(
					"value" => (string)$item->getPrice(),
					"currency" => "RUB"
				),
				"vat_code" => 1,
				"payment_mode" => "full_payment"
			);
		}

		$paymentID = $this->order->getPaymentSystemId();
		if($paymentID[0]===U_KASSA){
			$client = new Client();
			$client->setAuth("927290", "test_QozMUy4f2H_hmFnmLyPIlg2vF-sICf1l1SRmrw_MqrM");
			$payment = $client->createPayment(
				array(
					"amount" => array(
						"value" => $this->order->getPrice(),
						"currency" => "RUB"
					),
					"confirmation" => array(
						"type" => "redirect",
						"return_url" => "http://razvit.fixmaski.ru".$result["order"]["REDIRECT_URL"]
					),
					"description" => $description,
					"metadata" => array(
						"order_id" => $this->order->getId()
					),

					"receipt" => array(
						"customer" => array(
							"email" => $user["EMAIL"]
						),
						"items" => $items
					),
				),
				uniqid("", true)
			);
			echo Json::encode($payment->getConfirmation()->getConfirmationUrl());
		}
		elseif($paymentID[0]===REKVIZITY){
			
			$amo = new AMO();
			
			$name = $user["NAME"];
			if($user["LAST_NAME"]){
				$name = $user["LAST_NAME"]." ".$name;
			}
			$response = null;
			$rsUser = CIBlockElement::GetList(
				array("SORT"=>"ASC"),
				array("IBLOCK_ID"=>36, "PROPERTY_PHONE"=>$this->request->get("ORDER_PROP_2"), "PROPERTY_EMAIL"=>$user["EMAIL"]),
				false,
				false,
				array("ID", "IBLOCK_ID", "PROPERTY_PHONE", "PROPERTY_EMAIL", "PROPERTY_CONTACT_ID")
			);

			$amoOrderDescription = "";
			foreach($items as $item){
				$amoOrderDescription.=$item["description"]." - ". $item["amount"]["value"]."\n";
			}
			$orderData["description"] = $amoOrderDescription;
			$orderData["price"] = $this->order->getPrice();

			if($arUser = $rsUser->GetNext()){
				$orderData["contact_id"] = $arUser["PROPERTY_CONTACT_ID_VALUE"];
				$response = $amo->setLead($orderData);
				$response = $amo->setNote($response["_embedded"]["leads"][0]["id"], $orderData);
			}
			else{
				$contact["name"] = $name;
				$contact["phone"] = $this->request->get("ORDER_PROP_2");
				$contact["email"] = $user["EMAIL"];
				$response = $amo->setComplexLead($orderData, $contact);
				$newUser = new CIBlockElement;
				$PROP = array(
					"PHONE"=>$this->request->get("ORDER_PROP_2"),
					"CONTACT_ID"=>(int)$response[0]["contact_id"],
					"EMAIL" => $user["EMAIL"]
				);

				$arUserFields = [
					"NAME"=>uniqid(),
					"PROPERTY_VALUES"=>$PROP,
					"IBLOCK_ID"=>36,
					"ACTIVE"=>"Y"
				];
				$newUser->Add($arUserFields);
				$response = $amo->setNote($response[0]["id"], $orderData);
			}
			if($amo->getCode()=="200"){
				$response = "http://razvit.fixmaski.ru".$result["order"]["REDIRECT_URL"];
			}
			else{
				$response = "http://razvit.fixmaski.ru".$result["order"]["REDIRECT_URL"]."&error_code=".$amo->getCode();	
			}
			echo Json::encode($response);
		}
		else{
			echo Json::encode("");
		}

		if($this->wasAuthorized){
			$USER = new CUser;
			$USER->Logout();
		}

		CMain::FinalActions();
		die();
	}

	protected function showEmptyBasket()
	{
		global $APPLICATION;

		if ($this->action === 'saveOrderAjax')
		{
			$APPLICATION->RestartBuffer();
			echo json_encode([
				'order' => [
					'REDIRECT_URL' => $this->arParams['~CURRENT_PAGE'],
				],
			]);
			die();
		}

		if ($this->arParams['DISABLE_BASKET_REDIRECT'] === 'Y')
		{
			$this->arResult['SHOW_EMPTY_BASKET'] = true;

			if ($this->request->get('json') === 'Y' || $this->isRequestViaAjax)
			{
				$APPLICATION->RestartBuffer();
				echo json_encode([
					'success' => 'N',
					'redirect' => $this->arParams['~CURRENT_PAGE'],
				]);
				die();
			}
		}
		else
		{
			if ($this->request->get('json') === 'Y' || $this->isRequestViaAjax)
			{
				$APPLICATION->RestartBuffer();
				echo json_encode([
					'success' => 'N',
					'redirect' => $this->arParams['PATH_TO_BASKET'],
				]);
				die();
			}

			LocalRedirect($this->arParams['PATH_TO_BASKET']);
			die();
		}
	}

	protected function showOnlyPrepaymentPs($paySystemId)
	{
		if (empty($this->arPaySystemServiceAll) || intval($paySystemId) == 0)
			return;

		foreach ($this->arPaySystemServiceAll as $key => $psService)
		{
			if ($paySystemId != $psService['ID'])
			{
				unset($this->arPaySystemServiceAll[$key]);
				unset($this->arActivePaySystems[$key]);
			}
		}
	}

	protected function synchronize($arModifiedResult, Order $order)
	{
		$modifiedFields = self::arrayDiffRecursive($arModifiedResult, $this->arUserResult);

		if (!empty($modifiedFields))
			$this->synchronizeOrder($modifiedFields, $order);
	}

	protected function synchronizeOrder($modifiedFields, Order $order)
	{
		if (!empty($modifiedFields) && is_array($modifiedFields))
		{
			$recalculatePayment = $modifiedFields['CALCULATE_PAYMENT'] === true;
			unset($modifiedFields['CALCULATE_PAYMENT']);
			$recalculateDelivery = false;

			if (!empty($modifiedFields['PERSON_TYPE_ID']))
			{
				$order->setPersonTypeId($modifiedFields['PERSON_TYPE_ID']);
			}

			$propertyCollection = $order->getPropertyCollection();

			foreach ($modifiedFields as $field => $value)
			{
				switch ($field)
				{
					case 'PAY_SYSTEM_ID':
						$recalculatePayment = true;
						break;
					case 'PAY_CURRENT_ACCOUNT':
						$recalculatePayment = true;
						break;
					case 'DELIVERY_ID':
						$recalculateDelivery = true;
						break;
					case 'ORDER_PROP':
						if (is_array($value))
						{
							/** @var Sale\PropertyValue $property */
							foreach ($propertyCollection as $property)
							{
								if (array_key_exists($property->getPropertyId(), $value))
								{
									$property->setValue($value[$property->getPropertyId()]);
									$arProperty = $property->getProperty();
									if ($arProperty['IS_LOCATION'] === 'Y' || $arProperty['IS_ZIP'] === 'Y')
									{
										$recalculateDelivery = true;
									}
								}
							}
						}

						break;
					case 'ORDER_DESCRIPTION':
						$order->setField('USER_DESCRIPTION', $value);
						break;
					case 'DELIVERY_LOCATION':
						$codeValue = CSaleLocation::getLocationCODEbyID($value);
						if ($property = $propertyCollection->getDeliveryLocation())
						{
							$property->setValue($codeValue);
							$this->arUserResult['ORDER_PROP'][$property->getPropertyId()] = $codeValue;
						}

						$recalculateDelivery = true;
						break;
					case 'DELIVERY_LOCATION_BCODE':
						if ($property = $propertyCollection->getDeliveryLocation())
						{
							$property->setValue($value);
							$this->arUserResult['ORDER_PROP'][$property->getPropertyId()] = $value;
						}

						$recalculateDelivery = true;
						break;
					case 'DELIVERY_LOCATION_ZIP':
						if ($property = $propertyCollection->getDeliveryLocationZip())
						{
							$property->setValue($value);
							$this->arUserResult['ORDER_PROP'][$property->getPropertyId()] = $value;
						}

						$recalculateDelivery = true;
						break;
					case 'TAX_LOCATION':
						$codeValue = CSaleLocation::getLocationCODEbyID($value);
						if ($property = $propertyCollection->getTaxLocation())
						{
							$property->setValue($codeValue);
							$this->arUserResult['ORDER_PROP'][$property->getPropertyId()] = $codeValue;
						}

						break;
					case 'TAX_LOCATION_BCODE':
						if ($property = $propertyCollection->getTaxLocation())
						{
							$property->setValue($value);
							$this->arUserResult['ORDER_PROP'][$property->getPropertyId()] = $value;
						}

						break;
					case 'PAYER_NAME':
						if ($property = $propertyCollection->getPayerName())
						{
							$property->setValue($value);
							$this->arUserResult['ORDER_PROP'][$property->getPropertyId()] = $value;
						}

						break;
					case 'USER_EMAIL':
						if ($property = $propertyCollection->getUserEmail())
						{
							$property->setValue($value);
							$this->arUserResult['ORDER_PROP'][$property->getPropertyId()] = $value;
						}

						break;
					case 'PROFILE_NAME':
						if ($property = $propertyCollection->getProfileName())
						{
							$property->setValue($value);
							$this->arUserResult['ORDER_PROP'][$property->getPropertyId()] = $value;
						}

						break;
				}

				$this->arUserResult[$field] = $value;
			}
		}
	}

	private function loadBasket()
	{
		$registry = Sale\Registry::getInstance(Sale\Registry::REGISTRY_TYPE_ORDER);

		/** @var Sale\Basket $basketClassName */
		$basketClassName = $registry->getBasketClassName();

		return $basketClassName::loadItemsForFUser(Sale\Fuser::getId(), SITE_ID);
	}

}

$oh = new OrderHandler();
$oh->init($arParams, $request, $arResult);
$oh->executeComponent();
?>