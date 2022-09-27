<?
/*$_REQUEST['ID'] = htmlspecialchars($_REQUEST["ID"]);
//if(true){/* && $_REQUEST["ID"]!==''&&is_numeric($_REQUEST["ID"])
//if () {	
	require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
	CModule::IncludeModule('catalog');
	CModule::IncludeModule('sale');
	use \Bitrix\Sale,
		\Bitrix\Sale\Basket,
		\Bitrix\Sale\FUser,
		\Bitrix\Main\Context;

	$basket = \Bitrix\Sale\Basket::loadItemsForFUser(
	   \Bitrix\Sale\Fuser::getId(), 
	   \Bitrix\Main\Context::getCurrent()->getSite()
	);
	echo json_encode(["result"=>true]);
	//echo json_encode(["hasItem"=>$basket->getExistsItem('catalog', $_REQUEST["ID"])!==null]);
//}*/
//$_REQUEST['ID'] = htmlspecialchars($_REQUEST["ID"]);
//$_REQUEST["ID"]="95";
//$_SERVER['REQUEST_METHOD']="POST";
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('catalog');
CModule::IncludeModule('sale');
use \Bitrix\Sale,
	\Bitrix\Sale\Basket,
	\Bitrix\Sale\FUser,
	\Bitrix\Main\Context;
if($_SERVER["REQUEST_METHOD"]==="POST" && $_REQUEST["ID"]!=='' && is_numeric($_REQUEST["ID"])){
	$_REQUEST['ID']=(int)$_REQUEST["ID"];
	$basket = \Bitrix\Sale\Basket::loadItemsForFUser(
	   \Bitrix\Sale\Fuser::getId(), 
	   \Bitrix\Main\Context::getCurrent()->getSite()
	);
	foreach ($basket as $item) {
		$products[] = $item->getProductId();
	}
	echo json_encode(
		array("hasItem"=>in_array($_REQUEST["ID"], $products))
	);
}