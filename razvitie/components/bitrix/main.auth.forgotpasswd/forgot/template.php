<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)
{
	die();
}

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

function handle($data){
	return htmlspecialchars(stripslashes(trim($data)));
}


if ($arResult['AUTHORIZED'])
{
	LocalRedirect('/personal/');
	return;
}
if(!$arResult['SUCCESS']){
	$formStyle = 'style="display:block;"';
	$completeStyle = 'style="display:none;"';
}
else{
	$completeStyle = 'style="display:block;"';
	$formStyle = 'style="display:none;"';
	$filter = [];
	if(isset($_REQUEST['USER_LOGIN'])){
		$filter = [
			"LOGIN" => handle($_REQUEST['USER_LOGIN'])
		];
	}
	elseif(isset($_REQUEST['USER_EMAIL'])){
		$filter = [
			"EMAIL" => handle($_REQUEST['USER_EMAIL'])
		];
	}
	$rsUser = $USER->GetList(($by="id"), ($order="asc"), $filter);
	$arUser = $rsUser->fetch();
	$email = $arUser["EMAIL"];
}
?>

<section class="login login--restore">
    <div class="container">
    	<?if ($arResult['ERRORS']):?>
		<div class="alert alert-danger">
			<? foreach ($arResult['ERRORS'] as $error)
			{
				echo $error;
			}
			?>
		</div>
		<?endif;?>
        <div class="login__content-bg">
            <a href="<?=$_SERVER['HTTP_REFERER']?>" class="login__forgot login__back-link">
                <span class="desktop">← Вернуться назад</span>
                <span class="mobile">← Назад</span>
            </a>
            <form class="login__content" name="bform" method="post" target="_top" action="<?= POST_FORM_ACTION_URI;?>" <?=$formStyle?>>

                <div class="login__logo">
                    <img src="<?=SITE_TEMPLATE_PATH?>/img/logo.svg" alt="">
                </div>
                <h1 class="login__title">Восстановление пароля</h1>
                <div class="login__sub-text">
                    Введите название вашей учетной записи или адрес электронной почты
                </div>
                <label class="login__label">
                    <div class="login__label-name">Логин</div>
                    <input type="text" class="login__input" name="<?= $arResult['FIELDS']['login'];?>" maxlength="255" value="<?= \htmlspecialcharsbx($arResult['LAST_LOGIN']);?>">
                </label>
                <div class="login__label-name">ИЛИ</div>
                <label class="login__label">
                    <div class="login__label-name">E-mail</div>
                    <input type="text" class="login__input" name="<?= $arResult['FIELDS']['email'];?>" maxlength="255" value="">
                </label>
                <div class="login__confirm-block login__confirm-block--center calculate__confirm-block">
                    <button type="submit" class="calculate__btn complete-btn" name="<?= $arResult['FIELDS']['action'];?>" value="<?= Loc::getMessage('MAIN_AUTH_PWD_FIELD_SUBMIT');?>">
                        <span>Восстановить</span>
                        <svg viewBox="0 0 26 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M24.5 7.5H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1C18.5 1 19 3.36364 20.5 5C22.1874 6.84075 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14C18.5 14 19 11.6364 20.5 10C22.1874 8.15925 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
                <a href="<?=$arParams['AUTH_AUTH_URL']?>" class="login__forgot">Войти</a>
            </form>
            <div class="login__complete complete" <?=$completeStyle?>>
                <div class="login__logo">
                    <img src="<?=SITE_TEMPLATE_PATH?>/img/logo.svg" alt="">
                </div>
                <div class="complete__title">Восстановление пароля</div>
                <div class="complete__text">
                    <div>
                        Письмо с инструкциями по смене пароля было отправлено на почту <span class="complete__text--bold"><?=$email?></span>
                    </div>
                </div>
                <a href="<?=$arParams['AUTH_AUTH_URL']?>" class="calculate__btn">
                    <span>Войти</span>
                    <svg viewBox="0 0 26 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24.5 7.5H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1C18.5 1 19 3.36364 20.5 5C22.1874 6.84075 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14C18.5 14 19 11.6364 20.5 10C22.1874 8.15925 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
	document.bform.<?= $arResult['FIELDS']['login'];?>.focus();
</script>
