<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
<? foreach ($arResult["ITEMS"] as $item): ?>
    <div class="main-intro__item swiper-slide">
        <div class="main-intro__title"><?= $item["NAME"] ?></div>
        <div class="main-intro__item-text-box">
            <a href="<?= $item["PROPERTIES"]["SSYLKA"]["VALUE"] ?>" class="main-intro__slider-btn">
                <span>Узнать подробнее</span>
                <svg viewBox="0 0 26 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M24.5 7.5H1" stroke-width="2" stroke-linecap="round"/>
                    <path d="M18.5 1C18.5 1 19 3.36364 20.5 5C22.1874 6.84075 25 7.5 25 7.5" stroke-width="2"
                          stroke-linecap="round"/>
                    <path d="M18.5 14C18.5 14 19 11.6364 20.5 10C22.1874 8.15925 25 7.5 25 7.5" stroke-width="2"
                          stroke-linecap="round"/>
                </svg>
            </a>

            <p class="main-intro__description">
                <?= $item["PREVIEW_TEXT"] ?>
            </p>
        </div>
    </div>
<? endforeach; ?>
