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
<h1 class="review-page__title desktop"><?=$arResult["NAME"]?></h1>
<div class="review-page__content">
    <div class="review-page__image-block">
        <div class="review-page__img-bg">
            <img src="<?=SITE_TEMPLATE_PATH?>/img/review-page-img-bg.svg" alt="">
        </div>
        <button type="button" class="review-page__img-box review-img">
            <img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arResult["PREVIEW_PICTURE"]["ALT"]?>">
            <img class="review-page__review-doc review-doc" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>">
        </button>
    </div>
    <div class="review-page__title mobile"><?=$arResult["NAME"]?></div>
    <article class="review-page__text">
        <?=$arResult["~PREVIEW_TEXT"]?>
    </article>
</div>