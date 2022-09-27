<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)
{
	die();
}

function handle($data){
    return htmlspecialchars(stripslashes(trim($data)));
}

use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Web\Cookie;
Loc::loadMessages(__FILE__);

if($_SERVER['REQUEST_METHOD']==="POST"&&check_bitrix_sessid()){
    $context = Application::getInstance()->getContext();
    $request = $context->getRequest();
    if(isset($_REQUEST['REMEMBER_LOGIN'])&&$request->get("REMEMBER_LOGIN")==="on"){
        $cookie = new Cookie("LOGIN", handle($_REQUEST['USER_LOGIN']), time() +  60 * 60 * 24 * 60);
    }else{
        $cookie = new Cookie("LOGIN", "", time() + 60 * 60 * 24 * 60);
    }
    $cookie->setDomain( $context->getServer()->getHttpHost());
    $context->getResponse()->addCookie( $cookie );
    $context->getResponse()->writeHeaders("");

    LocalRedirect('/personal');
}

if ($arResult['AUTHORIZED'])
{
	LocalRedirect('/personal');
	return;
}
?>
<section class="login">
    <div class="container">
        <div class="login__content-bg">
            <a href="<?=$_SERVER['HTTP_REFERER']?>" class="login__forgot login__back-link">
                <span class="desktop">← Вернуться назад</span>
                <span class="mobile">← Назад</span>
            </a>
            <form class="login__content" name="<?= $arResult['FORM_ID'];?>" method="post" target="_top" action="<?= POST_FORM_ACTION_URI;?>">
                <?=bitrix_sessid_post()?>
                <div class="login__logo">

					<img src="<?=SITE_TEMPLATE_PATH?>/img/auth_logo.svg">
                </div>
                <h1 class="login__title">Вход в личный кабинет</h1>
                <?if ($arResult['ERRORS']):?>
				<div class="alert alert-danger">
					<? foreach ($arResult['ERRORS'] as $error)
					{
						echo $error;
					}
					?>
				</div>
				<?endif;?>
                <label class="login__label">
                    <div class="login__label-name">Логин</div>
                    <input type="text" class="login__input" name="<?= $arResult['FIELDS']['login'];?>" maxlength="255" value="<?= \htmlspecialcharsbx($arResult['LAST_LOGIN']);?>">
                </label>
                <label class="login__label">
                    <div class="login__label-name">Пароль</div>
                    <input type="password" class="login__input" name="<?= $arResult['FIELDS']['password'];?>" maxlength="255" autocomplete="off">
                </label>
                <input type="hidden" name="<?= $arResult['FIELDS']['action'];?>" value="<?= Loc::getMessage('MAIN_AUTH_FORM_FIELD_SUBMIT');?>">
                <div class="login__confirm-block calculate__confirm-block">
                    <button class="calculate__btn">
                        <span>Войти</span>
                        <svg viewBox="0 0 26 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M24.5 7.5H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1C18.5 1 19 3.36364 20.5 5C22.1874 6.84075 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14C18.5 14 19 11.6364 20.5 10C22.1874 8.15925 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <label class="calculate__label-checkbox label-checkbox check">
                        <input type="checkbox" name="REMEMBER_LOGIN" class="calculate__checkbox" checked>
                        <div class="calculate__checkbox-check">
                            <svg viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 1.1543L6 12.1543L1 7.1543" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span>Запомнить логин</span>
                    </label>
                </div>
                <a href="<?=$arResult['AUTH_FORGOT_PASSWORD_URL']?>" class="login__forgot">Забыли логин или пароль?</a>
                <a href="<?=$arResult['AUTH_REGISTER_URL']?>" class="login__forgot">Регистрация</a>
            </form>
        </div>
    </div>
</section>
<script type="text/javascript">
	<?if ($arResult['LAST_LOGIN'] != ''):?>
	try{document.<?= $arResult['FORM_ID'];?>.USER_PASSWORD.focus();}catch(e){}
	<?else:?>
	try{document.<?= $arResult['FORM_ID'];?>.USER_LOGIN.focus();}catch(e){}
	<?endif?>
</script>