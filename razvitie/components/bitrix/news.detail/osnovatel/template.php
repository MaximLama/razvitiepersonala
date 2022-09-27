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
<div class="boss__img-box">
    <img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arResult["PREVIEW_PICTURE"]["ALT"]?>">
</div>
<article class="boss__text-block">
    <div class="boss__sub-title">
        <?=$arResult["PREVIEW_TEXT"]?>
    </div>
    <div class="boss__title">
        <?=$arResult["NAME"]?>
    </div>
    <p class="boss__text">
        <?=$arResult["DETAIL_TEXT"]?>
    </p>
    <div class="boss__buttons">
        <a href="/obuchenie-i-attestatsiya/" class="boss__link boss__link--blue">
            <span class="desktop"><?=$arResult["PROPERTIES"]["USLUGI_SSYLKA"]["VALUE"]?></span>
            <span class="mobile">Смотреть услуги</span>
            <svg viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M24.5 7.6543H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1.1543C18.5 1.1543 19 3.51793 20.5 5.1543C22.1874 6.99504 25 7.6543 25 7.6543" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14.1543C18.5 14.1543 19 11.7907 20.5 10.1543C22.1874 8.31355 25 7.6543 25 7.6543" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </a>
        <a href="/o-nas/rukovodstvo-i-pedagogicheskiy-sostav/" class="boss__link boss__link--white">
            <span class="desktop"><?=$arResult["PROPERTIES"]["PODROBNEE_OB_OSNOVATELE"]["VALUE"]?></span>
            <span class="mobile">Подробнее</span>
            <svg viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M24.5 7.6543H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1.1543C18.5 1.1543 19 3.51793 20.5 5.1543C22.1874 6.99504 25 7.6543 25 7.6543" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14.1543C18.5 14.1543 19 11.7907 20.5 10.1543C22.1874 8.31355 25 7.6543 25 7.6543" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </a>
    </div>
</article>