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

<form class="header__search-form" action="<?=$arResult["FORM_ACTION"]?>">
    <input type="text" name="q" value="<?=$value?>" class="header__search-input" placeholder="Поиск по сайту...">
    <button type="reset" class="header__search-del desktop">
        <span></span>
    </button>
    <button type="submit" class="header__search-icon">
        <svg viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M21.8184 20.929L16.7139 15.8257C18.0832 14.2519 18.9122 12.1984 18.9122 9.9541C18.9122 5.01375 14.8929 1 9.95609 1C5.01464 1 1 5.0184 1 9.9541C1 14.8898 5.0193 18.9082 9.95609 18.9082C12.2009 18.9082 14.2548 18.0794 15.829 16.7104L20.9335 21.8137C21.0546 21.9348 21.2176 22 21.3759 22C21.5343 22 21.6973 21.9395 21.8184 21.8137C22.0605 21.5716 22.0605 21.1712 21.8184 20.929ZM2.25283 9.9541C2.25283 5.70754 5.70858 2.25721 9.95143 2.25721C14.1989 2.25721 17.65 5.7122 17.65 9.9541C17.65 14.196 14.1989 17.6557 9.95143 17.6557C5.70858 17.6557 2.25283 14.2007 2.25283 9.9541Z" stroke-width="0.6"/>
        </svg>
    </button>
</form>