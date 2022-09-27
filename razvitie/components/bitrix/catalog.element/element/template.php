<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(true);
$this->addExternalCss('/bitrix/css/main/bootstrap.css');
function countHours($hours){
	switch ($hours%100) {
		case 11:case 12:case 13: case 14:
			return $hours." часов";
		default:
			switch ($hours%10) {
				case 1:
					return $hours." час";
				case 2:case 3:case 4:
					return $hours." часа";				
				default:
					return $hours." часов";
					break;
			}
	}
}
?>
<section class="about-intro about-intro--service-page">
    <div class="container">
        <div class="about-intro__info-block">
        	<div class="about-intro__img-box" data-entity="images-container">
                <img src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>">
            </div>
            <div class="about-intro__title-content">
                <h1 class="about-intro__title"><?=$arResult["PROPERTIES"]["ZAGOLOVOK"]["~VALUE"]["TEXT"]?></h1>
                <div class="about-intro__price-block">
                    <div class="about-intro__price-text">Стоимость курса:</div>
                    <div class="about-intro__price"><?=$arResult["PROPERTIES"]["STOIMOST"]["VALUE"]?></div>
                </div>
                <a href="javascript:void(0)" class="about-intro__btn about-intro__btn--service-page buy" id="<?=$arResult["ID"]?>">
                    <span>Добавить в корзину</span>
                    <svg viewBox="0 0 26 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24.5 7.5H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1C18.5 1 19 3.36364 20.5 5C22.1874 6.84075 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14C18.5 14 19 11.6364 20.5 10C22.1874 8.15925 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </a>
            </div>

        </div>
    </div>
