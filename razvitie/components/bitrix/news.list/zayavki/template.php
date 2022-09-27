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
<div class="train-req__title">Заявки на обучение</div>
<div class="train-req__cards">
	<?foreach($arResult["ITEMS"] as $arItem):?>
	    <div class="train-req__item">
	        <div class="train-req__img-box">
	            <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>">
	        </div>
	        <div class="train-req__text">
	            <?=$arItem["NAME"]?>
	        </div>
	        <a href="<?=CFile::GetPath($arItem["PROPERTIES"]["DOKUMENT"]["VALUE"])?>" class="train-req__btn">
	            <svg viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg">
	                <path d="M8.5037 8.49969L2.20797 14.7933C1.93171 15.0689 1.48414 15.0689 1.20719 14.7933C0.930937 14.5178 0.930937 14.0702 1.20719 13.7947L7.00356 8.00038L1.20789 2.20611C0.931634 1.93055 0.931634 1.48298 1.20789 1.20673C1.48414 0.931171 1.93241 0.931171 2.20866 1.20673L8.5044 7.50037C8.77647 7.77308 8.77647 8.22768 8.5037 8.49969Z" stroke-width="0.5"/>
	            </svg>
	        </a>
	    </div>
	<?endforeach;?>
</div>