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
	<span class="about-content__text--bold"><?=$arResult["PROPERTIES"]["UROVNI_OBRAZOVANIYA_ZAGOLOVOK"]["VALUE"]?></span>
	<?foreach($arResult["PROPERTIES"]["UROVNI_OBRAZOVANIYA"]["VALUE"] as $value):?>
		<br><?=$value?>
	<?endforeach;?>
</p>
<ul class="about-content__list about-content__text">
	<span class="about-content__text about-content__text--bold"><?=$arResult["PROPERTIES"]["FORMY_OBUCHENIYA_ZAGOLOVOK"]["VALUE"]?></span>
	<?foreach($arResult["PROPERTIES"]["FORMY_OBUCHENIYA"]["VALUE"] as $value):?>
		<li> <?=$value?> </li>
	<?endforeach;?>
</ul>
<ul class="about-content__list about-content__text">
	<span class="about-content__text about-content__text--bold"><?=$arResult["PROPERTIES"]["SROKI_OBUCHENIYA_ZAGOLOVOK"]["VALUE"]?></span>
	<?foreach($arResult["PROPERTIES"]["SROKI_OBUCHENIYA"]["VALUE"] as $value):?>
		<li> <?=$value?> </li>
	<?endforeach;?>
</ul>
<ul class="about-content__list about-content__text">
	<span class="about-content__text about-content__text--bold"><?=$arResult["PROPERTIES"]["YAZYK_OBUCHENIYA_ZAGOLOVOK"]["VALUE"]?></span>
	<?foreach($arResult["PROPERTIES"]["YAZYK_OBUCHENIYA"]["VALUE"] as $value):?>
		<li> <?=$value?> </li>
	<?endforeach;?>
</ul>
<a href="/obuchenie-i-attestatsiya/" class="about-content__btn">
	<span><?=$arResult["PROPERTIES"]["PROGRAMMY_SSYLKA"]["VALUE"]?></span>
	<svg viewBox="0 0 26 15" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M24.5 7.5H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1C18.5 1 19 3.36364 20.5 5C22.1874 6.84075 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14C18.5 14 19 11.6364 20.5 10C22.1874 8.15925 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/>
    </svg>
</a> 