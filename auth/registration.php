<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?
$APPLICATION->IncludeComponent(
	"bitrix:main.register",
	"registration",
	Array(
		"AUTH" => "Y",
		"REQUIRED_FIELDS" => array("NAME", "EMAIL"),
		"SET_TITLE" => "Y",
		"SHOW_FIELDS" => array("NAME", "EMAIL"),
		//"SUCCESS_PAGE" => "/personal/",
		"USER_PROPERTY" => array(),
		"USER_PROPERTY_NAME" => "",
		"USE_BACKURL" => "Y"
	)
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>