<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 *
 *  _________________________________________________________________________
 * |	Attention!
 * |	The following comments are for system use
 * |	and are required for the component to work correctly in ajax mode:
 * |	<!-- items-container -->
 * |	<!-- pagination-container -->
 * |	<!-- component-end -->
 */

$this->setFrameMode(true);
$res = CIBlockElement::GetList(
	array("ID"=>$arResult["ELEMENTS"]),
	array("ID"=>$arResult["ELEMENTS"], "IBLOCK_ID"=>$arResult["IBLOCK_ID"]),
	false,
	false,
	array("ID", "IBLOCK_ID", "PROPERTY_NAZVANIE_KARTOCHKI")
);
$els = array();
while($el = $res->GetNext()){
	$els[] = $el;
}

?>

<section class="about-intro about-intro--service">
    <div class="container">
        <div class="about-intro__info-block">
            <div class="about-intro__img-box">
                <img src="<?=$arResult["DETAIL_PICTURE"]["SRC"] ?? ''?>" alt="<?= $arResult['DETAIL_PICTURE']['ALT'] ?>">
            </div>
            <div class="about-intro__title-content">
                <h1 class="about-intro__title"><?=$arResult["~UF_ZAGOLOVOK"]?></h1>
                <p class="about-intro__description">
                    <?=$arResult["~UF_PODZAGOLOVOK"]?>
                </p>
            </div>
        </div>
    </div>
</section>
<section class="service-desc">
    <div class="container">
        <div class="service-desc__text-content">
            <div class="service-desc__title">
                Подробнее об услуге
            </div>
            <p class="service-desc__description">
                <?=$arResult["~UF_TREBOVANIE"]?>
            </p>
            <?
            $query = http_build_query(array("SECTION_NAME"=>$arResult["NAME"]));
            ?>
            <a href="/kontakty/?<?=$query?>#feedback" class="service-desc__btn">
                <span>Отправить заявку</span>
                <svg viewBox="0 0 26 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M24.5 7.5H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1C18.5 1 19 3.36364 20.5 5C22.1874 6.84075 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14C18.5 14 19 11.6364 20.5 10C22.1874 8.15925 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </a>
        </div>
    </div>
</section>
<?if(count($arResult["ITEMS"])):?>
    <section class="course-page">
        <div class="container">
            <div class="course-page__title">Курсы, входящие в данный раздел</div>
            <div class="course-page__cards">
    			<?

    			foreach ($arResult['ITEMS'] as $key => $item)
    			{
    				$item["~PROPERTY_NAZVANIE_KARTOCHKI_VALUE"] = $els[$key]["~PROPERTY_NAZVANIE_KARTOCHKI"]; 
    				$APPLICATION->IncludeComponent(
    					'bitrix:catalog.item',
    					'course',
    					array(
    						'RESULT' => array(
    							'ITEM' => $item,
    							'AREA_ID' => $areaIds[$item['ID']],
    							'TYPE' => 'CARD',
    							'BIG_LABEL' => 'N',
    							'BIG_DISCOUNT_PERCENT' => 'N',
    							'BIG_BUTTONS' => 'N',
    							'SCALABLE' => 'N',
    						)
    					),
    					$component,
    					array('HIDE_ICONS' => 'Y')
    				);
    			}
    			?>
    		</div>
        </div>
    </section>
<?endif;?>
<?if($arResult['UF_SHOW_ETAPY_OBUCHENIYA']!=="0"):?>
    <?if($arResult['UF_ETAPY_OBUCHENIYA']!=="0"):?>
    <?$APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "kak-prohodit-obuchenie",
        Array(
            "AREA_FILE_SHOW" => "file",
            "AREA_FILE_SUFFIX" => "",
            "EDIT_TEMPLATE" => "standard.php",
            "PATH" => "/bitrix/templates/razvitie/includes/kak-prohodit-obuchenie.php"
        )
    );?>
    <?else:?>
        <section class="partner-stages partner-stages--five">
            <div class="container">
                <div class="partner-stages__title"><?=$arResult["UF_ZAGOLOVOK_CUSTOM_ETAP"]?></div>
                <div class="partner-stages__cards">
                    <div class="partner-stages__wrapper">
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:news.list",
                            "etapy-obucheniya",
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
                                "FIELD_CODE" => array("NAME",""),
                                "FILE_404" => "",
                                "FILTER_NAME" => "",
                                "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                                "IBLOCK_ID" => "28",
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
                                "PARENT_SECTION" => $arResult["UF_CUSTOM_LEARNING_ELEMENTS"],
                                "PARENT_SECTION_CODE" => "",
                                "PREVIEW_TRUNCATE_LEN" => "",
                                "PROPERTY_CODE" => array("","IKONKA",""),
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
                <div class="partner-stages__arrow-block mobile">
                    <svg class="partner-stages__arrow-left" viewBox="0 0 26 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.5 7.5H25" stroke-width="2" stroke-linecap="round"/><path d="M7.5 1C7.5 1 7 3.36364 5.5 5C3.81265 6.84075 1 7.5 1 7.5" stroke-width="2" stroke-linecap="round"/><path d="M7.5 14C7.5 14 7 11.6364 5.5 10C3.81265 8.15925 1 7.5 1 7.5" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <svg class="partner-stages__arrow-right" viewBox="0 0 26 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24.5 7.5H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1C18.5 1 19 3.36364 20.5 5C22.1874 6.84075 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14C18.5 14 19 11.6364 20.5 10C22.1874 8.15925 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </section>
    <?endif;?>
