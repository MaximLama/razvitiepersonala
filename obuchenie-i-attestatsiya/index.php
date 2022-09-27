<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Обучение и аттестация");

$els = CIBlockElement::GetList(
    array("PROPERTY_KOLVO_CHASOV" => "DESC")
);
$GLOBALS['MAX_TIME'] = $els->GetNext()["PROPERTY_KOLVO_CHASOV_VALUE"];

$tree = [];
$sectionsIds = [];
$APPLICATION->IncludeComponent(
    "bitrix:search.page",
    "search",
    array(
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A",
        "CHECK_DATES" => "N",
        "DEFAULT_SORT" => "rank",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "DISPLAY_TOP_PAGER" => "N",
        "FILTER_NAME" => "",
        "NO_WORD_LOGIC" => "N",
        "PAGER_SHOW_ALWAYS" => "Y",
        "PAGER_TEMPLATE" => "",
        "PAGER_TITLE" => "Результаты поиска",
        "PAGE_RESULT_COUNT" => "",
        "RESTART" => "N",
        "SHOW_WHEN" => "N",
        "SHOW_WHERE" => "N",
        "USE_LANGUAGE_GUESS" => "Y",
        "USE_SUGGEST" => "N",
        "USE_TITLE_RANK" => "N",
        "arrFILTER" => array("iblock_osnovnye_dannye"),
        "arrFILTER_iblock_osnovnye_dannye" => array("26"),
        "arrWHERE" => array("iblock_osnovnye_dannye")
    )
); ?>

    <section class="services">
        <div class="container">

            <? $res = CIBlock::GetByID(26);
            $arRes = $res->GetNext() ?>

            <h1 class="services__title"><?= $arRes["NAME"] ?></h1>

            <div class="services__content">
                <nav class="services__services-menu desktop">
                    <div class="services__services-menu-name">
                        <div class="services__services-burger">
                            <div></div>
                        </div>
                        <span>Наши услуги</span>
                    </div>

                    <? $APPLICATION->IncludeComponent(
                        "bitrix:catalog.section.list",
                        "courses-left-menu",
                        array(
                            "ADD_SECTIONS_CHAIN" => "N",
                            "CACHE_FILTER" => "N",
                            "CACHE_GROUPS" => "Y",
                            "CACHE_TIME" => "36000000",
                            "CACHE_TYPE" => "A",
                            "COUNT_ELEMENTS" => "N",
                            "COUNT_ELEMENTS_FILTER" => "CNT_ACTIVE",
                            "FILTER_NAME" => "",
                            "IBLOCK_ID" => "26",
                            "IBLOCK_TYPE" => "osnovnye_dannye",
                            "SECTION_CODE" => "",
                            "SECTION_FIELDS" => array(
                                0 => "NAME",
                                1 => "",
                            ),
                            "SECTION_ID" => "",
                            "SECTION_URL" => "",
                            "SECTION_USER_FIELDS" => array(
                                0 => "",
                                1 => "",
                            ),
                            "SHOW_PARENT_NAME" => "Y",
                            "TOP_DEPTH" => "2",
                            "VIEW_MODE" => "LIST",
                            "COMPONENT_TEMPLATE" => ".default"
                        ),
                        false
                    ); ?>

                    <div class="services__filter-block">
                        <div class="services__filter-name open">
                            <span>Количество часов обучения</span>
                            <svg class="filter-disable desktop" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.75 10.5V16.5C9.75 16.6989 9.82902 16.8897 9.96967 17.0303C10.1103 17.171 10.3011 17.25 10.5 17.25C10.6989 17.25 10.8897 17.171 11.0303 17.0303C11.171 16.8897 11.25 16.6989 11.25 16.5V10.5C11.25 10.3011 11.171 10.1103 11.0303 9.96967C10.8897 9.82902 10.6989 9.75 10.5 9.75C10.3011 9.75 10.1103 9.82902 9.96967 9.96967C9.82902 10.1103 9.75 10.3011 9.75 10.5ZM13.5 9.75C13.6989 9.75 13.8897 9.82902 14.0303 9.96967C14.171 10.1103 14.25 10.3011 14.25 10.5V16.5C14.25 16.6989 14.171 16.8897 14.0303 17.0303C13.8897 17.171 13.6989 17.25 13.5 17.25C13.3011 17.25 13.1103 17.171 12.9697 17.0303C12.829 16.8897 12.75 16.6989 12.75 16.5V10.5C12.75 10.3011 12.829 10.1103 12.9697 9.96967C13.1103 9.82902 13.3011 9.75 13.5 9.75ZM15 6H19.5C19.6989 6 19.8897 6.07902 20.0303 6.21967C20.171 6.36032 20.25 6.55109 20.25 6.75C20.25 6.94891 20.171 7.13968 20.0303 7.28033C19.8897 7.42098 19.6989 7.5 19.5 7.5H18.6705L17.5425 17.664C17.4406 18.5813 17.004 19.4288 16.3163 20.0443C15.6285 20.6598 14.738 21.0001 13.815 21H10.185C9.26205 21.0001 8.37148 20.6598 7.68373 20.0443C6.99599 19.4288 6.55939 18.5813 6.4575 17.664L5.328 7.5H4.5C4.30109 7.5 4.11032 7.42098 3.96967 7.28033C3.82902 7.13968 3.75 6.94891 3.75 6.75C3.75 6.55109 3.82902 6.36032 3.96967 6.21967C4.11032 6.07902 4.30109 6 4.5 6H9C9 5.20435 9.31607 4.44129 9.87868 3.87868C10.4413 3.31607 11.2044 3 12 3C12.7956 3 13.5587 3.31607 14.1213 3.87868C14.6839 4.44129 15 5.20435 15 6ZM12 4.5C11.6022 4.5 11.2206 4.65804 10.9393 4.93934C10.658 5.22064 10.5 5.60218 10.5 6H13.5C13.5 5.60218 13.342 5.22064 13.0607 4.93934C12.7794 4.65804 12.3978 4.5 12 4.5ZM6.8385 7.5L7.9485 17.499C8.00977 18.0493 8.27179 18.5576 8.68442 18.9268C9.09705 19.296 9.63132 19.5001 10.185 19.5H13.815C14.3684 19.4997 14.9023 19.2955 15.3146 18.9263C15.727 18.5572 15.9888 18.049 16.05 17.499L17.163 7.5H6.84H6.8385Z"/>
                            </svg>
                            <svg class="mobile" viewBox="0 0 16 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.35578 8.4595L1.06213 1.96406C0.78658 1.67904 0.78658 1.21727 1.06213 0.931535C1.33769 0.64652 1.78526 0.64652 2.06082 0.931535L7.85509 6.91177L13.6494 0.932256C13.9249 0.64724 14.3725 0.64724 14.6487 0.932256C14.9243 1.21727 14.9243 1.67976 14.6487 1.96478L8.3551 8.46022C8.08239 8.74092 7.62779 8.74092 7.35578 8.4595Z"
                                      stroke-width="1"/>
                            </svg>
                        </div>
                        <label class="services__label-range disabled">
                            <!-- Класс disabled для label при выключеном input -->
                            <div class="services__input-range-value input-range-value"></div>
                            <input type="range" min="0" max="<?= $GLOBALS["MAX_TIME"] ?>"
                                   value="<?= $GLOBALS["MAX_TIME"] ?>"
                                   class="services__input-range input-range desktop">
                            <div class="services__range-min-max-block">
                                <span class="range-min"></span>
                                <span class="range-max"></span>
                            </div>
                        </label>
                    </div>
                </nav>
                <nav class="services__services-menu mobile">
                    <div class="services__services-menu-name">
                        <div class="services__services-burger">
                            <div></div>
                        </div>
                        <span>Наши услуги</span>
                    </div>

                    <? $APPLICATION->IncludeComponent(
                        "bitrix:catalog.section.list",
                        "courses-left-menu",
                        array(
                            "ADD_SECTIONS_CHAIN" => "N",
                            "CACHE_FILTER" => "N",
                            "CACHE_GROUPS" => "Y",
                            "CACHE_TIME" => "36000000",
                            "CACHE_TYPE" => "A",
                            "COUNT_ELEMENTS" => "N",
                            "COUNT_ELEMENTS_FILTER" => "CNT_ACTIVE",
                            "FILTER_NAME" => "",
                            "IBLOCK_ID" => "26",
                            "IBLOCK_TYPE" => "osnovnye_dannye",
                            "SECTION_CODE" => "",
                            "SECTION_FIELDS" => array(
                                0 => "NAME",
                                1 => "",
                            ),
                            "SECTION_ID" => "",
                            "SECTION_URL" => "",
                            "SECTION_USER_FIELDS" => array(
                                0 => "",
                                1 => "",
                            ),
                            "SHOW_PARENT_NAME" => "Y",
                            "TOP_DEPTH" => "2",
                            "VIEW_MODE" => "LIST",
                            "COMPONENT_TEMPLATE" => ".default",
                            "sectionsIds" => $sectionsIds
                        ),
                        false
                    ); ?>

                    <div class="services__filter-block">
                        <div class="services__filter-name open">
                            <span>Количество часов обучения</span>
                            <svg class="mobile" viewBox="0 0 16 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.35578 8.4595L1.06213 1.96406C0.78658 1.67904 0.78658 1.21727 1.06213 0.931535C1.33769 0.64652 1.78526 0.64652 2.06082 0.931535L7.85509 6.91177L13.6494 0.932256C13.9249 0.64724 14.3725 0.64724 14.6487 0.932256C14.9243 1.21727 14.9243 1.67976 14.6487 1.96478L8.3551 8.46022C8.08239 8.74092 7.62779 8.74092 7.35578 8.4595Z"
                                      stroke-width="1"/>
                            </svg>
                        </div>
                        <div class="services__range-block">
                            <div class="services__input-range-value">
                                <span class="input-range-value"></span>
                                <svg class="filter-disable" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.75 10.5V16.5C9.75 16.6989 9.82902 16.8897 9.96967 17.0303C10.1103 17.171 10.3011 17.25 10.5 17.25C10.6989 17.25 10.8897 17.171 11.0303 17.0303C11.171 16.8897 11.25 16.6989 11.25 16.5V10.5C11.25 10.3011 11.171 10.1103 11.0303 9.96967C10.8897 9.82902 10.6989 9.75 10.5 9.75C10.3011 9.75 10.1103 9.82902 9.96967 9.96967C9.82902 10.1103 9.75 10.3011 9.75 10.5ZM13.5 9.75C13.6989 9.75 13.8897 9.82902 14.0303 9.96967C14.171 10.1103 14.25 10.3011 14.25 10.5V16.5C14.25 16.6989 14.171 16.8897 14.0303 17.0303C13.8897 17.171 13.6989 17.25 13.5 17.25C13.3011 17.25 13.1103 17.171 12.9697 17.0303C12.829 16.8897 12.75 16.6989 12.75 16.5V10.5C12.75 10.3011 12.829 10.1103 12.9697 9.96967C13.1103 9.82902 13.3011 9.75 13.5 9.75ZM15 6H19.5C19.6989 6 19.8897 6.07902 20.0303 6.21967C20.171 6.36032 20.25 6.55109 20.25 6.75C20.25 6.94891 20.171 7.13968 20.0303 7.28033C19.8897 7.42098 19.6989 7.5 19.5 7.5H18.6705L17.5425 17.664C17.4406 18.5813 17.004 19.4288 16.3163 20.0443C15.6285 20.6598 14.738 21.0001 13.815 21H10.185C9.26205 21.0001 8.37148 20.6598 7.68373 20.0443C6.99599 19.4288 6.55939 18.5813 6.4575 17.664L5.328 7.5H4.5C4.30109 7.5 4.11032 7.42098 3.96967 7.28033C3.82902 7.13968 3.75 6.94891 3.75 6.75C3.75 6.55109 3.82902 6.36032 3.96967 6.21967C4.11032 6.07902 4.30109 6 4.5 6H9C9 5.20435 9.31607 4.44129 9.87868 3.87868C10.4413 3.31607 11.2044 3 12 3C12.7956 3 13.5587 3.31607 14.1213 3.87868C14.6839 4.44129 15 5.20435 15 6ZM12 4.5C11.6022 4.5 11.2206 4.65804 10.9393 4.93934C10.658 5.22064 10.5 5.60218 10.5 6H13.5C13.5 5.60218 13.342 5.22064 13.0607 4.93934C12.7794 4.65804 12.3978 4.5 12 4.5ZM6.8385 7.5L7.9485 17.499C8.00977 18.0493 8.27179 18.5576 8.68442 18.9268C9.09705 19.296 9.63132 19.5001 10.185 19.5H13.815C14.3684 19.4997 14.9023 19.2955 15.3146 18.9263C15.727 18.5572 15.9888 18.049 16.05 17.499L17.163 7.5H6.84H6.8385Z"/>
                                </svg>
                            </div>
                            <label class="services__label-range disabled">
                                <!-- Класс disabled для label при выключеном input -->
                                <input type="range" min="0" max="<?= $GLOBALS["MAX_TIME"] ?>"
                                       value="<?= $GLOBALS["MAX_TIME"] ?>"
                                       class="services__input-range input-range mobile">
                            </label>
                            <div class="services__range-min-max-block">
                                <span class="range-min"></span>
                                <span class="range-max"></span>
                            </div>
                        </div>
                    </div>
                </nav>

                <? $APPLICATION->IncludeComponent(
                    "bitrix:catalog.section.list",
                    "courses",
                    array(
                        "ADD_SECTIONS_CHAIN" => "N",
                        "CACHE_FILTER" => "N",
                        "CACHE_GROUPS" => "Y",
                        "CACHE_TIME" => "36000000",
                        "CACHE_TYPE" => "A",
                        "COUNT_ELEMENTS" => "Y",
                        "COUNT_ELEMENTS_FILTER" => "CNT_ACTIVE",
                        "FILTER_NAME" => "",
                        "IBLOCK_ID" => "26",
                        "IBLOCK_TYPE" => "osnovnye_dannye",
                        "SECTION_CODE" => "",
                        "SECTION_FIELDS" => array("NAME", "PICTURE", ""),
                        "SECTION_ID" => "",
                        "SECTION_URL" => "",
                        "SECTION_USER_FIELDS" => array("UF_NAZVANIE_KARTOCHKI", ""),
                        "SHOW_PARENT_NAME" => "Y",
                        "TOP_DEPTH" => "2",
                        "VIEW_MODE" => "LIST"
                    )
                ); ?>
            </div>
        </div>
    </section>

    <section class="about-content desktop">
        <div class="container">
            <? $APPLICATION->IncludeComponent(
                "bitrix:news.detail",
                "udostoverenie",
                array(
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
                    "DISPLAY_PICTURE" => "N",
                    "DISPLAY_PREVIEW_TEXT" => "N",
                    "DISPLAY_TOP_PAGER" => "N",
                    "ELEMENT_CODE" => "",
                    "ELEMENT_ID" => 33,
                    "FIELD_CODE" => array("NAME", ""),
                    "FILE_404" => "",
                    "IBLOCK_ID" => "27",
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
                    "PROPERTY_CODE" => array("OPISANIE", "TEKST_SSYLKI", ""),
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
            ); ?>
        </div>
    </section>
<? $APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "feedback",
    array(
        "AREA_FILE_SHOW" => "file",
        "AREA_FILE_SUFFIX" => "inc",
        "EDIT_TEMPLATE" => "standard.php",
        "PATH" => SITE_TEMPLATE_PATH . '/includes/feedback.php'
    )
); ?>
<? $APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "klienty",
    array(
        "AREA_FILE_SHOW" => "file",
        "AREA_FILE_SUFFIX" => "",
        "EDIT_TEMPLATE" => "standard.php",
        "PATH" => SITE_TEMPLATE_PATH . '/includes/klienty.php'
    )
); ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>