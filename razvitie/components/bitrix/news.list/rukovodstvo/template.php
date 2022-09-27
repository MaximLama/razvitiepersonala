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
<h1 class="team__title"><?=$arResult["SECTION"]["PATH"][0]["NAME"]?></h1>
<div class="team__cards">
	<?foreach($arResult["ITEMS"] as $arItem):?>
	    <div class="team__item">
	        <div class="team__img-box">
	            <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>">
	        </div>
	        <article class="team__text-block">
	            <div class="team__speciality">
	                <?=$arItem["PREVIEW_TEXT"]?>
	            </div>
	            <div class="team__item-title">
	                <?=$arItem["NAME"]?>
	            </div>
	            <p class="team__item-text">
	                <?=$arItem["~DETAIL_TEXT"]?>
	            </p>
	        </article>
	    </div>
	<?endforeach;?>
</div>