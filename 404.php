<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus('404 Not Found');
@define('ERROR_404', 'Y');

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Страница не найдена");
use Bitrix\Main\Localization\Loc;?>

Данная страница не существует

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
