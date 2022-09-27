<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<?
$ids = [];
foreach ($arResult["ITEMS"] as $item) {
	$ids[] = $item["PROPERTIES"]["NAPRAVLENIE"]["VALUE"];
}
$res = CIBlockSection::GetList(
	array("ID"=>$ids),
	array("IBLOCK_ID"=>26, "ID"=>$ids),
	false,
	array("ID", "SECTION_PAGE_URL"),
	false
);
$sections = [];
while($sect = $res->GetNext()){
	$sections[$sect["ID"]] = $sect;
}
?>
<?foreach ($arResult["ITEMS"] as $key => $value):?>
	<a href="<?=$sections[$value["PROPERTIES"]["NAPRAVLENIE"]["VALUE"]]["SECTION_PAGE_URL"]?>" class="about-intro__link-category"><?=$value["NAME"]?></a>
<?endforeach;?>