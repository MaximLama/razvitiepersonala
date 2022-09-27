<section class="contacts">
	<div class="container">
		<div class="contacts__info-content">
			<div class="contacts__text-block">
				
				<?$APPLICATION->IncludeComponent(
					"bitrix:news.detail",
					"kontakty",
					Array(
						"ACTIVE_DATE_FORMAT" => "d.m.Y",
						"ADD_ELEMENT_CHAIN" => "N",
						"ADD_SECTIONS_CHAIN" => "N",
						"AJAX_MODE" => "N",
						"AJAX_OPTION_ADDITIONAL" => "",
						"AJAX_OPTION_HISTORY" => "N",
						"AJAX_OPTION_JUMP" => "N",
						"AJAX_OPTION_STYLE" => "Y",
						"BROWSER_TITLE" => "-",
						"CACHE_GROUPS" => "Y",
						"CACHE_TIME" => "36000000",
						"CACHE_TYPE" => "A",
						"CHECK_DATES" => "Y",
						"DETAIL_URL" => "",
						"DISPLAY_BOTTOM_PAGER" => "Y",
						"DISPLAY_DATE" => "N",
						"DISPLAY_NAME" => "Y",
						"DISPLAY_PICTURE" => "Y",
						"DISPLAY_PREVIEW_TEXT" => "Y",
						"DISPLAY_TOP_PAGER" => "N",
						"ELEMENT_CODE" => "",
						"ELEMENT_ID" => 13,
						"FIELD_CODE" => array("",""),
						"FILE_404" => "",
						"IBLOCK_ID" => "5",
						"IBLOCK_TYPE" => "odinochnye_bloki",
						"IBLOCK_URL" => "",
						"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
						"MESSAGE_404" => "",
						"META_DESCRIPTION" => "-",
						"META_KEYWORDS" => "-",
						"PAGER_BASE_LINK_ENABLE" => "N",
						"PAGER_SHOW_ALL" => "N",
						"PAGER_TEMPLATE" => ".default",
						"PAGER_TITLE" => "Страница",
						"PROPERTY_CODE" => array("ADRES_OFISA","ADRES_OFISA_ZAGOLOVOK","OSTAVIT_ZAYAVKU","POCHTOVYJ_ADRES","POCHTOVYJ_ADRES_ZAGOLOVOK",""),
						"SET_BROWSER_TITLE" => "N",
						"SET_CANONICAL_URL" => "N",
						"SET_LAST_MODIFIED" => "N",
						"SET_META_DESCRIPTION" => "N",
						"SET_META_KEYWORDS" => "N",
						"SET_STATUS_404" => "Y",
						"SET_TITLE" => "N",
						"SHOW_404" => "Y",
						"STRICT_SECTION_CHECK" => "N",
						"USE_PERMISSIONS" => "N",
						"USE_SHARE" => "N"
					)
				);?>

			</div>
			<div class="contacts__data-block">
				
				<?$APPLICATION->IncludeComponent(
					"bitrix:news.detail", 
					"kontakty-obshie-dannye", 
					array(
						"ACTIVE_DATE_FORMAT" => "d.m.Y",
						"ADD_ELEMENT_CHAIN" => "N",
						"ADD_SECTIONS_CHAIN" => "N",
						"AJAX_MODE" => "N",
						"AJAX_OPTION_ADDITIONAL" => "",
						"AJAX_OPTION_HISTORY" => "N",
						"AJAX_OPTION_JUMP" => "N",
						"AJAX_OPTION_STYLE" => "Y",
						"BROWSER_TITLE" => "-",
						"CACHE_GROUPS" => "Y",
						"CACHE_TIME" => "36000000",
						"CACHE_TYPE" => "A",
						"CHECK_DATES" => "Y",
						"DETAIL_URL" => "",
						"DISPLAY_BOTTOM_PAGER" => "Y",
						"DISPLAY_DATE" => "N",
						"DISPLAY_NAME" => "N",
						"DISPLAY_PICTURE" => "Y",
						"DISPLAY_PREVIEW_TEXT" => "Y",
						"DISPLAY_TOP_PAGER" => "N",
						"ELEMENT_CODE" => "",
						"ELEMENT_ID" => "2",
						"FIELD_CODE" => array(
							0 => "",
							1 => "",
						),
						"IBLOCK_ID" => "2",
						"IBLOCK_TYPE" => "odinochnye_bloki",
						"IBLOCK_URL" => "",
						"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
						"MESSAGE_404" => "",
						"META_DESCRIPTION" => "-",
						"META_KEYWORDS" => "-",
						"PAGER_BASE_LINK_ENABLE" => "N",
						"PAGER_SHOW_ALL" => "N",
						"PAGER_TEMPLATE" => ".default",
						"PAGER_TITLE" => "Страница",
						"PROPERTY_CODE" => array(
							0 => "GRAFIK",
							1 => "NOMER_TELEFONA",
							2 => "POCHTA",
							3 => "",
						),
						"SET_BROWSER_TITLE" => "N",
						"SET_CANONICAL_URL" => "N",
						"SET_LAST_MODIFIED" => "N",
						"SET_META_DESCRIPTION" => "N",
						"SET_META_KEYWORDS" => "N",
						"SET_STATUS_404" => "N",
						"SET_TITLE" => "N",
						"SHOW_404" => "N",
						"STRICT_SECTION_CHECK" => "N",
						"USE_PERMISSIONS" => "N",
						"USE_SHARE" => "N",
						"COMPONENT_TEMPLATE" => "kontakty-obshie-dannye"
					),
					false
				);?>
				
			</div>
		</div>
		<div class="contacts__map-block">
			<iframe src="https://yandex.by/map-widget/v1/-/CCUBF8FZ2B"></iframe>
		</div>
	</div>
</section>