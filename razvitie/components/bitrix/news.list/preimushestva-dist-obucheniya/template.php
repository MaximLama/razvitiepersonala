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
	<div class="advantages__item">
	    <div class="advantages__number">0<?=($key+1)?></div>
	    <div class="advantages__icon">
	        <img src="<?=CFile::GetPath($arItem["PROPERTIES"]["IKONKA"]["VALUE"])?>" alt="">
	    </div>
	    <div class="advantages__item-title"><?=$arItem["NAME"]?></div>
	    <p class="advantages__item-text">
	        <?=$arItem["~PREVIEW_TEXT"]?>
	    </p>
	</div>
<?endforeach;?>