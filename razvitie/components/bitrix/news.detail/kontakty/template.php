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
<h1 class="contacts__title"><?=$arResult["NAME"]?></h1>
<div class="contacts__address-block contacts__address-block--office">
	<div class="contacts__address-block-name"><?=$arResult["PROPERTIES"]["ADRES_OFISA_ZAGOLOVOK"]["VALUE"]?></div>
	<div class="contacts__address">
		<?=$arResult["PROPERTIES"]["ADRES_OFISA"]["VALUE"]?>
	</div>
</div>
<div class="contacts__address-block contacts__address-block--mail">
	<div class="contacts__address-block-name"><?=$arResult["PROPERTIES"]["POCHTOVYJ_ADRES_ZAGOLOVOK"]["VALUE"]?></div>
	<div class="contacts__address">
		<?=$arResult["PROPERTIES"]["POCHTOVYJ_ADRES"]["VALUE"]?>
	</div>
</div>
<a href="#feedback" class="contacts__btn">
	<span><?=$arResult["PROPERTIES"]["OSTAVIT_ZAYAVKU"]["VALUE"]?></span>
	<svg viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M24.5 8.11523H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1.61523C18.5 1.61523 19 3.97887 20.5 5.61523C22.1874 7.45598 25 8.11523 25 8.11523" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14.6152C18.5 14.6152 19 12.2516 20.5 10.6152C22.1874 8.77449 25 8.11523 25 8.11523" stroke-width="2" stroke-linecap="round"/>
	</svg>
</a>