<?endif;?>
<?if($arResult['UF_PREIMUSHESTVA_DIST_OBUCHENIYA']!=="0"):?>
<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "preimushestva-dist-obucheniya",
    Array(
        "AREA_FILE_SHOW" => "file",
        "AREA_FILE_SUFFIX" => "",
        "EDIT_TEMPLATE" => "",
        "PATH" => "/bitrix/templates/razvitie/includes/preimushestva-dist-obucheniya.php"
    )
);?>
<?endif;?>
<?if($arResult['UF_DIST_LEARNING']!=="0"):?>
<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "dist-learning",
    Array(
        "AREA_FILE_SHOW" => "file",
        "AREA_FILE_SUFFIX" => "",
        "EDIT_TEMPLATE" => "",
        "PATH" => "/bitrix/templates/razvitie/includes/dist-learning.php"
    )
);?>
<?endif;?>
<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "licenzii",
    Array(
        "AREA_FILE_SHOW" => "file",
        "AREA_FILE_SUFFIX" => "",
        "EDIT_TEMPLATE" => "",
        "PATH" => "/bitrix/templates/razvitie/includes/licenzii.php"
    )
);?>

<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "all-reviews",
    Array(
        "AREA_FILE_SHOW" => "file",
        "AREA_FILE_SUFFIX" => "",
        "EDIT_TEMPLATE" => "",
        "PATH" => "/bitrix/templates/razvitie/includes/all-reviews.php"
    )
);?>


<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "klienty",
    Array(
        "AREA_FILE_SHOW" => "file",
        "AREA_FILE_SUFFIX" => "",
        "EDIT_TEMPLATE" => "standard.php",
        "PATH" => SITE_TEMPLATE_PATH.'/includes/klienty.php'
    )
);?>
<section class="service-article">
    <div class="container">

        <?$res = CIBlockElement::GetList(
            array("SORT"=>"ASC"),
            array("ID"=>$arResult["UF_SECTION_KONTENT"], "IBLOCK_ID"=>34),
            false,
            false,
            array("ID", "IBLOCK_ID", "DETAIL_TEXT", "DETAIL_PICTURE", "PROPERTY_ZAGOLOVOK")
        );
        $content = array();
        while($txt = $res->GetNext()){
            $content[] = $txt;
        }
        ?>
        <?foreach($content as $key=>$value):?>
            <?if($key%2):?>
                <article class="service-article__block service-article__block--img-right">
            <?else:?>
                <article class="service-article__block">
            <?endif;?>
                <div class="service-article__img-box">
                    <img src="<?=CFile::GetPath($value["DETAIL_PICTURE"])?>" alt="<?=$value["~PROPERTY_ZAGOLOVOK_VALUE"]['TEXT']?>">
                </div>
                <div class="service-article__text-block">
                    <div class="service-article__title">
                        <?=$value["~PROPERTY_ZAGOLOVOK_VALUE"]["TEXT"]?>
                    </div>
                    <?=$value["~DETAIL_TEXT"]?>
                </div>
            </article>
        <?endforeach;?>

    </div>
</section>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"modal-cart",
	Array(
		"AREA_FILE_SHOW" => "file",
		"AREA_FILE_SUFFIX" => "inc",
		"EDIT_TEMPLATE" => "standard.php",
		"PATH" => "/bitrix/templates/razvitie/includes/modal_cart.php"
	)
);?>