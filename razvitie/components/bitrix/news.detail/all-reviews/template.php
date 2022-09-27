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
require_once($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/includes/reviews.php");
?>
<div class="all-reviews__title-content">
    <div class="all-reviews__text-block">
        <div class="all-reviews__sub-title"><?=$arResult["PREVIEW_TEXT"]?></div>
        <?if($APPLICATION->GetCurDir()==='/otzyvy/'):?>
        	<h1 class="all-reviews__title"><?=$arResult["NAME"]?></h1>
        <?else:?>
        	<div class="all-reviews__title"><?=$arResult["NAME"]?></div>
        <?endif;?>
        <div class="all-reviews__text">
            <p>
                <?=$arResult["~DETAIL_TEXT"]?>
            </p>
        </div>
    </div>
    <div class="all-reviews__rating-block">
        <div class="all-reviews__rating-box">
            <div class="all-reviews__rating-title"><?=$arResult["PROPERTIES"]["VSEGO_OTZYVOV_ZAGOLOVOK"]["VALUE"]?></div>
            <div class="all-reviews__total-feedback"><?=$GLOBALS['reviews']["reviews"]?></div>
        </div>
        <div class="all-reviews__rating-box">
            <div class="all-reviews__rating-title"><?=$arResult["PROPERTIES"]["OCENKA_ZAGOLOVOK"]["VALUE"]?></div>
            <div class="all-reviews__average-rating average-rating" rating="<?=$GLOBALS['reviews']["rating"]?>">
                <div class="all-reviews__rating-number rating-number"></div>
                <div class="all-reviews__rating-icons-block">
                    <div class="all-reviews__rating-icons-empty">
                        <svg viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.7289 1.59848L13.4889 5.14717C13.7289 5.64117 14.3689 6.115 14.9089 6.20573L18.0989 6.74005C20.1389 7.08282 20.6189 8.57489 19.1489 10.0468L16.6689 12.547C16.2489 12.9704 16.0189 13.787 16.1489 14.3718L16.8589 17.4668C17.4189 19.9166 16.1289 20.8643 13.9789 19.5839L10.9889 17.7995C10.4489 17.4769 9.55893 17.4769 9.00893 17.7995L6.01893 19.5839C3.87893 20.8643 2.57893 19.9065 3.13893 17.4668L3.84893 14.3718C3.97893 13.787 3.74893 12.9704 3.32893 12.547L0.848932 10.0468C-0.611068 8.57489 -0.141067 7.08282 1.89893 6.74005L5.08893 6.20573C5.61893 6.115 6.25893 5.64117 6.49893 5.14717L8.25893 1.59848C9.21893 -0.327096 10.7789 -0.327096 11.7289 1.59848Z" />
                        </svg>
                        <svg viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.7289 1.59848L13.4889 5.14717C13.7289 5.64117 14.3689 6.115 14.9089 6.20573L18.0989 6.74005C20.1389 7.08282 20.6189 8.57489 19.1489 10.0468L16.6689 12.547C16.2489 12.9704 16.0189 13.787 16.1489 14.3718L16.8589 17.4668C17.4189 19.9166 16.1289 20.8643 13.9789 19.5839L10.9889 17.7995C10.4489 17.4769 9.55893 17.4769 9.00893 17.7995L6.01893 19.5839C3.87893 20.8643 2.57893 19.9065 3.13893 17.4668L3.84893 14.3718C3.97893 13.787 3.74893 12.9704 3.32893 12.547L0.848932 10.0468C-0.611068 8.57489 -0.141067 7.08282 1.89893 6.74005L5.08893 6.20573C5.61893 6.115 6.25893 5.64117 6.49893 5.14717L8.25893 1.59848C9.21893 -0.327096 10.7789 -0.327096 11.7289 1.59848Z" />
                        </svg>
                        <svg viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.7289 1.59848L13.4889 5.14717C13.7289 5.64117 14.3689 6.115 14.9089 6.20573L18.0989 6.74005C20.1389 7.08282 20.6189 8.57489 19.1489 10.0468L16.6689 12.547C16.2489 12.9704 16.0189 13.787 16.1489 14.3718L16.8589 17.4668C17.4189 19.9166 16.1289 20.8643 13.9789 19.5839L10.9889 17.7995C10.4489 17.4769 9.55893 17.4769 9.00893 17.7995L6.01893 19.5839C3.87893 20.8643 2.57893 19.9065 3.13893 17.4668L3.84893 14.3718C3.97893 13.787 3.74893 12.9704 3.32893 12.547L0.848932 10.0468C-0.611068 8.57489 -0.141067 7.08282 1.89893 6.74005L5.08893 6.20573C5.61893 6.115 6.25893 5.64117 6.49893 5.14717L8.25893 1.59848C9.21893 -0.327096 10.7789 -0.327096 11.7289 1.59848Z" />
                        </svg>
                        <svg viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.7289 1.59848L13.4889 5.14717C13.7289 5.64117 14.3689 6.115 14.9089 6.20573L18.0989 6.74005C20.1389 7.08282 20.6189 8.57489 19.1489 10.0468L16.6689 12.547C16.2489 12.9704 16.0189 13.787 16.1489 14.3718L16.8589 17.4668C17.4189 19.9166 16.1289 20.8643 13.9789 19.5839L10.9889 17.7995C10.4489 17.4769 9.55893 17.4769 9.00893 17.7995L6.01893 19.5839C3.87893 20.8643 2.57893 19.9065 3.13893 17.4668L3.84893 14.3718C3.97893 13.787 3.74893 12.9704 3.32893 12.547L0.848932 10.0468C-0.611068 8.57489 -0.141067 7.08282 1.89893 6.74005L5.08893 6.20573C5.61893 6.115 6.25893 5.64117 6.49893 5.14717L8.25893 1.59848C9.21893 -0.327096 10.7789 -0.327096 11.7289 1.59848Z" />
                        </svg>
                        <svg viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.7289 1.59848L13.4889 5.14717C13.7289 5.64117 14.3689 6.115 14.9089 6.20573L18.0989 6.74005C20.1389 7.08282 20.6189 8.57489 19.1489 10.0468L16.6689 12.547C16.2489 12.9704 16.0189 13.787 16.1489 14.3718L16.8589 17.4668C17.4189 19.9166 16.1289 20.8643 13.9789 19.5839L10.9889 17.7995C10.4489 17.4769 9.55893 17.4769 9.00893 17.7995L6.01893 19.5839C3.87893 20.8643 2.57893 19.9065 3.13893 17.4668L3.84893 14.3718C3.97893 13.787 3.74893 12.9704 3.32893 12.547L0.848932 10.0468C-0.611068 8.57489 -0.141067 7.08282 1.89893 6.74005L5.08893 6.20573C5.61893 6.115 6.25893 5.64117 6.49893 5.14717L8.25893 1.59848C9.21893 -0.327096 10.7789 -0.327096 11.7289 1.59848Z" />
                        </svg>
                    </div>
                    <div class="all-reviews__rating-icons-full rating-icons-full">
                        <svg viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.7289 1.59848L13.4889 5.14717C13.7289 5.64117 14.3689 6.115 14.9089 6.20573L18.0989 6.74005C20.1389 7.08282 20.6189 8.57489 19.1489 10.0468L16.6689 12.547C16.2489 12.9704 16.0189 13.787 16.1489 14.3718L16.8589 17.4668C17.4189 19.9166 16.1289 20.8643 13.9789 19.5839L10.9889 17.7995C10.4489 17.4769 9.55893 17.4769 9.00893 17.7995L6.01893 19.5839C3.87893 20.8643 2.57893 19.9065 3.13893 17.4668L3.84893 14.3718C3.97893 13.787 3.74893 12.9704 3.32893 12.547L0.848932 10.0468C-0.611068 8.57489 -0.141067 7.08282 1.89893 6.74005L5.08893 6.20573C5.61893 6.115 6.25893 5.64117 6.49893 5.14717L8.25893 1.59848C9.21893 -0.327096 10.7789 -0.327096 11.7289 1.59848Z" />
                        </svg>
                        <svg viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.7289 1.59848L13.4889 5.14717C13.7289 5.64117 14.3689 6.115 14.9089 6.20573L18.0989 6.74005C20.1389 7.08282 20.6189 8.57489 19.1489 10.0468L16.6689 12.547C16.2489 12.9704 16.0189 13.787 16.1489 14.3718L16.8589 17.4668C17.4189 19.9166 16.1289 20.8643 13.9789 19.5839L10.9889 17.7995C10.4489 17.4769 9.55893 17.4769 9.00893 17.7995L6.01893 19.5839C3.87893 20.8643 2.57893 19.9065 3.13893 17.4668L3.84893 14.3718C3.97893 13.787 3.74893 12.9704 3.32893 12.547L0.848932 10.0468C-0.611068 8.57489 -0.141067 7.08282 1.89893 6.74005L5.08893 6.20573C5.61893 6.115 6.25893 5.64117 6.49893 5.14717L8.25893 1.59848C9.21893 -0.327096 10.7789 -0.327096 11.7289 1.59848Z" />
                        </svg>
                        <svg viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.7289 1.59848L13.4889 5.14717C13.7289 5.64117 14.3689 6.115 14.9089 6.20573L18.0989 6.74005C20.1389 7.08282 20.6189 8.57489 19.1489 10.0468L16.6689 12.547C16.2489 12.9704 16.0189 13.787 16.1489 14.3718L16.8589 17.4668C17.4189 19.9166 16.1289 20.8643 13.9789 19.5839L10.9889 17.7995C10.4489 17.4769 9.55893 17.4769 9.00893 17.7995L6.01893 19.5839C3.87893 20.8643 2.57893 19.9065 3.13893 17.4668L3.84893 14.3718C3.97893 13.787 3.74893 12.9704 3.32893 12.547L0.848932 10.0468C-0.611068 8.57489 -0.141067 7.08282 1.89893 6.74005L5.08893 6.20573C5.61893 6.115 6.25893 5.64117 6.49893 5.14717L8.25893 1.59848C9.21893 -0.327096 10.7789 -0.327096 11.7289 1.59848Z" />
                        </svg>
                        <svg viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.7289 1.59848L13.4889 5.14717C13.7289 5.64117 14.3689 6.115 14.9089 6.20573L18.0989 6.74005C20.1389 7.08282 20.6189 8.57489 19.1489 10.0468L16.6689 12.547C16.2489 12.9704 16.0189 13.787 16.1489 14.3718L16.8589 17.4668C17.4189 19.9166 16.1289 20.8643 13.9789 19.5839L10.9889 17.7995C10.4489 17.4769 9.55893 17.4769 9.00893 17.7995L6.01893 19.5839C3.87893 20.8643 2.57893 19.9065 3.13893 17.4668L3.84893 14.3718C3.97893 13.787 3.74893 12.9704 3.32893 12.547L0.848932 10.0468C-0.611068 8.57489 -0.141067 7.08282 1.89893 6.74005L5.08893 6.20573C5.61893 6.115 6.25893 5.64117 6.49893 5.14717L8.25893 1.59848C9.21893 -0.327096 10.7789 -0.327096 11.7289 1.59848Z" />
                        </svg>
                        <svg viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.7289 1.59848L13.4889 5.14717C13.7289 5.64117 14.3689 6.115 14.9089 6.20573L18.0989 6.74005C20.1389 7.08282 20.6189 8.57489 19.1489 10.0468L16.6689 12.547C16.2489 12.9704 16.0189 13.787 16.1489 14.3718L16.8589 17.4668C17.4189 19.9166 16.1289 20.8643 13.9789 19.5839L10.9889 17.7995C10.4489 17.4769 9.55893 17.4769 9.00893 17.7995L6.01893 19.5839C3.87893 20.8643 2.57893 19.9065 3.13893 17.4668L3.84893 14.3718C3.97893 13.787 3.74893 12.9704 3.32893 12.547L0.848932 10.0468C-0.611068 8.57489 -0.141067 7.08282 1.89893 6.74005L5.08893 6.20573C5.61893 6.115 6.25893 5.64117 6.49893 5.14717L8.25893 1.59848C9.21893 -0.327096 10.7789 -0.327096 11.7289 1.59848Z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="all-reviews__cards">
	<div class="all-reviews__wrapper">
		<?$APPLICATION->IncludeComponent(
			"bitrix:news.list",
			"all-reviews",
			Array(
				"ACTIVE_DATE_FORMAT" => "d.m.Y",
				"ADD_SECTIONS_CHAIN" => "N",
				"AJAX_MODE" => "N",
				"AJAX_OPTION_ADDITIONAL" => "",
				"AJAX_OPTION_HISTORY" => "N",
				"AJAX_OPTION_JUMP" => "N",
				"AJAX_OPTION_STYLE" => "Y",
				"CACHE_FILTER" => "N",
				"CACHE_GROUPS" => "Y",
				"CACHE_TIME" => "36000000",
				"CACHE_TYPE" => "A",
				"CHECK_DATES" => "Y",
				"DETAIL_URL" => "",
				"DISPLAY_BOTTOM_PAGER" => "Y",
				"DISPLAY_DATE" => "N",
				"DISPLAY_NAME" => "Y",
				"DISPLAY_PICTURE" => "Y",
				"DISPLAY_PREVIEW_TEXT" => "Y",
				"DISPLAY_TOP_PAGER" => "N",
				"FIELD_CODE" => array("NAME", "PREVIEW_TEXT", "PREVIEW_PICTURE", "DETAIL_PICTURE", ""),
				"FILE_404" => "",
				"FILTER_NAME" => "",
				"HIDE_LINK_WHEN_NO_DETAIL" => "N",
				"IBLOCK_ID" => "18",
				"IBLOCK_TYPE" => "osnovnye_dannye",
				"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
				"INCLUDE_SUBSECTIONS" => "N",
				"MESSAGE_404" => "",
				"NEWS_COUNT" => "20",
				"PAGER_BASE_LINK_ENABLE" => "N",
				"PAGER_DESC_NUMBERING" => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
				"PAGER_SHOW_ALL" => "N",
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_TEMPLATE" => ".default",
				"PAGER_TITLE" => "Новости",
				"PARENT_SECTION" => "",
				"PARENT_SECTION_CODE" => "",
				"PREVIEW_TRUNCATE_LEN" => "",
				"PROPERTY_CODE" => array("ZAGOLOVOK", ""),
				"SET_BROWSER_TITLE" => "N",
				"SET_LAST_MODIFIED" => "N",
				"SET_META_DESCRIPTION" => "N",
				"SET_META_KEYWORDS" => "N",
				"SET_STATUS_404" => "Y",
				"SET_TITLE" => "N",
				"SHOW_404" => "Y",
				"SORT_BY1" => "SORT",
				"SORT_BY2" => "ID",
				"SORT_ORDER1" => "ASC",
				"SORT_ORDER2" => "ASC",
				"STRICT_SECTION_CHECK" => "N"
			)
		);?>
    </div>
</div>
<div class="all-reviews__navigation-block navigation-block mobile">
    <div class="navigation-block__pagination-block">
        <div class="all-reviews__current-slide navigation-block__current-slide"></div>
        <div class="all-reviews__pagination navigation-block__pagination"></div>
        <div class="all-reviews__total-slide navigation-block__total-slide"></div>
    </div>
    <div class="navigation-block__arrows">
        <svg class="all-reviews__arrow-left navigation-block__arrow" viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1.5 7.88477H25" stroke-width="2" stroke-linecap="round"/><path d="M7.5 1.38477C7.5 1.38477 7 3.7484 5.5 5.38477C3.81265 7.22551 1 7.88477 1 7.88477" stroke-width="2" stroke-linecap="round"/><path d="M7.5 14.3848C7.5 14.3848 7 12.0211 5.5 10.3848C3.81265 8.54402 1 7.88477 1 7.88477" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <svg class="all-reviews__arrow-right navigation-block__arrow" viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M24.5 7.88477H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1.38477C18.5 1.38477 19 3.7484 20.5 5.38477C22.1874 7.22551 25 7.88477 25 7.88477" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14.3848C18.5 14.3848 19 12.0211 20.5 10.3848C22.1874 8.54402 25 7.88477 25 7.88477" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </div>
</div>