</section>
<section class="service-desc">
    <div class="container">
        <div class="service-desc__cards">
            <div class="service-desc__item">
                <div class="service-desc__number">01</div>
                <div class="service-desc__icon">
                    <svg viewBox="0 0 55 55" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M27.4758 4.58301C14.8258 4.58301 4.58203 14.8497 4.58203 27.4997C4.58203 40.1497 14.8258 50.4163 27.4758 50.4163C40.1487 50.4163 50.4154 40.1497 50.4154 27.4997C50.4154 14.8497 40.1487 4.58301 27.4758 4.58301ZM27.4987 45.833C17.3695 45.833 9.16536 37.6288 9.16536 27.4997C9.16536 17.3705 17.3695 9.16634 27.4987 9.16634C37.6279 9.16634 45.832 17.3705 45.832 27.4997C45.832 37.6288 37.6279 45.833 27.4987 45.833ZM26.9945 16.0413H26.857C25.9404 16.0413 25.207 16.7747 25.207 17.6913V28.508C25.207 29.3101 25.6195 30.0663 26.3299 30.4788L35.8404 36.1851C36.6195 36.6434 37.6279 36.4143 38.0862 35.6351C38.2013 35.4495 38.2778 35.2427 38.3113 35.0269C38.3448 34.8112 38.3345 34.5909 38.281 34.3792C38.2275 34.1675 38.132 33.9687 38.0001 33.7947C37.8682 33.6207 37.7027 33.475 37.5133 33.3663L28.6445 28.0955V17.6913C28.6445 16.7747 27.9112 16.0413 26.9945 16.0413Z" />
                    </svg>
                </div>
                <div class="service-desc__item-title">Продолжительность</div>
                <ul class="service-desc__item-list">
                    <li><?=countHours($arResult["PROPERTIES"]["KOLVO_CHASOV"]["VALUE"])?></li>
                </ul>
            </div>
            <div class="service-desc__item">
                <div class="service-desc__number">02</div>
                <div class="service-desc__icon">
                    <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 34.5C3 33.6716 3.67157 33 4.5 33H43.5C44.3284 33 45 33.6716 45 34.5C45 35.3284 44.3284 36 43.5 36H4.5C3.67157 36 3 35.3284 3 34.5Z"/><path d="M3 40.5C3 39.6716 3.67157 39 4.5 39H43.5C44.3284 39 45 39.6716 45 40.5C45 41.3284 44.3284 42 43.5 42H4.5C3.67157 42 3 41.3284 3 40.5Z"/><path d="M36 15C35.4067 15 34.8266 15.1759 34.3333 15.5056C33.8399 15.8352 33.4554 16.3038 33.2284 16.852C33.0013 17.4001 32.9419 18.0033 33.0576 18.5853C33.1734 19.1672 33.4591 19.7018 33.8787 20.1213C34.2982 20.5409 34.8328 20.8266 35.4147 20.9424C35.9967 21.0581 36.5999 20.9987 37.1481 20.7716C37.6962 20.5446 38.1648 20.1601 38.4944 19.6667C38.8241 19.1734 39 18.5933 39 18C39 17.2044 38.6839 16.4413 38.1213 15.8787C37.5587 15.3161 36.7957 15 36 15Z"/><path d="M24 24C22.8133 24 21.6533 23.6481 20.6666 22.9888C19.6799 22.3295 18.9109 21.3925 18.4567 20.2961C18.0026 19.1997 17.8838 17.9933 18.1153 16.8295C18.3468 15.6656 18.9182 14.5965 19.7574 13.7574C20.5965 12.9182 21.6656 12.3468 22.8295 12.1153C23.9933 11.8838 25.1997 12.0026 26.2961 12.4567C27.3925 12.9109 28.3295 13.6799 28.9888 14.6666C29.6481 15.6533 30 16.8133 30 18C29.998 19.5907 29.3652 21.1157 28.2405 22.2405C27.1157 23.3652 25.5907 23.998 24 24ZM24 15C23.4067 15 22.8266 15.176 22.3333 15.5056C21.8399 15.8352 21.4554 16.3038 21.2284 16.852C21.0013 17.4001 20.9419 18.0033 21.0576 18.5853C21.1734 19.1672 21.4591 19.7018 21.8787 20.1213C22.2982 20.5409 22.8328 20.8266 23.4147 20.9424C23.9967 21.0581 24.5999 20.9987 25.1481 20.7716C25.6962 20.5446 26.1648 20.1601 26.4944 19.6667C26.8241 19.1734 27 18.5933 27 18C26.9992 17.2046 26.6829 16.442 26.1204 15.8796C25.558 15.3171 24.7954 15.0008 24 15Z"/><path d="M12 15C11.4067 15 10.8266 15.1759 10.3333 15.5056C9.83994 15.8352 9.45543 16.3038 9.22836 16.852C9.0013 17.4001 8.94189 18.0033 9.05765 18.5853C9.1734 19.1672 9.45912 19.7018 9.87868 20.1213C10.2982 20.5409 10.8328 20.8266 11.4147 20.9424C11.9967 21.0581 12.5999 20.9987 13.1481 20.7716C13.6962 20.5446 14.1648 20.1601 14.4944 19.6667C14.8241 19.1734 15 18.5933 15 18C15 17.2044 14.6839 16.4413 14.1213 15.8787C13.5587 15.3161 12.7957 15 12 15Z"/><path d="M42 30H6C5.20496 29.998 4.44305 29.6813 3.88087 29.1191C3.31869 28.557 3.00198 27.795 3 27V9C3.00198 8.20496 3.31869 7.44305 3.88087 6.88087C4.44305 6.31869 5.20496 6.00198 6 6H42C42.795 6.00198 43.557 6.31869 44.1191 6.88087C44.6813 7.44305 44.998 8.20496 45 9V27C44.9988 27.7953 44.6824 28.5577 44.12 29.12C43.5577 29.6824 42.7953 29.9988 42 30ZM42 9H6V27H42V9Z"/>
                    </svg>
                </div>
                <div class="service-desc__item-title">Стоимость</div>
                <ul class="service-desc__item-list">
                    <li><?=$arResult["PROPERTIES"]["STOIMOST"]["VALUE"]?> ₽</li>
                </ul>
            </div>
            <div class="service-desc__item">
                <div class="service-desc__number">03</div>
                <div class="service-desc__icon">
                    <svg viewBox="0 0 57 57" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.696 32.0518C19.4032 32.0518 20.0815 31.7709 20.5815 31.2708C21.0816 30.7707 21.3625 30.0925 21.3625 29.3853C21.3625 28.6781 21.0816 27.9998 20.5815 27.4998C20.0815 26.9997 19.4032 26.7188 18.696 26.7188C17.9888 26.7188 17.3105 26.9997 16.8105 27.4998C16.3104 27.9998 16.0295 28.6781 16.0295 29.3853C16.0295 30.0925 16.3104 30.7707 16.8105 31.2708C17.3105 31.7709 17.9888 32.0518 18.696 32.0518ZM21.3643 38.2933C21.3643 39.0005 21.0834 39.6788 20.5833 40.1788C20.0832 40.6789 19.405 40.9598 18.6978 40.9598C17.9906 40.9598 17.3123 40.6789 16.8123 40.1788C16.3122 39.6788 16.0312 39.0005 16.0312 38.2933C16.0312 37.5861 16.3122 36.9079 16.8123 36.4078C17.3123 35.9077 17.9906 35.6268 18.6978 35.6268C19.405 35.6268 20.0832 35.9077 20.5833 36.4078C21.0834 36.9079 21.3643 37.5861 21.3643 38.2933ZM28.5 32.0483C29.2067 32.0483 29.8845 31.7675 30.3843 31.2678C30.884 30.768 31.1648 30.0902 31.1648 29.3835C31.1648 28.6768 30.884 27.999 30.3843 27.4992C29.8845 26.9995 29.2067 26.7188 28.5 26.7188C27.7928 26.7188 27.1145 26.9997 26.6145 27.4998C26.1144 27.9998 25.8335 28.6781 25.8335 29.3853C25.8335 30.0925 26.1144 30.7707 26.6145 31.2708C27.1145 31.7709 27.7928 32.0518 28.5 32.0518V32.0483ZM31.1683 38.2898C31.1683 38.997 30.8874 39.6752 30.3873 40.1753C29.8872 40.6753 29.209 40.9563 28.5018 40.9563C27.7946 40.9563 27.1163 40.6753 26.6163 40.1753C26.1162 39.6752 25.8352 38.997 25.8352 38.2898C25.8352 37.5825 26.1162 36.9043 26.6163 36.4042C27.1163 35.9042 27.7946 35.6232 28.5018 35.6232C29.209 35.6232 29.8872 35.9042 30.3873 36.4042C30.8874 36.9043 31.1683 37.5825 31.1683 38.2898ZM38.2933 32.0483C39.0005 32.0483 39.6788 31.7673 40.1788 31.2672C40.6789 30.7672 40.9598 30.0889 40.9598 29.3817C40.9598 28.6745 40.6789 27.9963 40.1788 27.4962C39.6788 26.9961 39.0005 26.7152 38.2933 26.7152C37.5861 26.7152 36.9079 26.9961 36.4078 27.4962C35.9077 27.9963 35.6268 28.6745 35.6268 29.3817C35.6268 30.0889 35.9077 30.7672 36.4078 31.2672C36.9079 31.7673 37.5861 32.0483 38.2933 32.0483ZM49.875 16.0312C49.875 13.6692 48.9367 11.4038 47.2664 9.73358C45.5962 8.06333 43.3308 7.125 40.9688 7.125H16.0312C13.6692 7.125 11.4038 8.06333 9.73358 9.73358C8.06333 11.4038 7.125 13.6692 7.125 16.0312V40.9688C7.125 43.3308 8.06333 45.5962 9.73358 47.2664C11.4038 48.9367 13.6692 49.875 16.0312 49.875H40.9688C43.3308 49.875 45.5962 48.9367 47.2664 47.2664C48.9367 45.5962 49.875 43.3308 49.875 40.9688V16.0312ZM10.6875 21.375H46.3125V40.9688C46.3125 42.386 45.7495 43.7452 44.7474 44.7474C43.7452 45.7495 42.386 46.3125 40.9688 46.3125H16.0312C14.614 46.3125 13.2548 45.7495 12.2526 44.7474C11.2505 43.7452 10.6875 42.386 10.6875 40.9688V21.375ZM16.0312 10.6875H40.9688C42.386 10.6875 43.7452 11.2505 44.7474 12.2526C45.7495 13.2548 46.3125 14.614 46.3125 16.0312V17.8125H10.6875V16.0312C10.6875 14.614 11.2505 13.2548 12.2526 12.2526C13.2548 11.2505 14.614 10.6875 16.0312 10.6875Z" />
                    </svg>
                </div>
                <div class="service-desc__item-title">Срок действия</div>
                <ul class="service-desc__item-list">
                	<?foreach($arResult["PROPERTIES"]["PERIODICHNOST"]["VALUE"] as $text):?>
                		<li><?=$text?></li>
                	<?endforeach;?>
                </ul>
            </div>
        </div>
        <div class="service-desc__text-content">
                <?=$arResult["PROPERTIES"]["TREBOVANIYA"]["~VALUE"]["TEXT"]?>
            <a href="/kontakty/#feedback" class="service-desc__btn">
                <span>Отправить заявку</span>
                <svg viewBox="0 0 26 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M24.5 7.5H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1C18.5 1 19 3.36364 20.5 5C22.1874 6.84075 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14C18.5 14 19 11.6364 20.5 10C22.1874 8.15925 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </a>
        </div>
    </div>
