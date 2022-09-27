<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
	return;
}

$arParams["PATH_TO_BASKET"] = Trim($arParams["PATH_TO_BASKET"]);
if ($arParams["PATH_TO_BASKET"] == '')
	$arParams["PATH_TO_BASKET"] = "basket.php";

$arParams["PATH_TO_PERSONAL"] = Trim($arParams["PATH_TO_PERSONAL"]);
if ($arParams["PATH_TO_PERSONAL"] == '')
	$arParams["PATH_TO_PERSONAL"] = "index.php";

$arParams["PATH_TO_PAYMENT"] = Trim($arParams["PATH_TO_PAYMENT"]);
if ($arParams["PATH_TO_PAYMENT"] == '')
	$arParams["PATH_TO_PAYMENT"] = "payment.php";

$arParams["PATH_TO_AUTH"] = Trim($arParams["PATH_TO_AUTH"]);
if ($arParams["PATH_TO_AUTH"] == '')
	$arParams["PATH_TO_AUTH"] = "/auth.php";

$arParams["ALLOW_PAY_FROM_ACCOUNT"] = (($arParams["ALLOW_PAY_FROM_ACCOUNT"] == "N") ? "N" : "Y");
$arParams["COUNT_DELIVERY_TAX"] = (($arParams["COUNT_DELIVERY_TAX"] == "Y") ? "Y" : "N");
$arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] = (($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y") ? "Y" : "N");
$arParams["PATH_TO_ORDER"] = $APPLICATION->GetCurPage();
$arParams["SHOW_MENU"] = ($arParams["SHOW_MENU"] == "N" ? "N" : "Y" );
$arParams["ALLOW_EMPTY_CITY"] = ($arParams["CITY_OUT_LOCATION"] == "N" ? "N" : "Y" );

$arParams["SHOW_AJAX_LOCATIONS"] = $arParams["SHOW_AJAX_LOCATIONS"] == 'N' ? 'N' : 'Y';

$arParams['PRICE_VAT_SHOW_VALUE'] = $arParams['PRICE_VAT_SHOW_VALUE'] == 'N' ? 'N' : 'Y';

$arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] = (($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y") ? "Y" : "N");
$arParams["SEND_NEW_USER_NOTIFY"] = (($arParams["SEND_NEW_USER_NOTIFY"] == "N") ? "N" : "Y");
$arResult["AUTH"]["new_user_registration_email_confirmation"] = ((COption::GetOptionString("main", "new_user_registration_email_confirmation", "N") == "Y") ? "Y" : "N");
$arResult["AUTH"]["new_user_registration"] = ((COption::GetOptionString("main", "new_user_registration", "Y") == "Y") ? "Y" : "N");

$bUseAccountNumber = \Bitrix\Sale\Integration\Numerator\NumeratorOrder::isUsedNumeratorForOrder();

if (!$arParams["DELIVERY_NO_SESSION"])
	$arParams["DELIVERY_NO_SESSION"] = "N";

if($arParams["SET_TITLE"] == "Y")
{
	if($USER->IsAuthorized())
		$APPLICATION->SetTitle(GetMessage("STOF_MAKING_ORDER"));
	else
		$APPLICATION->SetTitle(GetMessage("STOF_AUTH"));
}

if($arResult["POST"]["ORDER_PRICE"] <> '')
	$arResult["ORDER_PRICE"]  = doubleval($arResult["POST"]["ORDER_PRICE"]);
if($arResult["POST"]["ORDER_WEIGHT"] <> '')
	$arResult["ORDER_WEIGHT"] = doubleval($arResult["POST"]["ORDER_WEIGHT"]);

$arResult["WEIGHT_UNIT"] = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_unit', "", SITE_ID));
$arResult["WEIGHT_KOEF"] = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_koef', 1, SITE_ID));

$GLOBALS['CATALOG_ONETIME_COUPONS_BASKET']=null;
$GLOBALS['CATALOG_ONETIME_COUPONS_ORDER']=null;

$allCurrency = CSaleLang::GetLangCurrency(SITE_ID);

if ($_SERVER["REQUEST_METHOD"] == "POST" && ($arParams["DELIVERY_NO_SESSION"] == "N" || check_bitrix_sessid()))
{
	foreach($_POST as $k => $v)
	{
		if(!is_array($v))
		{
			$arResult["POST"][$k] = htmlspecialcharsex($v);
			$arResult["POST"]['~'.$k] = $v;
		}
		else
		{
			foreach($v as $kk => $vv)
			{
				$arResult["POST"][$k][$kk] = htmlspecialcharsex($vv);
				$arResult["POST"]['~'.$k][$kk] = $vv;
			}
		}
	}
}

$arResult["SKIP_FIRST_STEP"] = (($arResult["POST"]["SKIP_FIRST_STEP"] == "Y") ? "Y" : "N");
$arResult["SKIP_SECOND_STEP"] = (($arResult["POST"]["SKIP_SECOND_STEP"] == "Y") ? "Y" : "N");
$arResult["SKIP_THIRD_STEP"] = (($arResult["POST"]["SKIP_THIRD_STEP"] == "Y") ? "Y" : "N");
$arResult["SKIP_FORTH_STEP"] = (($arResult["POST"]["SKIP_FORTH_STEP"] == "Y") ? "Y" : "N");

if($arResult["POST"]["PERSON_TYPE"] <> '')
	$arResult["PERSON_TYPE"] = intval($arResult["POST"]["PERSON_TYPE"]);
if($arResult["POST"]["PROFILE_ID"] <> '')
{
	$arResult["PROFILE_ID"] = intval($arResult["POST"]["PROFILE_ID"]);
	$dbUserProfiles = CSaleOrderUserProps::GetList(
			array("DATE_UPDATE" => "DESC"),
			array(
					"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"],
					"USER_ID" => intval($USER->GetID()),
					"ID" => $arResult["PROFILE_ID"],
				)
		);
	if(!$dbUserProfiles->GetNext())
		$arResult["PROFILE_ID"] = 0;
}
if($arResult["POST"]["DELIVERY_ID"] <> '')
{
	if (mb_strpos($arResult["POST"]["DELIVERY_ID"], ":") === false)
		$arResult["DELIVERY_ID"] = intval($arResult["POST"]["DELIVERY_ID"]);
	else
		$arResult["DELIVERY_ID"] = explode(":", $arResult["POST"]["DELIVERY_ID"]);
}
if($arResult["POST"]["PAY_SYSTEM_ID"] <> '')
	$arResult["PAY_SYSTEM_ID"] = intval($arResult["POST"]["PAY_SYSTEM_ID"]);
if($arResult["POST"]["PAY_CURRENT_ACCOUNT"] <> '')
	$arResult["PAY_CURRENT_ACCOUNT"] = $arResult["POST"]["PAY_CURRENT_ACCOUNT"];
else
	$arResult["PAY_CURRENT_ACCOUNT"] = "N";
if($arResult["POST"]["TAX_EXEMPT"] <> '')
	$arResult["TAX_EXEMPT"] = $arResult["POST"]["TAX_EXEMPT"];
if($arResult["POST"]["ORDER_DESCRIPTION"] <> '')
	$arResult["ORDER_DESCRIPTION"] = trim($arResult["POST"]["ORDER_DESCRIPTION"]);
if ($_REQUEST["CurrentStep"] == 7 || ($_SERVER["REQUEST_METHOD"] == "POST" && ($arParams["DELIVERY_NO_SESSION"] == "N" || check_bitrix_sessid())))
{
	if($_REQUEST["ORDER_ID"] <> '')
		$ID = urldecode(urldecode($_REQUEST["ORDER_ID"]));
	if(intval($_REQUEST["CurrentStep"])>0)
		$arResult["CurrentStep"] = intval($_REQUEST["CurrentStep"]);
	if(intval($_REQUEST["CurrentStep"])>0)
		$CurrentStepTmp = intval($_REQUEST["CurrentStep"]);
	elseif(intval($arResult["POST"]["CurrentStep"])>0)
		$CurrentStepTmp = intval($arResult["POST"]["CurrentStep"]);
}
$arResult["BACK"] = (($arResult["POST"]["BACK"] == "Y") ? "Y" : "");

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_REQUEST["backButton"] <> '' && ($arParams["DELIVERY_NO_SESSION"] == "N" || check_bitrix_sessid()))
{
	if($arResult["POST"]["CurrentStep"] == 6 && $arResult["SKIP_FORTH_STEP"] == "Y")
		$arResult["CurrentStepTmp"] = 3;

	if($arResult["POST"]["CurrentStepTmp"] <= 5 && $arResult["SKIP_THIRD_STEP"] == "Y")
		$arResult["CurrentStepTmp"] = 2;

	if($arResult["POST"]["CurrentStepTmp"] <= 3 && $arResult["SKIP_SECOND_STEP"] == "Y")
		$arResult["CurrentStepTmp"] = 1;

	if(intval($arResult["CurrentStepTmp"])>0)
		$arResult["CurrentStep"] = $arResult["CurrentStepTmp"];
	else
		$arResult["CurrentStep"] = $arResult["CurrentStep"] - 2;
	$arResult["BACK"] = "Y";
}
if ($arResult["CurrentStep"] <= 0)
	$arResult["CurrentStep"] = 1;
$arResult["ERROR_MESSAGE"] = "";





if (!$USER->IsAuthorized())
{
	$arResult["USER_LOGIN"] = (($arResult["POST"]["USER_LOGIN"] <> '') ? $arResult["POST"]["USER_LOGIN"] : htmlspecialcharsbx(${COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"}));
	$arResult["AUTH"]["captcha_registration"] = ((COption::GetOptionString("main", "captcha_registration", "N") == "Y") ? "Y" : "N");
	if($arResult["AUTH"]["captcha_registration"] == "Y")
		$arResult["AUTH"]["capCode"] = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());
	$arResult["AUTH"]["new_user_registration"] = ((COption::GetOptionString("main", "new_user_registration", "Y") == "Y") ? "Y" : "N");

	if($_SERVER["REQUEST_METHOD"] == "POST" && ($arParams["DELIVERY_NO_SESSION"] == "N" || check_bitrix_sessid()))
	{
		if ($arResult["POST"]["do_authorize"] == "Y")
		{
			if ($arResult["POST"]["USER_LOGIN"] == '')
				$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_AUTH_LOGIN").".<br />";

			if ($arResult["ERROR_MESSAGE"] == '')
			{
				$arAuthResult = $USER->Login($arResult["POST"]["~USER_LOGIN"], $arResult["POST"]["~USER_PASSWORD"], "N");
				if ($arAuthResult != False && $arAuthResult["TYPE"] == "ERROR")
					$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_AUTH").(($arAuthResult["MESSAGE"] <> '') ? ": ".$arAuthResult["MESSAGE"] : ".<br />" );
				else
					LocalRedirect($arParams["PATH_TO_ORDER"]);

			}
		}
		elseif ($arResult["POST"]["do_register"] == "Y" && $arResult["AUTH"]["new_user_registration"] == "Y")
		{
			if ($arResult["POST"]["NEW_NAME"] == '')
				$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_NAME").".<br />";

			if ($arResult["POST"]["NEW_LAST_NAME"] == '')
				$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_LASTNAME").".<br />";

			if ($arResult["POST"]["NEW_EMAIL"] == '')
				$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_EMAIL").".<br />";
			elseif (!check_email($arResult["POST"]["NEW_EMAIL"]))
				$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_BAD_EMAIL").".<br />";

			if ($arResult["POST"]["NEW_GENERATE"] == "Y")
			{
				$arResult["POST"]["~NEW_LOGIN"] = $arResult["POST"]["~NEW_EMAIL"];

				$pos = mb_strpos($arResult["POST"]["~NEW_LOGIN"], "@");
				if ($pos !== false)
					$arResult["POST"]["~NEW_LOGIN"] = mb_substr($arResult["POST"]["~NEW_LOGIN"], 0, $pos);

				if (mb_strlen($arResult["POST"]["~NEW_LOGIN"]) > 47)
					$arResult["POST"]["~NEW_LOGIN"] = mb_substr($arResult["POST"]["~NEW_LOGIN"], 0, 47);

				if (mb_strlen($arResult["POST"]["~NEW_LOGIN"]) < 3)
					$arResult["POST"]["~NEW_LOGIN"] .= "_";

				if (mb_strlen($arResult["POST"]["~NEW_LOGIN"]) < 3)
					$arResult["POST"]["~NEW_LOGIN"] .= "_";

				$dbUserLogin = CUser::GetByLogin($arResult["POST"]["~NEW_LOGIN"]);
				if ($arUserLogin = $dbUserLogin->Fetch())
				{
					$newLoginTmp = $arResult["POST"]["~NEW_LOGIN"];
					$uind = 0;
					do
					{
						$uind++;
						if ($uind == 10)
						{
							$arResult["POST"]["~NEW_LOGIN"] = $arResult["POST"]["~NEW_EMAIL"];
							$newLoginTmp = $arResult["POST"]["~NEW_LOGIN"];
						}
						elseif ($uind > 10)
						{
							$arResult["POST"]["~NEW_LOGIN"] = "buyer".time().GetRandomCode(2);
							$newLoginTmp = $arResult["POST"]["~NEW_LOGIN"];
							break;
						}
						else
						{
							$newLoginTmp = $arResult["POST"]["~NEW_LOGIN"].$uind;
						}
						$dbUserLogin = CUser::GetByLogin($newLoginTmp);
					}
					while ($arUserLogin = $dbUserLogin->Fetch());
					$arResult["POST"]["~NEW_LOGIN"] = $newLoginTmp;
				}

				$def_group = COption::GetOptionString("main", "new_user_registration_def_group", "");
				if($def_group!="")
				{
					$GROUP_ID = explode(",", $def_group);
				}
				else
				{
					$GROUP_ID = [];
				}

				$arResult["POST"]["~NEW_PASSWORD"] = $arResult["POST"]["~NEW_PASSWORD_CONFIRM"] = \CAllUser::GeneratePasswordByPolicy($GROUP_ID);
			}
			else
			{
				if ($arResult["POST"]["NEW_LOGIN"] == '')
					$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_FLAG").".<br />";

				if ($arResult["POST"]["NEW_PASSWORD"] == '')
					$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_FLAG1").".<br />";

				if ($arResult["POST"]["NEW_PASSWORD"] <> '' && $arResult["POST"]["NEW_PASSWORD_CONFIRM"] == '')
					$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_FLAG1").".<br />";

				if ($arResult["POST"]["NEW_PASSWORD"] <> ''
					&& $arResult["POST"]["NEW_PASSWORD_CONFIRM"] <> ''
					&& $arResult["POST"]["NEW_PASSWORD"] != $arResult["POST"]["NEW_PASSWORD_CONFIRM"])
					$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_PASS").".<br />";
			}

			if ($arResult["ERROR_MESSAGE"] == '')
			{
				$arAuthResult = $USER->Register($arResult["POST"]["~NEW_LOGIN"], $arResult["POST"]["~NEW_NAME"], $arResult["POST"]["~NEW_LAST_NAME"], $arResult["POST"]["~NEW_PASSWORD"], $arResult["POST"]["~NEW_PASSWORD_CONFIRM"], $arResult["POST"]["~NEW_EMAIL"], LANG, $arResult["POST"]["~captcha_word"], $arResult["POST"]["~captcha_sid"]);
				if ($arAuthResult != False && $arAuthResult["TYPE"] == "ERROR")
					$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG").(($arAuthResult["MESSAGE"] <> '') ? ": ".$arAuthResult["MESSAGE"] : ".<br />" );
				else
				{
					if ($USER->IsAuthorized())
					{
						if($arParams["SEND_NEW_USER_NOTIFY"] == "Y")
							CUser::SendUserInfo($USER->GetID(), SITE_ID, GetMessage("INFO_REQ"), true);
						LocalRedirect($arParams["PATH_TO_ORDER"]);
					}
					else
					{
						$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_CONFIRM")."<br />";
					}
				}
			}
		}
	}
}
else
{
	$arResult["BASE_LANG_CURRENCY"] = CSaleLang::GetLangCurrency(SITE_ID);

	if ($arResult["CurrentStep"] > 0 && $arResult["CurrentStep"] <= 6)
	{
		if ($arResult["PAY_CURRENT_ACCOUNT"] != "N" && $arParams["ALLOW_PAY_FROM_ACCOUNT"] == "Y")
			$arResult["PAY_CURRENT_ACCOUNT"] = "Y";

		// <***************** BEFORE 1 STEP
		$arResult["ORDER_PRICE"] = 0;
		$arResult["ORDER_WEIGHT"] = 0;
		$bProductsInBasket = False;
		$arResult["bUsingVat"] = "N";
		$arResult["vatRate"] = 0;
		$arResult["vatSum"] = 0;
		$arProductsInBasket = array();
		$DISCOUNT_PRICE_ALL = 0;
		CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID(), SITE_ID);
		$dbBasketItems = CSaleBasket::GetList(
				array("ID" => "ASC"),
				array(
						"FUSER_ID" => CSaleBasket::GetBasketUserID(),
						"LID" => SITE_ID,
						"ORDER_ID" => "NULL"
					),
				false,
				false,
				array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "NAME", "DISCOUNT_PRICE", "VAT_RATE")
			);
		while ($arBasketItems = $dbBasketItems->GetNext())
		{
			if ($arBasketItems["DELAY"] == "N" && $arBasketItems["CAN_BUY"] == "Y")
			{
				$arBasketItems["PRICE"] = roundEx($arBasketItems["PRICE"], SALE_VALUE_PRECISION);
				$arBasketItems["QUANTITY"] = DoubleVal($arBasketItems["QUANTITY"]);
				$arBasketItems["WEIGHT"] = DoubleVal($arBasketItems["WEIGHT"]);
				$arBasketItems["WEIGHT_FORMATED"] = roundEx(DoubleVal($arBasketItems["WEIGHT"]/$arResult["WEIGHT_KOEF"]), SALE_WEIGHT_PRECISION)." ".$arResult["WEIGHT_UNIT"];
				$arBasketItems["VAT_RATE"] = DoubleVal($arBasketItems["VAT_RATE"]);
				//$arBasketItems["DISCOUNT_PRICE"] = roundEx($arBasketItems["DISCOUNT_PRICE"], SALE_VALUE_PRECISION);

				$DISCOUNT_PRICE_ALL += $arBasketItems["DISCOUNT_PRICE"] * $arBasketItems["QUANTITY"];

				$arResult["ORDER_PRICE"] += $arBasketItems["PRICE"] * $arBasketItems["QUANTITY"];
				$arResult["ORDER_WEIGHT"] += $arBasketItems["WEIGHT"] * $arBasketItems["QUANTITY"];
				if(DoubleVal($arBasketItems["VAT_RATE"]) > 0)
				{

					$arResult["bUsingVat"] = "Y";
					if($arBasketItems["VAT_RATE"] > $arResult["vatRate"])
						$arResult["vatRate"] = $arBasketItems["VAT_RATE"];

					//$arBasketItems["VAT_VALUE"] = roundEx((($arBasketItems["PRICE"] / ($arBasketItems["VAT_RATE"] +1)) * $arBasketItems["VAT_RATE"]), SALE_VALUE_PRECISION);
					$arBasketItems["VAT_VALUE"] = (($arBasketItems["PRICE"] / ($arBasketItems["VAT_RATE"] +1)) * $arBasketItems["VAT_RATE"]);
					$arResult["vatSum"] += roundEx($arBasketItems["VAT_VALUE"] * $arBasketItems["QUANTITY"], SALE_VALUE_PRECISION);
				}
				$arBasketItems["PRICE_FORMATED"] = SaleFormatCurrency($arBasketItems["PRICE"], $arBasketItems["CURRENCY"]);

				$arProductsInBasket[] = $arBasketItems;
				$bProductsInBasket = true;
			}
		}

		if (!$bProductsInBasket)
		{
			LocalRedirect($arParams["PATH_TO_BASKET"]);
			$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_BASKET_EMPTY");
		}

		// DISCOUNT
		$countProdInBaket = count($arProductsInBasket);
		for ($i = 0; $i < $countProdInBaket; $i++)
			$arProductsInBasket[$i]["DISCOUNT_PRICE"] = DoubleVal($arProductsInBasket[$i]["PRICE"]);

		$arMinDiscount = array();
		$allSum = 0;
		foreach ($arProductsInBasket as &$arResultItem)
		{
			$allSum += ($arResultItem["PRICE"] * $arResultItem["QUANTITY"]);
		}
		$dblMinPrice = $allSum;

		$dbDiscount = CSaleDiscount::GetList(
				array("SORT" => "ASC"),
				array(
						"LID" => SITE_ID,
						"ACTIVE" => "Y",
						"!>ACTIVE_FROM" => Date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL"))),
						"!<ACTIVE_TO" => Date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL"))),
						"<=PRICE_FROM" => $arResult["ORDER_PRICE"],
						">=PRICE_TO" => $arResult["ORDER_PRICE"],
						"USER_GROUPS" => $USER->GetUserGroupArray(),
					),
				false,
				false,
				array("*")
			);
		$arResult["DISCOUNT_PRICE"] = 0;
		$arResult["DISCOUNT_PERCENT"] = 0;
		$arDiscounts = array();

		while ($arDiscount = $dbDiscount->Fetch())
		{
			$dblDiscount = 0;
			$allSum_tmp = $allSum;

			if ($arDiscount["DISCOUNT_TYPE"] == "P")
			{
				if($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y")
				{
					foreach ($arProductsInBasket as &$arBasketItem)
					{
						$curDiscount = roundEx($arBasketItem["PRICE"] * $arBasketItem["QUANTITY"] * $arDiscount["DISCOUNT_VALUE"] / 100, SALE_VALUE_PRECISION);
						$dblDiscount += $curDiscount;
					}
				}
				else
				{
					foreach ($arProductsInBasket as &$arBasketItem)
					{
						$curDiscount = roundEx($arBasketItem["PRICE"] * $arDiscount["DISCOUNT_VALUE"] / 100, SALE_VALUE_PRECISION);
						$dblDiscount += $curDiscount * $arBasketItem["QUANTITY"];
					}
				}
			}
			else
			{
				$dblDiscount = roundEx(CCurrencyRates::ConvertCurrency($arDiscount["DISCOUNT_VALUE"], $arDiscount["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION);
			}

			$allSum = $allSum - $dblDiscount;
			if ($dblMinPrice > $allSum)
			{
				$dblMinPrice = $allSum;
				$arMinDiscount = $arDiscount;
			}
			$allSum = $allSum_tmp;
		}

		if (!empty($arMinDiscount))
		{
			if ($arMinDiscount["DISCOUNT_TYPE"] == "P")
			{
				$arResult["DISCOUNT_PERCENT"] = $arMinDiscount["DISCOUNT_VALUE"];
				$countProdBasket = count($arProductsInBasket);
				for ($bi = 0; $bi < $countProdBasket; $bi++)
				{
					if($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y")
					{
						$curDiscount = roundEx($arProductsInBasket[$bi]["PRICE"] * $arProductsInBasket[$bi]["QUANTITY"] * $arMinDiscount["DISCOUNT_VALUE"] / 100, SALE_VALUE_PRECISION);
						$arResult["DISCOUNT_PRICE"] += $curDiscount;
					}
					else
					{
						$curDiscount = roundEx($arProductsInBasket[$bi]["PRICE"] * $arMinDiscount["DISCOUNT_VALUE"] / 100, SALE_VALUE_PRECISION);
						$arResult["DISCOUNT_PRICE"] += $curDiscount * $arProductsInBasket[$bi]["QUANTITY"];
					}
					$arProductsInBasket[$bi]["DISCOUNT_PRICE"] = $arProductsInBasket[$bi]["PRICE"] - $curDiscount;
				}
			}
			else
			{
				$arResult["DISCOUNT_PRICE"] = CCurrencyRates::ConvertCurrency($arMinDiscount["DISCOUNT_VALUE"], $arMinDiscount["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]);
				$arResult["DISCOUNT_PRICE"] = roundEx($arResult["DISCOUNT_PRICE"], SALE_VALUE_PRECISION);
			}
		}

		if ($arResult["ERROR_MESSAGE"] == '' && $arResult["CurrentStep"] > 1)
		{
			// <***************** AFTER 1 STEP
			if ($arResult["PERSON_TYPE"] <= 0)
				$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_NO_PERS_TYPE")."<br />";

			if (($arResult["PERSON_TYPE"] > 0) && !($arPersType = CSalePersonType::GetByID($arResult["PERSON_TYPE"])))
				$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_PERS_TYPE_NOT_FOUND")."<br />";

			if ($arResult["ERROR_MESSAGE"] <> '')
				$arResult["CurrentStep"] = 1;
		}

		if ($arResult["ERROR_MESSAGE"] == '' && $arResult["CurrentStep"] > 2)
		{
			// <***************** AFTER 2 STEP
			if ($arResult["PROFILE_ID"] > 0 && $USER->IsAuthorized())
			{
				$dbUserProps = CSaleOrderUserPropsValue::GetList(
						array("SORT" => "ASC"),
						array("USER_PROPS_ID" => $arResult["PROFILE_ID"]),
						false,
						false,
						array("ID", "ORDER_PROPS_ID", "VALUE", "SORT")
					);
				while ($arUserProps = $dbUserProps->GetNext())
				{
					$arResult["POST"]["ORDER_PROP_".$arUserProps["ORDER_PROPS_ID"]] = $arUserProps["VALUE"];
					$arResult["POST"]["~ORDER_PROP_".$arUserProps["ORDER_PROPS_ID"]] = $arUserProps["~VALUE"];
				}
			}

			$arFilter = array("PERSON_TYPE_ID" => $arResult["PERSON_TYPE"], "ACTIVE" => "Y", "UTIL" => "N");
			if(!empty($arParams["PROP_".$arResult["PERSON_TYPE"]]))
				$arFilter["!ID"] = $arParams["PROP_".$arResult["PERSON_TYPE"]];

			$dbOrderProps = CSaleOrderProps::GetList(
					array("SORT" => "ASC"),
					$arFilter,
					false,
					false,
					array("ID", "NAME", "TYPE", "IS_LOCATION", "IS_LOCATION4TAX", "IS_PROFILE_NAME", "IS_PAYER", "IS_EMAIL", "IS_ZIP", "REQUIED", "SORT")
				);
			while ($arOrderProps = $dbOrderProps->GetNext())
			{
				$bErrorField = False;
				$curVal = $arResult["POST"]["~ORDER_PROP_".$arOrderProps["ID"]];

				if ($arOrderProps["TYPE"]=="LOCATION")
				{
					if (isset($arResult["POST"]["NEW_LOCATION_".$arOrderProps["ID"]]) && intval($arResult["POST"]["NEW_LOCATION_".$arOrderProps["ID"]]) > 0)
					{
						$curVal = intval($arResult["POST"]["NEW_LOCATION_".$arOrderProps["ID"]]);
						$arResult["POST"]["ORDER_PROP_".$arOrderProps["ID"]] = $curVal;
					}
				}
				if ($arOrderProps["TYPE"]=="LOCATION" && ($arOrderProps["IS_LOCATION"]=="Y" || $arOrderProps["IS_LOCATION4TAX"]=="Y"))
				{
					if ($arOrderProps["IS_LOCATION"]=="Y")
						$arResult["DELIVERY_LOCATION"] = intval($curVal);
					if ($arOrderProps["IS_LOCATION4TAX"]=="Y")
						$arResult["TAX_LOCATION"] = intval($curVal);

					if (intval($curVal)<=0) $bErrorField = True;
				}
				elseif ($arOrderProps["IS_PROFILE_NAME"]=="Y" || $arOrderProps["IS_PAYER"]=="Y" || $arOrderProps["IS_EMAIL"]=="Y" || $arOrderProps["IS_ZIP"]=="Y")
				{
					if ($arOrderProps["IS_PROFILE_NAME"]=="Y")
					{
						$arResult["PROFILE_NAME"] = Trim($curVal);
						if ($arResult["PROFILE_NAME"] == '')
							$bErrorField = True;
					}
					if ($arOrderProps["IS_PAYER"]=="Y")
					{
						$arResult["PAYER_NAME"] = Trim($curVal);
						if ($arResult["PAYER_NAME"] == '')
							$bErrorField = True;
					}
					if ($arOrderProps["IS_EMAIL"]=="Y")
					{
						$arResult["USER_EMAIL"] = Trim($curVal);
						if ($arResult["USER_EMAIL"] == '' || !check_email($arResult["USER_EMAIL"]))
							$bErrorField = True;
					}
					if($arOrderProps["IS_ZIP"]=="Y")
					{
						$arResult["DELIVERY_LOCATION_ZIP"] = $curVal;
						if ($arResult["DELIVERY_LOCATION_ZIP"] == '')
							$bErrorField = True;
					}
				}
				elseif ($arOrderProps["REQUIED"]=="Y")
				{
					if ($arOrderProps["TYPE"]=="TEXT" || $arOrderProps["TYPE"]=="TEXTAREA" || $arOrderProps["TYPE"]=="RADIO" || $arOrderProps["TYPE"]=="SELECT" || $arOrderProps["TYPE"] == "CHECKBOX")
					{
						if ($curVal == '')
							$bErrorField = True;
					}
					elseif ($arOrderProps["TYPE"]=="LOCATION")
					{
						if (intval($curVal)<=0)
							$bErrorField = True;
					}
					elseif ($arOrderProps["TYPE"]=="MULTISELECT")
					{
						if (!is_array($curVal) || count($curVal)<=0)
							$bErrorField = True;
					}
				}

				if ($bErrorField)
					$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_EMPTY_FIELD")." \"".$arOrderProps["NAME"]."\".<br />";
			}


			if ($arResult["ERROR_MESSAGE"] <> '')
				$arResult["CurrentStep"] = 2;
		}

		if ($arResult["ERROR_MESSAGE"] == '' && $arResult["CurrentStep"] > 3)
		{
			// <***************** AFTER 3 STEP
			$arResult["TaxExempt"] = array();
			$arUserGroups = $USER->GetUserGroupArray();

			if($arResult["bUsingVat"] != "Y")
			{
				$dbTaxExemptList = CSaleTax::GetExemptList(array("GROUP_ID" => $arUserGroups));
				while ($TaxExemptList = $dbTaxExemptList->Fetch())
				{
					if (!in_array(intval($TaxExemptList["TAX_ID"]), $arResult["TaxExempt"]))
					{
						$arResult["TaxExempt"][] = intval($TaxExemptList["TAX_ID"]);
					}
				}
			}

			// DELIVERY

			$arResult["DELIVERY_PRICE"] = 0;

			if (is_array($arResult["DELIVERY_ID"]))
			{
				$locFrom = COption::GetOptionString('sale', 'location');

				$arOrder = array(
					"PRICE" => $arResult["ORDER_PRICE"],
					"WEIGHT" => $arResult["ORDER_WEIGHT"],
					"LOCATION_FROM" => $locFrom,
					"LOCATION_TO" => $arResult["DELIVERY_LOCATION"],
					"LOCATION_ZIP" => $arResult["DELIVERY_LOCATION_ZIP"],
				);

				$arDeliveryPrice = CSaleDeliveryHandler::CalculateFull($arResult["DELIVERY_ID"][0], $arResult["DELIVERY_ID"][1], $arOrder, $arResult["BASE_LANG_CURRENCY"]);

				if ($arDeliveryPrice["RESULT"] == "ERROR")
					$arResult["ERROR_MESSAGE"] = $arDeliveryPrice["TEXT"];
				else
					$arResult["DELIVERY_PRICE"] = roundEx($arDeliveryPrice["VALUE"], SALE_VALUE_PRECISION);
			}
			else
			{
				if (($arResult["DELIVERY_ID"] > 0) && !($arDeliv = CSaleDelivery::GetByID($arResult["DELIVERY_ID"])))
					$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_DELIVERY_NOT_FOUND")."<br />";
				elseif (($arResult["DELIVERY_ID"] > 0) && $arDeliv)
					$arResult["DELIVERY_PRICE"] = roundEx(CCurrencyRates::ConvertCurrency($arDeliv["PRICE"], $arDeliv["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION);
			}

			if ($arResult["ERROR_MESSAGE"] <> '')
				$arResult["CurrentStep"] = 3;
		}

		// TAX
		$arResult["TAX_EXEMPT"] = (($_REQUEST["TAX_EXEMPT"]=="Y") ? "Y" : "N");
		if ($arResult["TAX_EXEMPT"] == "N")
		{
			unset($arResult["TaxExempt"]);
			$arResult["TaxExempt"] = array();
		}


		$arResult["TAX_PRICE"] = 0;
		$arResult["arTaxList"] = array();
		if($arResult["bUsingVat"] != "Y")
		{
			$dbTaxRate = CSaleTaxRate::GetList(
					array("APPLY_ORDER"=>"ASC"),
					array(
							"LID" => SITE_ID,
							"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"],
							"ACTIVE" => "Y",
							"LOCATION" => intval($arResult["TAX_LOCATION"])
						)
				);
			while ($arTaxRate = $dbTaxRate->GetNext())
			{
				if (!in_array(intval($arTaxRate["TAX_ID"]), $arResult["TaxExempt"]))
				{
					$arResult["arTaxList"][] = $arTaxRate;
				}
			}

			$arTaxSums = array();
			if (count($arResult["arTaxList"]) > 0)
			{
				$countProdBasket = count($arProductsInBasket);
				for ($i = 0; $i < $countProdBasket; $i++)
				{
					$arResult["TAX_PRICE_tmp"] = CSaleOrderTax::CountTaxes(
							$arProductsInBasket[$i]["DISCOUNT_PRICE"] * $arProductsInBasket[$i]["QUANTITY"],
							$arResult["arTaxList"],
							$arResult["BASE_LANG_CURRENCY"]
						);

					$countResultTax = count($arResult["arTaxList"]);
					for ($j = 0; $j < $countResultTax; $j++)
					{
						$arResult["arTaxList"][$j]["VALUE_MONEY"] += $arResult["arTaxList"][$j]["TAX_VAL"];
					}
				}
				if(DoubleVal($arResult["DELIVERY_PRICE"])>0 && $arParams["COUNT_DELIVERY_TAX"] == "Y")
				{
					$arResult["TAX_PRICE_tmp"] = CSaleOrderTax::CountTaxes(
							$arResult["DELIVERY_PRICE"],
							$arResult["arTaxList"],
							$arResult["BASE_LANG_CURRENCY"]
						);

					$countResTax = count($arResult["arTaxList"]);
					for ($j = 0; $j < $countResTax; $j++)
					{
						$arResult["arTaxList"][$j]["VALUE_MONEY"] += $arResult["arTaxList"][$j]["TAX_VAL"];
					}
				}

				$countResultTax = count($arResult["arTaxList"]);
				for ($i = 0; $i < $countResultTax; $i++)
				{
					$arTaxSums[$arResult["arTaxList"][$i]["TAX_ID"]]["VALUE"] = $arResult["arTaxList"][$i]["VALUE_MONEY"];
					$arTaxSums[$arResult["arTaxList"][$i]["TAX_ID"]]["NAME"] = $arResult["arTaxList"][$i]["NAME"];
					if ($arResult["arTaxList"][$i]["IS_IN_PRICE"] != "Y")
					{
						$arResult["TAX_PRICE"] += $arResult["arTaxList"][$i]["VALUE_MONEY"];
					}
				}
			}
		}
		else
		{
			if(DoubleVal($arResult["DELIVERY_PRICE"])>0 && $arParams["COUNT_DELIVERY_TAX"] == "Y")
				$arResult["vatSum"] += roundEx($arResult["DELIVERY_PRICE"] * $arResult["vatRate"] / (1 + $arResult["vatRate"]), 2);

			$arResult["arTaxList"][] = Array(
						"NAME" => GetMessage("STOF_VAT"),
						"IS_PERCENT" => "Y",
						"VALUE" => $arResult["vatRate"]*100,
						"VALUE_MONEY" => $arResult["vatSum"],
						"APPLY_ORDER" => 100,
						"IS_IN_PRICE" => "Y",
						"CODE" => "VAT"
			);

		}

		if ($arResult["ERROR_MESSAGE"] == '' && $arResult["CurrentStep"] >= 4)
		{
			// <***************** AFTER 4 STEP
			// PAY_SYSTEM
			if($arResult["CurrentStep"] > 4)
			{
				$arResult["PAY_SYSTEM_ID"] = intval($_REQUEST["PAY_SYSTEM_ID"]);
				if ($arResult["PAY_SYSTEM_ID"] <= 0)
					$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_NO_PAY_SYS")."<br />";
				if (($arResult["PAY_SYSTEM_ID"] > 0) && !($arPaySys = CSalePaySystem::GetByID($arResult["PAY_SYSTEM_ID"], $arResult["PERSON_TYPE"])))
					$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_PAY_SYS_NOT_FOUND")."<br />";
			}
			//if ($arResult["PAY_CURRENT_ACCOUNT"] != "Y")
				//$arResult["PAY_CURRENT_ACCOUNT"] = "N";

			if ($arResult["ERROR_MESSAGE"] <> '')
				$arResult["CurrentStep"] = 4;
		}

		if ($arResult["ERROR_MESSAGE"] == '' && $arResult["CurrentStep"] > 5)
		{

			if ($arResult["ERROR_MESSAGE"] <> '')
				$arResult["CurrentStep"] = 5;

			if ($arResult["ERROR_MESSAGE"] == '')
			{
				$totalOrderPrice = $arResult["ORDER_PRICE"] + $arResult["DELIVERY_PRICE"] + $arResult["TAX_PRICE"] - $arResult["DISCOUNT_PRICE"];

				$arFields = array(
						"LID" => SITE_ID,
						"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"],
						"PAYED" => "N",
						"CANCELED" => "N",
						"STATUS_ID" => "N",
						"PRICE" => $totalOrderPrice,
						"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
						"USER_ID" => intval($USER->GetID()),
						"PAY_SYSTEM_ID" => $arResult["PAY_SYSTEM_ID"],
						"DELIVERY_ID" => is_array($arResult["DELIVERY_ID"]) ? implode(":", $arResult["DELIVERY_ID"]) : ($arResult["DELIVERY_ID"] > 0 ? $arResult["DELIVERY_ID"] : false),
						"DISCOUNT_VALUE" => $arResult["DISCOUNT_PRICE"],
						"TAX_VALUE" => $arResult["bUsingVat"] == "Y" ? $arResult["vatSum"] : $arResult["TAX_PRICE"],
						"USER_DESCRIPTION" => $arResult["ORDER_DESCRIPTION"]
					);

				// add Guest ID
				if (CModule::IncludeModule("statistic"))
					$arFields["STAT_GID"] = CStatistic::GetEventParam();

				$affiliateID = CSaleAffiliate::GetAffiliate();
				if ($affiliateID > 0)
				{
					$dbAffiliat = CSaleAffiliate::GetList(array(), array("SITE_ID" => SITE_ID, "ID" => $affiliateID));
					$arAffiliates = $dbAffiliat->Fetch();
					if (count($arAffiliates) > 1)
						$arFields["AFFILIATE_ID"] = $affiliateID;
				}
				else
					$arFields["AFFILIATE_ID"] = false;

				$isPayFromUserBudget = false;
				$isPayFullFromUserBudget = false;

				if ($arResult["PAY_CURRENT_ACCOUNT"] == "Y" && $arParams["ALLOW_PAY_FROM_ACCOUNT"] == "Y")
				{
					$userAccountRes = CSaleUserAccount::GetList(
						array(),
						array(
							"USER_ID" => $USER->GetID(),
							"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
						),
						false,
						false,
						array("CURRENT_BUDGET")
					);
					if ($userAccount = $userAccountRes->GetNext())
					{
						if ($userAccount["CURRENT_BUDGET"] > 0)
						{
							$isPayFromUserBudget = (($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && DoubleVal($userAccount["CURRENT_BUDGET"]) >= DoubleVal($totalOrderPrice)) || $arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] != "Y");

							if ($isPayFromUserBudget)
								$isPayFullFromUserBudget = (($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && DoubleVal($arResult["USER_ACCOUNT"]["CURRENT_BUDGET"]) >= DoubleVal($orderTotalSum)));


							if ($isPayFromUserBudget)
								$arFields['ONLY_FULL_PAY_FROM_ACCOUNT'] = $isPayFullFromUserBudget;
						}
					}
				}

				\Bitrix\Sale\Notify::setNotifyDisable(true);

				$arResult["ORDER_ID"] = CSaleOrder::Add($arFields);
				$arResult["ORDER_ID"] = intval($arResult["ORDER_ID"]);

				if ($arResult["ORDER_ID"] <= 0)
				{
					if($ex = $APPLICATION->GetException())
						$arResult["ERROR_MESSAGE"] .= $ex->GetString();
					else
						$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_ERROR_ADD_ORDER")."<br />";
				}
				else
				{
					$arOrder = CSaleOrder::GetByID($arResult["ORDER_ID"]);
				}
			}

			if ($arResult["ERROR_MESSAGE"] == '')
			{
				CSaleBasket::OrderBasket($arResult["ORDER_ID"], CSaleBasket::GetBasketUserID(), SITE_ID, false);

				$dbBasketItems = CSaleBasket::GetList(
						array("ID" => "ASC"),
						array(
								"FUSER_ID" => CSaleBasket::GetBasketUserID(),
								"LID" => SITE_ID,
								"ORDER_ID" => $arResult["ORDER_ID"]
							),
						false,
						false,
						array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "NAME")
					);
				$arResult["ORDER_PRICE"] = 0;
				while ($arBasketItems = $dbBasketItems->GetNext())
				{
					$arResult["ORDER_PRICE"] += DoubleVal($arBasketItems["PRICE"]) * DoubleVal($arBasketItems["QUANTITY"]);
				}

				$totalOrderPrice = $arResult["ORDER_PRICE"] + $arResult["DELIVERY_PRICE"] + $arResult["TAX_PRICE"] - $arResult["DISCOUNT_PRICE"];
				CSaleOrder::Update($arResult["ORDER_ID"], Array("PRICE" => $totalOrderPrice));
			}

			if ($arResult["ERROR_MESSAGE"] == '')
			{
				//if($arResult["bUsingVat"] != "Y")
				//{
					$countResultTax = count($arResult["arTaxList"]);
					for ($i = 0; $i < $countResultTax; $i++)
					{
						$arFields = array(
								"ORDER_ID" => $arResult["ORDER_ID"],
								"TAX_NAME" => $arResult["arTaxList"][$i]["NAME"],
								"IS_PERCENT" => $arResult["arTaxList"][$i]["IS_PERCENT"],
								"VALUE" => ($arResult["arTaxList"][$i]["IS_PERCENT"]=="Y") ? $arResult["arTaxList"][$i]["VALUE"] : RoundEx(CCurrencyRates::ConvertCurrency($arResult["arTaxList"][$i]["VALUE"], $arResult["arTaxList"][$i]["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION),
								"VALUE_MONEY" => $arResult["arTaxList"][$i]["VALUE_MONEY"],
								"APPLY_ORDER" => $arResult["arTaxList"][$i]["APPLY_ORDER"],
								"IS_IN_PRICE" => $arResult["arTaxList"][$i]["IS_IN_PRICE"],
								"CODE" => $arResult["arTaxList"][$i]["CODE"]
							);
						CSaleOrderTax::Add($arFields);
					}
				//}
				/*
				elseif($arResult["vatRate"] > 0)
				{
					$arFields = array(
							"ORDER_ID" => $arResult["ORDER_ID"],
							"TAX_NAME" => GetMessage("STOF_VAT"),
							"IS_PERCENT" => "Y",
							"VALUE" => $arResult["vatRate"],
							"VALUE_MONEY" => $arResult["vatSum"],
							"APPLY_ORDER" => 100,
							"IS_IN_PRICE" => "Y",
							"CODE" => "VAT"
						);
					CSaleOrderTax::Add($arFields);

				}
				*/
				$arFilter = array("PERSON_TYPE_ID" => $arResult["PERSON_TYPE"], "ACTIVE" => "Y", "UTIL" => "N");
				if(!empty($arParams["PROP_".$arResult["PERSON_TYPE"]]))
					$arFilter["!ID"] = $arParams["PROP_".$arResult["PERSON_TYPE"]];

				$dbOrderProperties = CSaleOrderProps::GetList(
						array("SORT" => "ASC"),
						$arFilter,
						false,
						false,
						array("ID", "TYPE", "NAME", "CODE", "USER_PROPS", "SORT")
					);
				while ($arOrderProperties = $dbOrderProperties->Fetch())
				{
					$curVal = $arResult["POST"]["~ORDER_PROP_".$arOrderProperties["ID"]];
					if ($arOrderProperties["TYPE"] == "MULTISELECT")
					{
						$curVal = "";
						$countResProp = count($arResult["POST"]["~ORDER_PROP_".$arOrderProperties["ID"]]);
						for ($i = 0; $i < $countResProp; $i++)
						{
							if ($i > 0)
								$curVal .= ",";
							$curVal .= $arResult["POST"]["~ORDER_PROP_".$arOrderProperties["ID"]][$i];
						}
					}

					if ($arOrderProperties["TYPE"] == "CHECKBOX" && $curVal == '' && $arOrderProperties["REQUIED"] != "Y")
					{
						$curVal = "N";
					}

					if ($curVal <> '')
					{
						$arFields = array(
								"ORDER_ID" => $arResult["ORDER_ID"],
								"ORDER_PROPS_ID" => $arOrderProperties["ID"],
								"NAME" => $arOrderProperties["NAME"],
								"CODE" => $arOrderProperties["CODE"],
								"VALUE" => $curVal
							);
						CSaleOrderPropsValue::Add($arFields);
						if ( $arOrderProperties["USER_PROPS"] == "Y" && intval($arResult["PROFILE_ID"]) <= 0 && intval($arResult["USER_PROPS_ID"])<=0)
						{
							if ($arResult["PROFILE_NAME"] == '')
								$arResult["PROFILE_NAME"] = GetMessage("SALE_PROFILE_NAME")." ".Date("Y-m-d");

							$arFields = array(
									"NAME" => $arResult["PROFILE_NAME"],
									"USER_ID" => intval($USER->GetID()),
									"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"]
								);
							$arResult["USER_PROPS_ID"] = CSaleOrderUserProps::Add($arFields);
							$arResult["USER_PROPS_ID"] = intval($arResult["USER_PROPS_ID"]);
						}

						if (intval($arResult["PROFILE_ID"]) <= 0 && $arOrderProperties["USER_PROPS"] == "Y" && $arResult["USER_PROPS_ID"] > 0)
						{
							$arFields = array(
									"USER_PROPS_ID" => $arResult["USER_PROPS_ID"],
									"ORDER_PROPS_ID" => $arOrderProperties["ID"],
									"NAME" => $arOrderProperties["NAME"],
									"VALUE" => $curVal
								);
							CSaleOrderUserPropsValue::Add($arFields);
						}
					}
				}
			}

			// mail message
			if ($arResult["ERROR_MESSAGE"] == '')
			{
				$strOrderList = "";
				$dbBasketItems = CSaleBasket::GetList(
						array("ID" => "ASC"),
						array("ORDER_ID" => $arResult["ORDER_ID"]),
						false,
						false,
						array("ID", "NAME", "QUANTITY")
					);
				while ($arBasketItems = $dbBasketItems->Fetch())
				{
					$strOrderList .= $arBasketItems["NAME"]." - ".$arBasketItems["QUANTITY"]." ".GetMessage("SALE_QUANTITY_UNIT");
					$strOrderList .= "\n";
				}

				$arFields = Array(
					"ORDER_ID" => $arOrder["ACCOUNT_NUMBER"],
					"ORDER_DATE" => Date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT", SITE_ID))),
					"ORDER_USER" => ( ($arResult["PAYER_NAME"] <> '') ? $arResult["PAYER_NAME"] : $USER->GetFormattedName(false) ),
					"PRICE" => SaleFormatCurrency($totalOrderPrice, $arResult["BASE_LANG_CURRENCY"]),
					"BCC" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
					"EMAIL" => $arResult["USER_EMAIL"],
					"ORDER_LIST" => $strOrderList,
					"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME)
				);

				$eventName = "SALE_NEW_ORDER";

				$bSend = true;
				foreach(GetModuleEvents("sale", "OnOrderNewSendEmail", true) as $arEvent)
					if (ExecuteModuleEventEx($arEvent, Array($arResult["ORDER_ID"], &$eventName, &$arFields))===false)
						$bSend = false;

				if($bSend)
				{
					$event = new CEvent;
					$event->Send($eventName, SITE_ID, $arFields, "N");
				}

				CSaleMobileOrderPush::send("ORDER_CREATED", array("ORDER_ID" => $arFields["ORDER_ID"]));
			}

			\Bitrix\Sale\Notify::setNotifyDisable(false);
			if ($arResult["ERROR_MESSAGE"] == '')
			{
				LocalRedirect($arParams["PATH_TO_ORDER"]."?CurrentStep=7&ORDER_ID=".urlencode(urlencode($arOrder["ACCOUNT_NUMBER"])));
			}

			if ($arResult["ERROR_MESSAGE"] <> '')
				$arResult["CurrentStep"] = 5;
		}
	}
}