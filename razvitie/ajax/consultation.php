<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
require_once('./amo/amo.php');

use \Bitrix\Main\Application;

if($_SERVER['REQUEST_METHOD']=="POST"&&check_bitrix_sessid()){
	if(!CModule::IncludeModule('iblock'))die(json_encode(["error"=>"Ошибка запроса"]));

	$amo = new AMO;

	$context = Application::getInstance()->getContext();
    $request = $context->getRequest();
	$rsUser = CIBlockElement::GetList(
		array("SORT"=>"ASC"),
		array("IBLOCK_ID"=>36, "PROPERTY_PHONE"=>$request->get("USER_PHONE")),
		false,
		false,
		array("ID", "IBLOCK_ID", "PROPERTY_PHONE", "PROPERTY_EMAIL", "PROPERTY_CONTACT_ID")
	);
	if($arUser = $rsUser->GetNext()){
		$orderData["contact_id"] = $arUser["PROPERTY_CONTACT_ID_VALUE"];
		if($request->get("elementName")){
			$name = "Тестовый запрос на консультацию по курсу: {$request->get("elementName")}";
			$response = $amo->setConsultation($orderData, $name);
		}
		else if($request->get("sectionName")){
			$name = "Тестовый запрос на консультацию по услуге: {$request->get("sectionName")}";
			$response = $amo->setConsultation($orderData, $name);
		}
		else
			$response = $amo->setConsultation($orderData);
	}
	else{
		$contact["name"] = $request->get("USER_NAME");
		$contact["phone"] = $request->get("USER_PHONE");
		if($request->get("elementName")){
			$name = "Тестовый запрос на консультацию по курсу: {$request->get("elementName")}";
			$response = $amo->setComplexConsultation($contact, $name);
		}
		else if($request->get("sectionName")){
			$name = "Тестовый запрос на консультацию по услуге: {$request->get("sectionName")}";
			$response = $amo->setComplexConsultation($contact, $name);
		}
		else
			$response = $amo->setComplexConsultation($contact);
		$response = $amo->setComplexConsultation($contact);
		$newUser = new CIBlockElement;
		$PROP = array(
			"PHONE"=>$request->get("USER_PHONE"),
			"CONTACT_ID"=>(int)$response[0]["contact_id"]
		);

		$arUserFields = [
			"NAME"=>uniqid(),
			"PROPERTY_VALUES"=>$PROP,
			"IBLOCK_ID"=>36,
			"ACTIVE"=>"Y"
		];
		$newUser->Add($arUserFields);
	}
	$response = ["code"=>$amo->getCode()];

	echo json_encode($response);
}
else{
	echo json_encode(array("error"=>"Ошибка запроса"));
}