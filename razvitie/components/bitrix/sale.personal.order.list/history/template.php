<?

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Page\Asset;

?>
<?if(!$USER->IsAuthorized()){
	LocalRedirect('/auth/');
}
?>
<div class="personal__main personal__main--history" data-simplebar>
    <table class="personal__history">
        <tbody>
            <tr>
                <th class="personal__history-column-name">Программа</th>
                <th class="personal__history-column-name">Направление</th>
                <th class="personal__history-column-name">Стоимость</th>
                <th class="personal__history-column-name">Дата</th>
            </tr>
			<?foreach($arResult["ORDERS"] as $order):
				foreach($order["BASKET_ITEMS"] as $item):
					$el = CIBlockElement::GetList(
						array("SORT"=>"ASC"),
						array("IBLOCK_ID"=>26, "ID"=>$item["ID"]),
						false,
						false,
						array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID")
					)->GetNext();
					
					$sect = CIBlockSection::GetList(
						array("SORT"=>"ASC"),
						array("IBLOCK_ID"=>26, "ID"=>$el["IBLOCK_SECTION_ID"]),
						false,
						array("ID", "IBLOCK_ID", "NAME"),
						false,
					)->GetNext();
					?>
					<tr>
		                <td class="personal__history-text"><?=$item["NAME"]?></td>
		                <td class="personal__history-text"><?=$sect["NAME"]?></td>
		                <td class="personal__history-text"><?=round((float)$item["BASE_PRICE"], 2)?> ₽</td>
		                <td class="personal__history-text"><?=$order['ORDER']['DATE_INSERT_FORMATED']?></td>
		            </tr>
					<?
				endforeach;
			endforeach;?>
		</tbody>
    </table>
</div>