</section>
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
<section class="documents">
    <div class="container">
    	<?
    	$parentSectionsObj = CIBlockSection::GetList(
    		array("SORT"=>"ASC"),
    		array("IBLOCK_ID"=>26, "ID"=>$arResult["SECTION"]["ID"]),
    		false,
    		array("ID", "IBLOCK_ID", "UF_DOCS"),
    		false
    	);
    	$parentSection = $parentSectionsObj->GetNext();
    	$res = CIBlockElement::GetList(
    		array("SORT"=>"ASC"),
    		array("IBLOCK_ID"=>35, "ID"=>$parentSection["UF_DOCS"]),
    		false,
    		false,
    		array("ID", "IBLOCK_ID", "PROPERTY_DOCUMENT", "NAME", "PREVIEW_PICTURE", "PREVIEW_TEXT", "PROPERTY_NAZVANIE")
    	);
    	$docs = array();
    	while($doc = $res->GetNext()){
    		$docs[] = $doc;
    	}
    	?>
        <div class="documents__title">Выдаваемые документы</div>
        <div class="documents__cards desktop">
        	<?foreach($docs as $doc):?>
        		<div class="documents__item documents__item--big">
	                <div class="documents__image-block">
	                    <div class="documents__img-bg">
	                        <img src="<?=SITE_TEMPLATE_PATH?>/img/review-page-img-bg.svg" alt="">
	                    </div>
	                    <div class="documents__img-box">
	                        <img src="<?=CFile::GetPath($doc["PREVIEW_PICTURE"])?>" alt="<?=$doc["~PROPERTY_NAZVANIE_VALUE"]?>">
	                    </div>
	                </div>
	                <div class="documents__text-block">
	                    <div class="documents__item-title">
	                        <?=$doc["~PROPERTY_NAZVANIE_VALUE"]?>
	                    </div>
	                    <p class="documents__item-text">
	                        <?=$doc["~PREVIEW_TEXT"]?>
	                    </p>
	                </div>
	                <a href="<?=CFile::GetPath($doc["PROPERTY_DOCUMENT_VALUE"])?>" target="_blank" class="documents__item-btn">
	                    <span>смотреть</span>
	                    <svg viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg">
	                        <path d="M8.65019 8.49969L2.35445 14.7933C2.0782 15.0689 1.63062 15.0689 1.35367 14.7933C1.07742 14.5178 1.07742 14.0702 1.35367 13.7947L7.15004 8.00038L1.35437 2.20611C1.07812 1.93055 1.07812 1.48298 1.35437 1.20673C1.63062 0.931171 2.0789 0.931171 2.35515 1.20673L8.65089 7.50037C8.92295 7.77308 8.92295 8.22768 8.65019 8.49969Z" stroke-width="0.5"/>
	                    </svg>
	                </a>
	            </div>
        	<?endforeach;?>
        </div>
        <div class="documents__cards mobile">
        	<?foreach($docs as $doc):?>
        		<div class="documents__item documents__item--big">
	                <div class="documents__image-block">
	                    <div class="documents__img-bg">
	                        <img src="<?=SITE_TEMPLATE_PATH?>/img/review-page-img-bg.svg" alt="">
	                    </div>
	                    <div class="documents__img-box">
	                        <img src="<?=CFile::GetPath($doc["PREVIEW_PICTURE"])?>" alt="<?=$doc["~NAME"]?>">
	                    </div>
	                </div>
	                <div class="documents__text-block">
	                    <div class="documents__item-title">
	                        <?=$doc["~NAME"]?>
	                    </div>
	                    <p class="documents__item-text">
	                        <?=$doc["~PREVIEW_TEXT"]?>
	                    </p>
	                </div>
	                <a href="<?=CFile::GetPath($doc["PROPERTY_DOCUMENT_VALUE"])?>" class="documents__item-btn">
	                    <span>смотреть</span>
	                    <svg viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg">
	                        <path d="M8.65019 8.49969L2.35445 14.7933C2.0782 15.0689 1.63062 15.0689 1.35367 14.7933C1.07742 14.5178 1.07742 14.0702 1.35367 13.7947L7.15004 8.00038L1.35437 2.20611C1.07812 1.93055 1.07812 1.48298 1.35437 1.20673C1.63062 0.931171 2.0789 0.931171 2.35515 1.20673L8.65089 7.50037C8.92295 7.77308 8.92295 8.22768 8.65019 8.49969Z" stroke-width="0.5"/>
	                    </svg>
	                </a>
	            </div>
        	<?endforeach;?>
        </div>
    </div>
