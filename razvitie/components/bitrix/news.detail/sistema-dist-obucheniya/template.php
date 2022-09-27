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
<div class="dist-learning__img-box">
    <img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arResult["PREVIEW_PICTURE"]["ALT"]?>">
</div>
<div class="dist-learning__text-block">
    <div class="dist-learning__sub-title">
        <?=$arResult["PREVIEW_TEXT"]?>
    </div>
    <div class="dist-learning__title">
        <?=$arResult["NAME"]?>
    </div>
    <?=$arResult["~DETAIL_TEXT"]?>
</div>