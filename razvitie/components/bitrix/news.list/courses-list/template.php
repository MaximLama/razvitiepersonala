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
<?foreach($arResult["ITEMS"] as $arItem):?>
	<li class="services__card-item">
        <a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
            <span>
                <?=$arItem["~NAME"]?> <span class="services__card-item--gray">(<?=countHours((int)$arItem["PROPERTIES"]["KOLVO_CHASOV"]["VALUE"])?>)</span>
            </span>
            <span><?=$arItem["PROPERTIES"]["STOIMOST"]["VALUE"]?> ₽</span>
        </a>
    </li>
<?endforeach;?>