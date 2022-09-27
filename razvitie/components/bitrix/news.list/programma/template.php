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
<?foreach($arResult["ITEMS"] as $key=>$arItem):?>
	<div class="partner-stages__item">
        <div class="partner-stages__item-bg-line desktop">
            <img src="<?=SITE_TEMPLATE_PATH?>/img/partner-stages-line.svg" alt="">
        </div>
        <div class="partner-stages__item-number">0<?=($key+1)?></div>
        <div class="partner-stages__item-icon">
            <img src="<?=CFile::GetPath($arItem["PROPERTIES"]["IKONKA"]["VALUE"])?>" alt="">
        </div>
        <div class="partner-stages__item-title"><?=$arItem["NAME"]?></div>
    </div>
<?endforeach;?>