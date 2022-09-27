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
<div class="footer__text"><?=$arResult["PROPERTIES"]["KOPIRAJT"]["VALUE"]?></div>
<a href="<?=SITE_TEMPLATE_PATH?>/docs/politika-konfidencialnosti.docx" class="footer__link footer__link--underline"><?=$arResult["PROPERTIES"]["POLITIKA_KONFIDENCIALNOSTI"]["VALUE"]?></a>