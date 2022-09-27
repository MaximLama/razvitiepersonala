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

global $sectionsIds;
global $tree;
function coursesCount($count){
	switch ($count%100) {
		case 11:case 12:case 13: case 14:
			return $count." курсов";
		default:
			switch ($count%10) {
				case 1:
					return $count." курс";
				case 2:case 3:case 4:
					return $count." курса";				
				default:
					return $count." курсов";
					break;
			}
	}
}
function countHours($hours){
	switch ($hours%100) {
		case 11:case 12:case 13: case 14:
			return $hours." часов";
			break;
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
	return 0;
}
function hasCourses($id, $hours){
	$res = CIBlockElement::GetList(
		array("SORT"=>"ASC"),
		array("IBLOCK_ID"=>26, "SECTION_ID"=>$id, "INCLUDE_SUBSECTIONS"=>"Y", "<=PROPERTY_KOLVO_CHASOV"=>$hours),
		false,
		false,
		array("ID", "IBLOCK_ID")
	);
	$ids = [];
	while($el = $res->GetNext()){
		$ids[] = $el["ID"];
	}
	return $ids;
}
function hasSections($id, $sectionsIds){
	$res = CIBlockSection::GetList(
		array("SORT"=>"ASC"),
		array("IBLOCK_ID"=>26, "SECTION_ID"=>$id),
		false,
		array("ID", "IBLOCK_ID"),
		false
	);
	if(isset($_REQUEST['q'])&&$_REQUEST['q']!=""){
		$sect = '';
		while($section = $res->GetNext()){
			if(in_array($section["ID"], $sectionsIds)){
				$sect = $section;
			}
		}
	}
	else{
		$sect = $res->GetNext();
	}
	return (bool)$sect;
}
$sections = array();
foreach($arResult["SECTIONS"] as $sect):
	if($sect["DEPTH_LEVEL"]==1){
		$sect["SECTIONS"] = array();
		$sections[] = $sect;
		continue;
	}
	if($sect["DEPTH_LEVEL"]==2){
		$sections[count($sections)-1]["SECTIONS"][] = $sect;
	}
endforeach;
?>
<div class="services__cards-block">
	<?foreach($sections as $arSect1Lvl):?>
		<?if(isset($_POST["value"])&&$_POST["value"]!==""&&is_numeric($_POST['value'])){
			$_POST['value'] = (int)htmlspecialchars($_POST["value"]);
			if(empty(hasCourses($arSect1Lvl["ID"], $_POST["value"]))) continue;
		}else{
			if(!hasSections($arSect1Lvl["ID"], $sectionsIds)) continue;
		}?>

		<div class="services__cards">
	        <div class="services__cards-title"><?=$arSect1Lvl["NAME"]?></div>
	        <?foreach($arSect1Lvl["SECTIONS"] as $arSect2Lvl):?>
	        	<?if(isset($_REQUEST['q'])&&$_REQUEST['q']!=""){
	        		if(!in_array($arSect2Lvl["ID"], $sectionsIds)) continue;
	        	}?>

	        	<?if(isset($_POST["value"])&&$_POST["value"]!==""&&is_numeric($_POST['value'])){
					$_POST['value'] = (int)htmlspecialchars($_POST["value"]);
					$ids = hasCourses($arSect2Lvl["ID"], $_POST["value"]);
					if(empty($ids)) continue;
				}?>
		        <div class="services__item">
		            <div class="services__img-box">
		                <img src="<?=$arSect2Lvl["PICTURE"]["SRC"]?>" alt="<?=$arSect2Lvl["PICTURE"]["ALT"]?>">
		            </div>
		            <div class="services__item-text-block">
		                <div class="services__item-title"><?=$arSect2Lvl["~UF_NAZVANIE_KARTOCHKI"]?></div>
		                <?if((int)$arSect2Lvl["ELEMENT_CNT"]):?>
		                	<a href="<?=$arSect2Lvl["SECTION_PAGE_URL"]?>" class="services__item-link"><?=coursesCount((int)$arSect2Lvl["ELEMENT_CNT"])?></a>
		                <?endif;?>
		            </div>
		            <?if((int)$arSect2Lvl["ELEMENT_CNT"]):?>
			            <button class="services__btn">
			                <svg viewBox="0 0 21 12" fill="none" xmlns="http://www.w3.org/2000/svg">
			                    <path d="M9.7862 11.3622L0.795239 2.37809C0.401587 1.98387 0.401587 1.34517 0.795239 0.949961C1.18889 0.555742 1.82828 0.555742 2.22194 0.949961L10.4995 9.2215L18.7771 0.950957C19.1707 0.556739 19.8101 0.556739 20.2048 0.950957C20.5984 1.34518 20.5984 1.98487 20.2048 2.37909L11.2138 11.3632C10.8242 11.7515 10.1748 11.7515 9.7862 11.3622Z" stroke-width="1"/>
			                </svg>
			            </button>
			        <?else:?>
			        	<a href="<?=$arSect2Lvl["SECTION_PAGE_URL"]?>" class="services__btn services__btn--link">
			                <svg viewBox="0 0 21 12" fill="none" xmlns="http://www.w3.org/2000/svg">
			                    <path d="M9.7862 11.3622L0.795239 2.37809C0.401587 1.98387 0.401587 1.34517 0.795239 0.949961C1.18889 0.555742 1.82828 0.555742 2.22194 0.949961L10.4995 9.2215L18.7771 0.950957C19.1707 0.556739 19.8101 0.556739 20.2048 0.950957C20.5984 1.34518 20.5984 1.98487 20.2048 2.37909L11.2138 11.3632C10.8242 11.7515 10.1748 11.7515 9.7862 11.3622Z" stroke-width="1"/>
			                </svg>
			            </a>
			        <?endif;?>
		            <?if((int)$arSect2Lvl["ELEMENT_CNT"]):?>
			            <div class="services__card-sub-item">
			                <ul class="services__card-list">

			                	<?
			                	$filterField = "";
			                	global $arFilter;
			                	$arFilter = [];
			                	if(isset($_REQUEST['q'])&&$_REQUEST['q']!=""){
			                		$filterField = "arFilter";
			                		$key = array_search($arSect2Lvl["ID"], $sectionsIds);
			                		$arFilter["ID"] = $tree[$key]["ELEMENTS"];
			                	}
			                	if(isset($ids)&&!empty($ids)){
			                		if(empty($arFilter)){
			                			$filterField = "arFilter";
			                			$arFilter["ID"] = $ids;
			                		}
			                		else{
			                			$arFilter["ID"] = array_intersect($arFilter["ID"], $ids);
			                		}
			                	}
			                	?>

			                	<?$APPLICATION->IncludeComponent(
									"bitrix:news.list",
									"courses-list",
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
										"FILTER_NAME" => $filterField,
										"HIDE_LINK_WHEN_NO_DETAIL" => "N",
										"IBLOCK_ID" => "26",
										"IBLOCK_TYPE" => "osnovnye_dannye",
										"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
										"INCLUDE_SUBSECTIONS" => "Y",
										"MESSAGE_404" => "",
										"NEWS_COUNT" => "5",
										"PAGER_BASE_LINK_ENABLE" => "N",
										"PAGER_DESC_NUMBERING" => "N",
										"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
										"PAGER_SHOW_ALL" => "N",
										"PAGER_SHOW_ALWAYS" => "N",
										"PAGER_TEMPLATE" => ".default",
										"PAGER_TITLE" => "Новости",
										"PARENT_SECTION" => $arSect2Lvl["ID"],
										"PARENT_SECTION_CODE" => "",
										"PREVIEW_TRUNCATE_LEN" => "",
										"PROPERTY_CODE" => array("KOLVO_CHASOV","STOIMOST",""),
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

			                </ul>
			                <a href="<?=$arSect2Lvl["SECTION_PAGE_URL"]?>" class="services__card-more-link">
			                    Смотреть все
			                </a>
			            </div>
			        <?endif;?>
		        </div>
	        <?endforeach;?>
	    </div>
	<?endforeach;?>
    <div class="services__card-open-bg"></div>
</div>