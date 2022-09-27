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
<?foreach($arResult["ITEMS"] as $key=>$arItem):?>
	<div class="documents__item documents__item--big mobile">
	    <div class="documents__image-block">
	        <div class="documents__img-bg">
	            <img src="<?=SITE_TEMPLATE_PATH?>/img/review-page-img-bg.svg" alt="">
	        </div>
	        <div class="documents__img-box">
	            <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>">
	        </div>
	    </div>
	    <div class="documents__text-block">
	        <div class="documents__item-title">
	            <?=$arItem["NAME"]?>
	        </div>
	        <p class="documents__item-text">
	            <?=$arItem["~PREVIEW_TEXT"]?>
	        </p>
	    </div>
	    <a href="<?=CFile::GetPath($arItem["PROPERTIES"]["DOCUMENT"]["VALUE"])?>" target="_blank" class="documents__item-btn">
	        <span>смотреть</span>
	        <svg viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg">
	            <path d="M8.65019 8.49969L2.35445 14.7933C2.0782 15.0689 1.63062 15.0689 1.35367 14.7933C1.07742 14.5178 1.07742 14.0702 1.35367 13.7947L7.15004 8.00038L1.35437 2.20611C1.07812 1.93055 1.07812 1.48298 1.35437 1.20673C1.63062 0.931171 2.0789 0.931171 2.35515 1.20673L8.65089 7.50037C8.92295 7.77308 8.92295 8.22768 8.65019 8.49969Z" stroke-width="0.5"/>
	        </svg>
	    </a>
	</div>
	
	<?if($key):?>
		<div class="documents__item desktop">
		    <div class="documents__image-block">
		        <div class="documents__img-bg">
		            <img src="<?=SITE_TEMPLATE_PATH?>/img/review-page-img-bg.svg" alt="">
		        </div>
		        <div class="documents__img-box">
		            <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>">
		        </div>
		    </div>
		    <a href="<?=CFile::GetPath($arItem["PROPERTIES"]["DOCUMENT"]["VALUE"])?>" target="_blank" class="documents__item-btn">
			    <span>смотреть</span>
		        <svg viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg">
		            <path d="M8.65019 8.49969L2.35445 14.7933C2.0782 15.0689 1.63062 15.0689 1.35367 14.7933C1.07742 14.5178 1.07742 14.0702 1.35367 13.7947L7.15004 8.00038L1.35437 2.20611C1.07812 1.93055 1.07812 1.48298 1.35437 1.20673C1.63062 0.931171 2.0789 0.931171 2.35515 1.20673L8.65089 7.50037C8.92295 7.77308 8.92295 8.22768 8.65019 8.49969Z" stroke-width="0.5"/>
		        </svg>
		    </a>
		</div>
	<?else:?>
		<div class="documents__item documents__item--big desktop">
		    <div class="documents__image-block">
		        <div class="documents__img-bg">
		            <img src="<?=SITE_TEMPLATE_PATH?>/img/review-page-img-bg.svg" alt="">
		        </div>
		        <div class="documents__img-box">
		            <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>">
		        </div>
		    </div>
		    <div class="documents__text-block">
		        <div class="documents__item-title">
		            <?=$arItem["NAME"]?>
		        </div>
		        <p class="documents__item-text">
		            <?=$arItem["~PREVIEW_TEXT"]?>
		        </p>
		    </div>
		    <a href="<?=CFile::GetPath($arItem["PROPERTIES"]["DOCUMENT"]["VALUE"])?>" target="_blank" class="documents__item-btn">
		        <span>смотреть</span>
		        <svg viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg">
		            <path d="M8.65019 8.49969L2.35445 14.7933C2.0782 15.0689 1.63062 15.0689 1.35367 14.7933C1.07742 14.5178 1.07742 14.0702 1.35367 13.7947L7.15004 8.00038L1.35437 2.20611C1.07812 1.93055 1.07812 1.48298 1.35437 1.20673C1.63062 0.931171 2.0789 0.931171 2.35515 1.20673L8.65089 7.50037C8.92295 7.77308 8.92295 8.22768 8.65019 8.49969Z" stroke-width="0.5"/>
		        </svg>
		    </a>
		</div>
	<?endif;?>

<?endforeach;?>