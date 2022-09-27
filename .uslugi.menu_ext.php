<?
// пример файла .left.menu_ext.php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

$aMenuLinksExt = $APPLICATION->IncludeComponent(
    "bitrix:menu.sections",
    "",
    Array(
        "CACHE_TIME" => "36000000",
        "CACHE_TYPE" => "A",
        "DEPTH_LEVEL" => "2",
        "DETAIL_PAGE_URL" => "",
        "IBLOCK_ID" => "26",
        "IBLOCK_TYPE" => "osnovnye_dannye",
        "ID" => "",
        "IS_SEF" => "Y",
        "SECTION_PAGE_URL" => "#SECTION_CODE#/",
        "SECTION_URL" => "",
        "SEF_BASE_URL" => "/obuchenie-i-attestatsiya/"
    )
);

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>