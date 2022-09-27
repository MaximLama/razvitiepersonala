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
<div class="more-center__title"><?=$arResult["~NAME"]?></div>
<div class="more-center__content">
    <div class="more-center__img-box">
        <img src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>">
    </div>
    <article class="more-center__text-block">
        <a href="https://obrnadzor.gov.ru/gosudarstvennye-uslugi-i-funkczii/7701537808-gosfunction/formirovanie-i-vedenie-federalnogo-reestra-svedenij-o-dokumentah-ob-obrazovanii-i-ili-o-kvalifikaczii-dokumentah-ob-obuchenii/#service-search" class="more-center__link">
            <?=$arResult["PROPERTIES"]["TEKST_SSYLKI_GOS_REESTRA"]["VALUE"]?>
        </a>
        <ul class="more-center__list">
            <?=$arResult["~DETAIL_TEXT"]?>
        </ul>
    </article>
</div>