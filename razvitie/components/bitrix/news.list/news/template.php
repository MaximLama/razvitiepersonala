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
function toRuDate($date){
	$MES = array(
	  	"January"   => "января",
	  	"February"  => "февраля",
	  	"March"     => "марта",
	  	"April"     => "апреля",
	  	"May"       => "мая",
	  	"June"      => "июня",
	  	"July"      => "июля",
	  	"August"    => "августа",
	  	"September" => "сентября",
	  	"October"   => "октября",
	  	"November"  => "ноября",
	  	"December"  => "декабря"
	  );
	$dateArr = explode(" ", $date);
	$dateArr[1] = $MES[$dateArr[1]];
	return implode(" ", $dateArr);
}
?>
<?foreach($arResult["ITEMS"] as $key=>$arItem):?>

	<?$kurs = CIBlockElement::GetByID($arItem["PROPERTIES"]["KURS"]["VALUE"])->GetNext();?>

	<?if($key%2):?>
		<div class="news__item news__item--red swiper-slide">
	<?else:?>
		<div class="news__item news__item--blue swiper-slide">
	<?endif;?>
        <div class="news__img-box">
            <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>">
        </div>
        <div class="news__item-title">
            <?=$arItem["~NAME"]?>
        </div>
        <div class="news__courses-name">
            <?=$kurs["~NAME"]?>
        </div>
        <div class="news__info-block">
            <a href="<?=$kurs["DETAIL_PAGE_URL"]?>" class="news__link">Перейти к курсу</a>
            <div class="news__date"><?=toRuDate(ConvertDateTime($arItem["TIMESTAMP_X"], "DD MMMM YYYY, HH:MI", "ru"))?></div>
        </div>
    </div>
<?endforeach;?>