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
<div class="reviews__item swiper-slide">
	<div class="reviews__item-bg">
		<img src="<?=SITE_TEMPLATE_PATH?>/img/reviews-item-bg.svg" alt="">
	</div>
	<button type="button" class="reviews__item-img-box review-img-slider">
		<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>">
		<img class="reviews__review-doc review-doc" src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arItem["DETAIL_PICTURE"]["ALT"]?>">
	</button>
	<div class="reviews__item-title"><?=$arItem["~NAME"]?></div>
	<p class="reviews__item-text">
		<?=$arItem["~PREVIEW_TEXT"]?>
	</p>
	<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="reviews__item-link">
		<span>Читать</span>
		<svg viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M8.65019 8.65399L2.35445 14.9476C2.0782 15.2232 1.63062 15.2232 1.35367 14.9476C1.07742 14.6721 1.07742 14.2245 1.35367 13.9489L7.15004 8.15468L1.35437 2.3604C1.07812 2.08485 1.07812 1.63728 1.35437 1.36102C1.63062 1.08547 2.0789 1.08547 2.35515 1.36102L8.65089 7.65467C8.92295 7.92737 8.92295 8.38198 8.65019 8.65399Z" stroke-width="0.5"/>
		</svg>
	</a>
</div>
<?endforeach;?>