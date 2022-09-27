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
<div class="more-center__title"><?=$arResult["~NAME"]?></div>
<div class="more-center__content">
	<div class="more-center__img-box">
		<img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arResult["PREVIEW_PICTURE"]["ALT"]?>">
	</div>
	<article class="more-center__text-block">
		<p class="more-center__text-bold">
			<?=$arResult["PREVIEW_TEXT"]?>
		</p>
		<div class="more-center__citation-block">
			<div class="more-center__citation-icon">
				<svg viewBox="0 0 15 12" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M3.1267 0.540691C4.37603 0.540691 5.32103 1.0257 5.96171 1.99572C6.50629 2.81417 6.77858 3.8145 6.77858 4.99671C6.77858 6.51236 6.37816 7.87645 5.57731 9.08897C4.74442 10.3015 3.49509 11.2715 1.82932 11.999L1.39686 11.1806C2.38992 10.7865 3.25484 10.1651 3.99162 9.31632C4.69637 8.46755 5.04874 7.60363 5.04874 6.72455C5.04874 6.3608 5.00069 6.04251 4.90459 5.76969C4.39205 6.16376 3.79941 6.3608 3.1267 6.3608C2.26178 6.3608 1.54101 6.08798 0.964402 5.54234C0.355755 5.02702 0.0514317 4.32982 0.0514317 3.45074C0.0514317 2.63229 0.355755 1.93509 0.964402 1.35914C1.54101 0.813508 2.26178 0.540691 3.1267 0.540691ZM11.2954 0.540691C12.5447 0.540691 13.4897 1.0257 14.1304 1.99572C14.675 2.81417 14.9473 3.8145 14.9473 4.99671C14.9473 6.51236 14.5468 7.87645 13.746 9.08897C12.9131 10.3015 11.6638 11.2715 9.998 11.999L9.56554 11.1806C10.5586 10.7865 11.4235 10.1651 12.1603 9.31632C12.8651 8.46755 13.2174 7.60363 13.2174 6.72455C13.2174 6.3608 13.1694 6.04251 13.0733 5.76969C12.5607 6.16376 11.9681 6.3608 11.2954 6.3608C10.4305 6.3608 9.7097 6.08798 9.13308 5.54234C8.52444 5.02702 8.22011 4.32982 8.22011 3.45074C8.22011 2.63229 8.52444 1.93509 9.13308 1.35914C9.7097 0.813508 10.4305 0.540691 11.2954 0.540691Z" />
				</svg>
			</div>
			<p class="more-center__citation-text">
				<?=$arResult["DETAIL_TEXT"]?>
			</p>
		</div>
	</article>
</div>
<a href="/o-nas/" class="more-center__btn">
	<span><?=$arResult["PROPERTIES"]["PODROBNEE"]["VALUE"]?></span>
	<svg viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M24.5 8.26953H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1.76953C18.5 1.76953 19 4.13317 20.5 5.76953C22.1874 7.61028 25 8.26953 25 8.26953" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14.7695C18.5 14.7695 19 12.4059 20.5 10.7695C22.1874 8.92878 25 8.26953 25 8.26953" stroke-width="2" stroke-linecap="round"/>
	</svg>
</a>