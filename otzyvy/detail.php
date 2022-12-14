<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "ИБХ РАН");
?>

<section class="review-page">
	<div class="review-page__review-doc-bg review-doc-bg"></div>
    <div class="container">

        <?$APPLICATION->IncludeComponent(
			"bitrix:news.detail",
			"review",
			Array(
				"ACTIVE_DATE_FORMAT" => "d.m.Y",
				"ADD_ELEMENT_CHAIN" => "Y",
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
				"ELEMENT_CODE" => $_REQUEST["ELEMENT_CODE"],
				"ELEMENT_ID" => "",
				"FIELD_CODE" => array("NAME", "PREVIEW_TEXT", "PREVIEW_PICTURE", "DETAIL_PICTURE", ""),
				"FILE_404" => "",
				"IBLOCK_ID" => "18",
				"IBLOCK_TYPE" => "osnovnye_dannye",
				"IBLOCK_URL" => "",
				"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
				"MESSAGE_404" => "",
				"META_DESCRIPTION" => "-",
				"META_KEYWORDS" => "-",
				"PAGER_BASE_LINK_ENABLE" => "N",
				"PAGER_SHOW_ALL" => "N",
				"PAGER_TEMPLATE" => ".default",
				"PAGER_TITLE" => "Страница",
				"PROPERTY_CODE" => array("ZAGOLOVOK", ""),
				"SET_BROWSER_TITLE" => "Y",
				"SET_CANONICAL_URL" => "N",
				"SET_LAST_MODIFIED" => "Y",
				"SET_META_DESCRIPTION" => "Y",
				"SET_META_KEYWORDS" => "Y",
				"SET_STATUS_404" => "Y",
				"SET_TITLE" => "Y",
				"SHOW_404" => "Y",
				"STRICT_SECTION_CHECK" => "N",
				"USE_PERMISSIONS" => "N",
				"USE_SHARE" => "N"
			)
		);?>
		
    </div>
</section>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"klienty",
	Array(
		"AREA_FILE_SHOW" => "file",
		"AREA_FILE_SUFFIX" => "",
		"EDIT_TEMPLATE" => "standard.php",
		"PATH" => SITE_TEMPLATE_PATH.'/includes/klienty.php'
	)
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>