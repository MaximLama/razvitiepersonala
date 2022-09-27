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
<section class="about-content about-content--limited">
    <article class="container">
        <h1 class="about-content__title"><?=$arResult["NAME"]?></h1>
        <div class="about-content__sub-title"><?=$arResult["PROPERTIES"]["PROF_STANDARTY_ZAGOLOVOK"]["VALUE"]?></div>
        <?=$arResult["PROPERTIES"]["PROF_STANDARTY"]["~VALUE"]["TEXT"]?>
    </article>
    <button class="about-content__next about-content__text mobile">
        Читать далее...
    </button>
</section>
<section class="about-content">
    <article class="container">
        <div class="about-content__sub-title"><?=$arResult["PROPERTIES"]["ZAKONODATELSTVO_ZAGOLOVOK"]["VALUE"]?></div>
        <?=$arResult["PROPERTIES"]["ZAKONODATELSTVO"]["~VALUE"]["TEXT"]?>
    </article>
</section>