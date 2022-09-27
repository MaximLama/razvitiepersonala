<section class="more-center">
    <div class="more-center__bg"></div>
    <div class="container">

        <?$APPLICATION->IncludeComponent(
            "bitrix:news.detail",
            "licenzii",
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
                "DISPLAY_PREVIEW_TEXT" => "N",
                "DISPLAY_TOP_PAGER" => "N",
                "ELEMENT_CODE" => "",
                "ELEMENT_ID" => 44,
                "FIELD_CODE" => array("NAME", "DETAIL_TEXT", "DETAIL_PICTURE", ""),
                "FILE_404" => "",
                "IBLOCK_ID" => "31",
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
                "PROPERTY_CODE" => array("TEKST_SSYLKI_GOS_REESTRA", ""),
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

        <a href="/o-nas/" class="more-center__btn">
            <span>Подробнее о нас</span>
            <svg viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M24.5 8.26953H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1.76953C18.5 1.76953 19 4.13317 20.5 5.76953C22.1874 7.61028 25 8.26953 25 8.26953" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14.7695C18.5 14.7695 19 12.4059 20.5 10.7695C22.1874 8.92878 25 8.26953 25 8.26953" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </a>
    </div>
</section>