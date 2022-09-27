<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;

$res = CIBlockSection::GetList(
	array("SORT"=>"ASC"),
	array("IBLOCK_ID"=>26, "SECTION_ID"=>false),
	false,
	array("ID", "IBLOCK_ID", "SECTION_PAGE_URL"),
	false
);
$urls = array();
while($sect = $res->GetNext()){
	$urls[] = $sect["SECTION_PAGE_URL"];
}

//delayed function must return a string
if(empty($arResult))
	return "";

$strReturn = '';

$strReturn .= '<div class="page-pagination">
            <div class="container page-pagination__container">';

$itemSize = count($arResult);
for($index = 0; $index < $itemSize; $index++)
{
	if(array_search($arResult[$index]["LINK"], $urls)!==false) continue;

	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	$arrow = ($index > 0? '<svg class="page-pagination__arrow" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.65019 8.65399L2.35445 14.9476C2.0782 15.2232 1.63062 15.2232 1.35367 14.9476C1.07742 14.6721 1.07742 14.2245 1.35367 13.9489L7.15004 8.15468L1.35437 2.3604C1.07812 2.08485 1.07812 1.63728 1.35437 1.36102C1.63062 1.08547 2.0789 1.08547 2.35515 1.36102L8.65089 7.65467C8.92295 7.92737 8.92295 8.38198 8.65019 8.65399Z" stroke-width="0.5"/>
                </svg>' : '');

	if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1)
	{
		$strReturn .= $arrow.'<a href="'.$arResult[$index]["LINK"].'" class="page-pagination__page page-pagination__page--old">'.$title.'</a>';
	}
	else
	{
		$strReturn .= $arrow.'<div class="page-pagination__page page-pagination__page--current">'.$title.'</div>';
	}
}

$strReturn .= '</div>
        </div>';

return $strReturn;
?>