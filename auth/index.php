<?
define("AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?
if(isset($_REQUEST['change_password'])&&$_REQUEST['change_password']==="yes"){
    $APPLICATION->SetTitle("Смена пароля");
    $APPLICATION->IncludeComponent(
    	"bitrix:main.auth.changepasswd",
    	"change_password",
    	Array(
    		"AUTH_AUTH_URL" => "/auth/",
    		"AUTH_REGISTER_URL" => "/auth/registration.php"
    	)
    );
}
elseif(isset($_REQUEST['forgotpasswd'])&&$_REQUEST["forgotpasswd"]==="yes"){
    $APPLICATION->SetTitle("Восстановление пароля");
    $APPLICATION->IncludeComponent(
        "bitrix:main.auth.forgotpasswd",
        "forgot",
        Array(
            "AUTH_AUTH_URL" => "/auth/",
            "AUTH_REGISTER_URL" => "/auth/registration.php"
        )
    );
}elseif(isset($_REQUEST['confirm_registration'])&&$_REQUEST["confirm_registration"]==="yes"){
    $APPLICATION->SetTitle("Подтверждение регистрации");
    $APPLICATION->IncludeComponent(
        "bitrix:system.auth.confirmation",
        "confirmation",
        Array(
            "CONFIRM_CODE" => "confirm_code",
            "LOGIN" => "login",
            "USER_ID" => "confirm_user_id"
        )
    );
}
else{
    $APPLICATION->SetTitle("Авторизация");
    $APPLICATION->IncludeComponent(
    	"bitrix:main.auth.form",
    	"auth",
    	Array(
    		"AUTH_FORGOT_PASSWORD_URL" => "/auth/?forgotpasswd=yes",
    		"AUTH_REGISTER_URL" => "/auth/registration.php",
    		//"AUTH_SUCCESS_URL" => "/personal/"
    	)
    );
}?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>