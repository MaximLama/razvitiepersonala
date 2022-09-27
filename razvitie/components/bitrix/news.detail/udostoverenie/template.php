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
<div class="about-content__title"><?=$arResult["NAME"]?></div>
<p class="about-content__text">
    <?=$arResult["PROPERTIES"]["OPISANIE"]["~VALUE"]["TEXT"]?>
</p>
<a href="https://obrnadzor.gov.ru/gosudarstvennye-uslugi-i-funkczii/7701537808-gosfunction/formirovanie-i-vedenie-federalnogo-reestra-svedenij-o-dokumentah-ob-obrazovanii-i-ili-o-kvalifikaczii-dokumentah-ob-obuchenii/#service-search" class="about-content__text about-content__text--link about-content__text--block">
    <?=$arResult["PROPERTIES"]["TEKST_SSYLKI"]["VALUE"]?>
</a>