<section class="partners">
	<div class="container">
		<div class="partners__title-content">

			<?$APPLICATION->IncludeComponent(
				"bitrix:news.detail",
				"klienty",
				Array(
					"ACTIVE_DATE_FORMAT" => "d.m.Y",
					"ADD_ELEMENT_CHAIN" => "N",
					"ADD_SECTIONS_CHAIN" => "N",
					"AJAX_MODE" => "N",
					"AJAX_OPTION_ADDITIONAL" => "",
					"AJAX_OPTION_HISTORY" => "N",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"BROWSER_TITLE" => "-",
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
					"ELEMENT_CODE" => "",
					"ELEMENT_ID" => 11,
					"FIELD_CODE" => array("NAME", "PREVIEW_TEXT", ""),
					"FILE_404" => "",
					"IBLOCK_ID" => "25",
					"IBLOCK_TYPE" => "odinochnye_bloki",
					"IBLOCK_URL" => "",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"MESSAGE_404" => "",
					"META_DESCRIPTION" => "-",
					"META_KEYWORDS" => "-",
					"PAGER_BASE_LINK_ENABLE" => "N",
					"PAGER_SHOW_ALL" => "N",
					"PAGER_TEMPLATE" => ".default",
					"PAGER_TITLE" => "Страница",
					"PROPERTY_CODE" => array("", ""),
					"SET_BROWSER_TITLE" => "N",
					"SET_CANONICAL_URL" => "N",
					"SET_LAST_MODIFIED" => "N",
					"SET_META_DESCRIPTION" => "N",
					"SET_META_KEYWORDS" => "N",
					"SET_STATUS_404" => "Y",
					"SET_TITLE" => "N",
					"SHOW_404" => "Y",
					"STRICT_SECTION_CHECK" => "N",
					"USE_PERMISSIONS" => "N",
					"USE_SHARE" => "N"
				)
			);?>

		</div>
		<div class="partners__slider swiper">
			<div class="swiper-wrapper">

				<?$APPLICATION->IncludeComponent(
					"bitrix:news.list",
					"klienty",
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
						"DISPLAY_NAME" => "N",
						"DISPLAY_PICTURE" => "Y",
						"DISPLAY_PREVIEW_TEXT" => "Y",
						"DISPLAY_TOP_PAGER" => "N",
						"FIELD_CODE" => array("PREVIEW_PICTURE", ""),
						"FILE_404" => "",
						"FILTER_NAME" => "",
						"HIDE_LINK_WHEN_NO_DETAIL" => "N",
						"IBLOCK_ID" => "19",
						"IBLOCK_TYPE" => "osnovnye_dannye",
						"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
						"INCLUDE_SUBSECTIONS" => "N",
						"MESSAGE_404" => "",
						"NEWS_COUNT" => "100",
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
						"PROPERTY_CODE" => array("", ""),
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
		<div class="partners__navigation-block navigation-block">
			<div class="navigation-block__pagination-block">
				<div class="partners__current-slide navigation-block__current-slide"></div>
				<div class="partners__pagination navigation-block__pagination"></div>
				<div class="partners__total-slide navigation-block__total-slide"></div>
			</div>
			<div class="navigation-block__arrows">
				<svg class="partners__arrow-left navigation-block__arrow" viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M1.5 7.88477H25" stroke-width="2" stroke-linecap="round"/><path d="M7.5 1.38477C7.5 1.38477 7 3.7484 5.5 5.38477C3.81265 7.22551 1 7.88477 1 7.88477" stroke-width="2" stroke-linecap="round"/><path d="M7.5 14.3848C7.5 14.3848 7 12.0211 5.5 10.3848C3.81265 8.54402 1 7.88477 1 7.88477" stroke-width="2" stroke-linecap="round"/>
				</svg>
				<svg class="partners__arrow-right navigation-block__arrow" viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M24.5 7.88477H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1.38477C18.5 1.38477 19 3.7484 20.5 5.38477C22.1874 7.22551 25 7.88477 25 7.88477" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14.3848C18.5 14.3848 19 12.0211 20.5 10.3848C22.1874 8.54402 25 7.88477 25 7.88477" stroke-width="2" stroke-linecap="round"/>
				</svg>
			</div>
		</div>
	</div>
</section>