</section>
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
<section class="courses courses--watched">
    <div class="container">
        <div class="courses__title-block">
            <div class="courses__title">С этим курсом часто смотрят</div>
            <div class="courses__navigation-block navigation-block">
                <div class="navigation-block__pagination-block">
                    <div class="courses__current-slide--watched navigation-block__current-slide"></div>
                    <div class="courses__pagination--watched navigation-block__pagination"></div>
                    <div class="courses__total-slide--watched navigation-block__total-slide"></div>
                </div>
                <div class="navigation-block__arrows">
                    <svg class="courses__arrow-left--watched navigation-block__arrow" viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.5 7.88477H25" stroke-width="2" stroke-linecap="round"/><path d="M7.5 1.38477C7.5 1.38477 7 3.7484 5.5 5.38477C3.81265 7.22551 1 7.88477 1 7.88477" stroke-width="2" stroke-linecap="round"/><path d="M7.5 14.3848C7.5 14.3848 7 12.0211 5.5 10.3848C3.81265 8.54402 1 7.88477 1 7.88477" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <svg class="courses__arrow-right--watched navigation-block__arrow" viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24.5 7.88477H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1.38477C18.5 1.38477 19 3.7484 20.5 5.38477C22.1874 7.22551 25 7.88477 25 7.88477" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14.3848C18.5 14.3848 19 12.0211 20.5 10.3848C22.1874 8.54402 25 7.88477 25 7.88477" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="courses__slider courses__slider--watched swiper">
        	<?$res = CIBlockElement::GetList(
        		array("SORT"=>"ASC"),
        		array("IBLOCK_ID"=>26, "ID"=>$arResult["PROPERTIES"]["DRUGIE_KURSY"]["VALUE"]),
        		false,
        		false,
        		array("ID", "IBLOCK_ID", "NAME", "DETAIL_PICTURE")
        	);
        	$courses = array();
        	while($course = $res->GetNext()){
        		$courses[] = $course;
        	}?>
            <div class="swiper-wrapper">
            	
            	<?foreach($courses as $key=>$course):?>
            		<div class="courses__item swiper-slide">
	                    <div class="courses__item-title-block">
	                        <div class="courses__title-text-block">
	                            <div class="courses__item-name">Программа</div>
	                            <div class="courses__item-title"><?=$course["~NAME"]?></div>
	                        </div>
	                        <div class="courses__item-number">
	                            <span>0<?=($key+1)?></span>
	                        </div>
	                    </div>
	                    <div class="courses__image-block">
	                        <div class="courses__img-box">
	                            <img src="<?=CFile::GetPath($course["DETAIL_PICTURE"])?>" alt="<?=$course["~NAME"]?>">
	                        </div>
	                    </div>
	                </div>
            	<?endforeach;?>
                
            </div>
        </div>
    </div>
</section>

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
    	<?
    	$res = CIBlockElement::GetList(
    		array("SORT"=>"ASC"),
    		array("IBLOCK_ID"=>33, "ID"=>$arResult["PROPERTIES"]["KONTENT"]["VALUE"]),
    		false,
    		false,
    		array("ID", "IBLOCK_ID", "PROPERTY_ZAGOLOVOK", "DETAIL_PICTURE", "DETAIL_TEXT")
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
                    <img src="<?=CFile::GetPath($value["DETAIL_PICTURE"])?>" alt="<?=$value["~PROPERTY_ZAGOLOVOK_VALUE"]["TEXT"]?>">
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