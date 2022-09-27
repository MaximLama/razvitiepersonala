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
<a href="tel:<?=preg_replace("/[\D]*/", "", $arResult["PROPERTIES"]["NOMER_TELEFONA"]["VALUE"])?>" class="contacts__data-item">
	<svg viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M32 26.7432C32 27.3192 31.8718 27.9112 31.5994 28.4872C31.327 29.0632 30.9745 29.6072 30.5098 30.1192C29.7246 30.9832 28.8593 31.6072 27.8818 32.0072C26.9204 32.4072 25.8788 32.6152 24.7571 32.6152C23.1227 32.6152 21.3761 32.2312 19.5333 31.4472C17.6905 30.6632 15.8478 29.6072 14.021 28.2792C12.1783 26.9352 10.4316 25.4472 8.76515 23.7992C7.11467 22.1352 5.62444 20.3912 4.29444 18.5672C2.98047 16.7432 1.92288 14.9192 1.15373 13.1112C0.384577 11.2872 0 9.54323 0 7.87923C0 6.79123 0.192288 5.75123 0.576865 4.79123C0.961442 3.81523 1.57036 2.91923 2.41963 2.11923C3.44517 1.11123 4.56685 0.615234 5.75263 0.615234C6.2013 0.615234 6.64998 0.711234 7.05058 0.903234C7.4672 1.09523 7.83575 1.38323 8.12419 1.79923L11.8418 7.03123C12.1302 7.43123 12.3385 7.79923 12.4827 8.15123C12.6269 8.48723 12.7071 8.82323 12.7071 9.12723C12.7071 9.51124 12.5949 9.89523 12.3706 10.2632C12.1622 10.6312 11.8578 11.0152 11.4732 11.3992L10.2554 12.6632C10.0791 12.8392 9.999 13.0472 9.999 13.3032C9.999 13.4312 10.015 13.5432 10.0471 13.6712C10.0951 13.7992 10.1432 13.8952 10.1753 13.9912C10.4637 14.5192 10.9604 15.2072 11.6655 16.0392C12.3866 16.8712 13.1557 17.7192 13.989 18.5672C14.8543 19.4152 15.6875 20.1992 16.5368 20.9192C17.3701 21.6232 18.0591 22.1032 18.6039 22.3912C18.684 22.4232 18.7802 22.4712 18.8923 22.5192C19.0205 22.5672 19.1487 22.5832 19.2929 22.5832C19.5653 22.5832 19.7737 22.4872 19.9499 22.3112L21.1678 21.1112C21.5684 20.7112 21.9529 20.4072 22.3215 20.2152C22.69 19.9912 23.0586 19.8792 23.4592 19.8792C23.7636 19.8792 24.0841 19.9432 24.4367 20.0872C24.7892 20.2312 25.1577 20.4392 25.5583 20.7112L30.8623 24.4712C31.2789 24.7592 31.5674 25.0952 31.7436 25.4952C31.9039 25.8952 32 26.2952 32 26.7432Z" />
	</svg>
	<div class="contacts__data-value"><?=$arResult["PROPERTIES"]["NOMER_TELEFONA"]["VALUE"]?></div>
</a>
<a href="mailto:<?=$arResult["PROPERTIES"]["POCHTA"]["VALUE"]?>" class="contacts__data-item">
	<svg viewBox="0 0 32 28" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M23.9165 0.156738H8.08317C3.33317 0.156738 0.166504 2.53174 0.166504 8.07341V19.1567C0.166504 24.6984 3.33317 27.0734 8.08317 27.0734H23.9165C28.6665 27.0734 31.8332 24.6984 31.8332 19.1567V8.07341C31.8332 2.53174 28.6665 0.156738 23.9165 0.156738ZM24.6607 9.79924L19.7048 13.7576C18.6598 14.5967 17.3298 15.0084 15.9998 15.0084C14.6698 15.0084 13.324 14.5967 12.2948 13.7576L7.339 9.79924C6.83234 9.38757 6.75317 8.62757 7.149 8.12091C7.56067 7.61424 8.30484 7.51924 8.8115 7.93091L13.7673 11.8892C14.9707 12.8551 17.0132 12.8551 18.2165 11.8892L23.1723 7.93091C23.679 7.51924 24.439 7.59841 24.8348 8.12091C25.2465 8.62757 25.1673 9.38757 24.6607 9.79924Z" />
	</svg>
	<div class="contacts__data-value"><?=$arResult["PROPERTIES"]["POCHTA"]["VALUE"]?></div>
</a>
<div class="contacts__data-item">
	<svg viewBox="0 0 30 29" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M15.0002 0.448242C7.19433 0.448242 0.833496 6.80908 0.833496 14.6149C0.833496 22.4207 7.19433 28.7816 15.0002 28.7816C22.806 28.7816 29.1668 22.4207 29.1668 14.6149C29.1668 6.80908 22.806 0.448242 15.0002 0.448242ZM21.1627 19.6724C20.9643 20.0124 20.6102 20.1966 20.2418 20.1966C20.0577 20.1966 19.8735 20.1541 19.7035 20.0407L15.3118 17.4199C14.221 16.7682 13.4135 15.3374 13.4135 14.0766V8.26824C13.4135 7.68741 13.8952 7.20574 14.476 7.20574C15.0568 7.20574 15.5385 7.68741 15.5385 8.26824V14.0766C15.5385 14.5866 15.9635 15.3374 16.4027 15.5924L20.7943 18.2132C21.3043 18.5107 21.4743 19.1624 21.1627 19.6724Z" />
	</svg>
	<div class="contacts__data-value"><?=$arResult["PROPERTIES"]["GRAFIK"]["VALUE"]?></div>
</div>