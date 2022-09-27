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
<h1 class="about-content__title"><?=$arResult["NAME"]?></h1>
<p class="about-content__text">
    <?=$arResult['PROPERTIES']["NAZVANIE_KOMPANII"]["~VALUE"]["TEXT"]?>
</p>
<p class="about-content__text">
    <span class="about-content__text--bold"><?=$arResult["PROPERTIES"]["UCHREDITELI_ZAGOLOVOK"]["VALUE"]?></span>
    <br><span class="about-content__text--gray"><?=$arResult["PROPERTIES"]["ADRES_YURIDICHESKIJ_ZAGOLOVOK"]["VALUE"]?></span> <?=$arResult["PROPERTIES"]["ADRES_YURIDICHESKIJ"]["VALUE"]?>
    <br><span class="about-content__text--gray"><?=$arResult["PROPERTIES"]["ADRES_MESTONAHOZHDENIYA_ZAGOLOVOK"]["VALUE"]?></span> <?=$arResult["PROPERTIES"]["ADRES_MESTONAHOZHDENIYA"]["VALUE"]?>
    <br><span class="about-content__text--gray"><?=$arResult["PROPERTIES"]["GENERALNYJ_DIREKTOR_ZAGOLOVOK"]["VALUE"]?></span> <?=$arResult["PROPERTIES"]["GENERALNYJ_DIREKTOR"]["VALUE"]?>
</p>
<p class="about-content__text">
    <span class="about-content__text--bold"><?=$arResult["PROPERTIES"]["KONTAKTY_ZAGOLOVOK"]["VALUE"]?></span>
    <br><span class="about-content__text--gray"><?=$arResult["PROPERTIES"]["TELEFON_ZAGOLOVOK"]["VALUE"]?></span> <?=$arResult["PROPERTIES"]["TELEFON"]["VALUE"]?>
    <br><span class="about-content__text--gray"><?=$arResult["PROPERTIES"]["POCHTA_ZAGOLOVOK"]["VALUE"]?></span> <?=$arResult["PROPERTIES"]["POCHTA"]["VALUE"]?>
    <br><span class="about-content__text--gray"><?=$arResult["PROPERTIES"]["SAJT_ZAGOLOVOK"]["VALUE"]?></span> <?=$arResult["PROPERTIES"]["SAJT"]["VALUE"]?>
</p>
<p class="about-content__text">
    <span class="about-content__text--bold"><?=$arResult["PROPERTIES"]["REZHIM_I_GRAFIK_RABOTY_ZAGOLOVOK"]["VALUE"]?></span>
    <br><span class="about-content__text--gray"><?=$arResult["PROPERTIES"]["RABOCHIE_DNI_ZAGOLOVOK"]["VALUE"]?></span> <?=$arResult["PROPERTIES"]["RABOCHIE_DNI"]["VALUE"]?>
    <br><span class="about-content__text--gray"><?=$arResult["PROPERTIES"]["VYHODNYE_DNI_ZAGOLOVOK"]["VALUE"]?></span> <?=$arResult["PROPERTIES"]["VYHODNYE_DNI"]["VALUE"]?>
</p>
<p class="about-content__text">
    <span class="about-content__text--bold"><?=$arResult["PROPERTIES"]["GRAFIK_OBUCHENIYA_ZAGOLOVOK"]["VALUE"]?></span>
    <br><span class="about-content__text--gray"><?=$arResult["PROPERTIES"]["RABOCHIE_DNI_OBUCHENIE_ZAGOLOVOK"]["VALUE"]?></span> <?=$arResult["PROPERTIES"]["RABOCHIE_DNI_OBUCHENIE"]["VALUE"]?>
    <br><span class="about-content__text--gray"><?=$arResult["PROPERTIES"]["VYHODNYE_DNI_OBUCHENIE_ZAGOLOVOK"]["VALUE"]?></span> <?=$arResult["PROPERTIES"]["VYHODNYE_DNI_OBUCHENIE"]["VALUE"]?>
</p>
<p class="about-content__text">
    <span class="about-content__text--bold"><?=$arResult["PROPERTIES"]["REKVIZITY_KOMPANII_ZAGOLOVOK"]["VALUE"]?></span>
    <br><span class="about-content__text--gray"><?=$arResult["PROPERTIES"]["INN_ZAGOLOVOK"]["VALUE"]?></span> <?=$arResult["PROPERTIES"]["INN"]["VALUE"]?>
    <br><span class="about-content__text--gray"><?=$arResult["PROPERTIES"]["RS_ZAGOLOVOK"]["VALUE"]?></span> <?=$arResult["PROPERTIES"]["RS"]["VALUE"]?>
    <br><span class="about-content__text--gray"><?=$arResult["PROPERTIES"]["LS_ZAGOLOVOK"]["VALUE"]?></span> <?=$arResult["PROPERTIES"]["LS"]["VALUE"]?>
</p>