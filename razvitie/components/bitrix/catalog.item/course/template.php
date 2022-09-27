<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogProductsViewedComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(true);
if (isset($arResult['ITEM']))
{
	$item = $arResult['ITEM'];
	$obName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $areaId);

	$productTitle = isset($item["PROPERTIES"]["NAZVANIE_KARTOCHKI"]["~VALUE"]['TEXT']) && $item["PROPERTIES"]["NAZVANIE_KARTOCHKI"]["~VALUE"]['TEXT'] !== ''
		? $item["PROPERTIES"]["NAZVANIE_KARTOCHKI"]["~VALUE"]['TEXT']
		: $item['NAME'];

	$price = $item['ITEM_PRICES'][$item['ITEM_PRICE_SELECTED']];
	?>

	<div class="course-page__item">
		
		<div class="course-page__img-box">
		    <img src="<?=$item['PREVIEW_PICTURE']['SRC']?>" alt="">
		</div>
		<a href="<?=$item["DETAIL_PAGE_URL"]?>" class="course-page__text-block">
			<div class="course-page__item-title"><?=$productTitle?></div>
			<div class="course-page__item-price"><?=$price['PRINT_RATIO_PRICE']?></div>
		</a>

		
	    <a href="javascript:void(0)" class="course-page__item-btn buy" id=<?=$item["ID"]?>>
	        <svg viewBox="0 0 26 27" fill="none" xmlns="http://www.w3.org/2000/svg">
	            <path d="M4.3085 10.8146C4.35208 10.2717 4.59857 9.76503 4.99888 9.39564C5.3992 9.02624 5.92396 8.82118 6.46867 8.82129H19.5315C20.0762 8.82118 20.601 9.02624 21.0013 9.39564C21.4016 9.76503 21.6481 10.2717 21.6917 10.8146L22.5616 21.648C22.5855 21.9461 22.5475 22.2459 22.4498 22.5286C22.3522 22.8113 22.1971 23.0707 21.9942 23.2906C21.7914 23.5104 21.5453 23.6858 21.2714 23.8058C20.9974 23.9259 20.7016 23.9879 20.4025 23.988H5.59767C5.29858 23.9879 5.00276 23.9259 4.72881 23.8058C4.45487 23.6858 4.20874 23.5104 4.00592 23.2906C3.80311 23.0707 3.64799 22.8113 3.55035 22.5286C3.45271 22.2459 3.41466 21.9461 3.43859 21.648L4.3085 10.8146V10.8146Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M17.3337 12.0713V6.65462C17.3337 5.50535 16.8771 4.40315 16.0645 3.59049C15.2518 2.77784 14.1496 2.32129 13.0003 2.32129C11.8511 2.32129 10.7489 2.77784 9.9362 3.59049C9.12354 4.40315 8.66699 5.50535 8.66699 6.65462V12.0713" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
	        </svg>
	    </a>
	</div>
	<?
}