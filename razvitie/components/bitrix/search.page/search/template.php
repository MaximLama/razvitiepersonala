<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
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
?>

<?

global $tree;
global $sectionsIds;

class SearchRecreator{
	public $searchItems;
	public $sections;
	public $sectionsIds;
	public $elements;

	public function __construct($arResult){
		$this->searchItems = $arResult;
	}

	public function sortItems(){
		$exclude = $this->getSection1Lvl();
		foreach($this->searchItems as $item){
			if(strncmp($item["ITEM_ID"], "S", 1)!=0){
				$this->elements[] = $item;
			}
			else{
				if(array_search(str_replace("S", "", $item["ITEM_ID"]), $exclude)===false){
					$this->sections[] = $item;
				}
			}
		}
	}

	public function getSectionsByElemsID(){
//		$ids = $this->getElementsIds();
//		$arFilter = array("IBLOCK_ID"=>26, "ID"=>$ids);
//		$res = CIBlockElement::GetList(
//			array("ID"=>$ids),
//			$arFilter,
//			false,
//			false,
//			array("ID", "IBLOCK_SECTION_ID")
//		);
        $sectionsIds = [];
//        $els = [];

//		while($element = $res->GetNext()){
//			$els[] = $element;
//			$sectionsIds[] = $element["IBLOCK_SECTION_ID"];
//		}
		$sectionsIds = array_unique($sectionsIds);
		$tree = [];
//		foreach($els as $el){
//			$key = array_search($el["IBLOCK_SECTION_ID"], $sectionsIds);
//			if(!isset($tree[$key]["ELEMENTS"])){
//				$tree[$key] = array("ELEMENTS"=>[], "ID"=>$sectionsIds[$key]);
//			}
//			$tree[$key]["ELEMENTS"][] = $el["ID"];
//		}
		foreach($this->sections as $section){
			$id = str_replace("S", "", $section["ITEM_ID"]);
			if(!in_array($id, $sectionsIds)){
				$sectionsIds[] = $id;
				$tree[count($sectionsIds)-1] = array("ID"=>$id, "ELEMENTS"=>[0]);
			}
		}
		return [$tree, $sectionsIds];
		
	}

	public function getElementsIds(){
		$ids = [];
		foreach($this->elements as $el){
			$ids[] = (int)$el['ITEM_ID'];
		}
		return $ids;
	}

	private function getSection1Lvl(){
		$rsSections = CIBlockSection::GetList(
			array("SORT"=>"ASC"),
			array("IBLOCK_ID"=>26, "DEPTH_LEVEL"=>1),
			false,
			array("IBLOCK_ID", "ID"),
			false
		);
		$arSections = [];
		while($arSection = $rsSections->GetNext()){
			$arSections[] = $arSection["ID"];
		}
		return $arSections;
	}
}
if(isset($_REQUEST['q'])&&$_REQUEST['q']=="") return;
$search = new SearchRecreator($arResult["SEARCH"]);
$search->sortItems();
[$tree, $sectionsIds] = $search->getSectionsByElemsID();

$sections11=[];
foreach($search->sections as $item){
    $item['ITEM_ID'] = str_replace("S", "", $item["ITEM_ID"]);
    $sections11[] = (int)$item['ITEM_ID'];

}

?>