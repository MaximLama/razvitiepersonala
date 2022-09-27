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
$this->setFrameMode(true);?>
<?
$value = "";
if(isset($_REQUEST['q'])&&$_REQUEST['q']!=""){
    $value = htmlspecialchars(stripslashes(trim($_REQUEST['q'])));
}?>
<form class="container header__container" action="<?=$arResult["FORM_ACTION"]?>">
    <button type="button" class="header__burger-menu-btn header__mobile-btn">
        <span></span>
    </button>
    <div class="header__mobile-search-icon header__mobile-btn search-mobile">
        <svg viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18.0875 17.34L13.7998 13.0532C14.95 11.7312 15.6463 10.0063 15.6463 8.12106C15.6463 3.97116 12.2701 0.599609 8.12321 0.599609C3.97239 0.599609 0.600098 3.97507 0.600098 8.12106C0.600098 12.267 3.97631 15.6425 8.12321 15.6425C10.0089 15.6425 11.7341 14.9463 13.0565 13.7964L17.3442 18.0832C17.4459 18.1849 17.5829 18.2396 17.7159 18.2396C17.8489 18.2396 17.9858 18.1888 18.0875 18.0832C18.291 17.8798 18.291 17.5434 18.0875 17.34ZM1.65247 8.12106C1.65247 4.55394 4.55531 1.65566 8.1193 1.65566C11.6872 1.65566 14.5861 4.55785 14.5861 8.12106C14.5861 11.6843 11.6872 14.5904 8.1193 14.5904C4.55531 14.5904 1.65247 11.6882 1.65247 8.12106Z" stroke-width="0.6"/>
        </svg>
    </div>
    <a href="/" class="header__logo">
        <img src="/bitrix/templates/razvitie/img/logo_new.svg" alt="">
    </a>
    <a href="#" class="header__mobile-personal header__mobile-btn">
        <svg viewBox="0 0 17 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0.76001 18.9016C0.76001 16.2204 2.60282 13.9366 5.10725 13.5141L5.33284 13.476C7.33826 13.1376 9.38175 13.1376 11.3872 13.476L11.6128 13.5141C14.1172 13.9366 15.96 16.2204 15.96 18.9016C15.96 20.0605 15.0714 21 13.9752 21H2.74483C1.64865 21 0.76001 20.0605 0.76001 18.9016Z" stroke-width="1.5"/><path d="M12.7934 5.375C12.7934 7.79125 10.8085 9.75 8.36003 9.75C5.91157 9.75 3.9267 7.79125 3.9267 5.375C3.9267 2.95875 5.91157 1 8.36003 1C10.8085 1 12.7934 2.95875 12.7934 5.375Z" stroke-width="1.5"/>
        </svg>
    </a>
    <?$APPLICATION->IncludeComponent(
        "bitrix:sale.basket.basket.line",
        "cart-mobile",
        Array(
            "HIDE_ON_BASKET_PAGES" => "N",
            "PATH_TO_AUTHORIZE" => "",
            "PATH_TO_BASKET" => SITE_DIR."korzina/",
            "PATH_TO_ORDER" => SITE_DIR."korzina/oformlenie-zakaza/",
            "POSITION_FIXED" => "N",
            "SHOW_AUTHOR" => "N",
            "SHOW_EMPTY_VALUES" => "Y",
            "SHOW_NUM_PRODUCTS" => "Y",
            "SHOW_PERSONAL_LINK" => "N",
            "SHOW_PRODUCTS" => "N",
            "SHOW_REGISTRATION" => "N",
            "SHOW_TOTAL_PRICE" => "N"
        )
    );?>
    <label class="header__search-label-mob">
        <input type="text" name="q" value="<?=$value?>" placeholder="Поиск..." class="header__search-input-mob">
    </label>
</form>