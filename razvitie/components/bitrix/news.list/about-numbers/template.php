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
<div class="about-numbers__title">Наша компания в цифрах</div>
<div class="about-numbers__cards">
	
	<?foreach($arResult["ITEMS"] as $key=>$arItem):?>
	    <div class="about-numbers__item">
	        <div class="about-numbers__card-number">0<?=($key+1)?></div>
	        <p class="about-numbers__number"><?=$arItem["PROPERTIES"]["CHISLO"]["VALUE"]?></p>
	        <p class="about-numbers__text"><?=$arItem["PROPERTIES"]["PODPIS"]["VALUE"]?></p>
	    </div>
	<?endforeach;?>

</div>