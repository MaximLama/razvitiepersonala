<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)
{
	die();
}

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

if($arResult["SUCCESS"]){
    $USER->Login($arResult["LAST_LOGIN"], $arResult["USER_PASSWORD"]);

    LocalRedirect('/personal/');
}

if ($arResult['AUTHORIZED'])
{
	echo Loc::getMessage('MAIN_AUTH_CHD_SUCCESS');
	return;
}

$fields = $arResult['FIELDS'];
?>

<section class="login login--new-pass">
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
            <form class="login__content" name="bform" method="post" target="_top" action="<?= POST_FORM_ACTION_URI;?>">
                <div class="login__logo">
                    <img src="<?=SITE_TEMPLATE_PATH?>/img/logo.svg" alt="">
                </div>
                <h1 class="login__title">Придумайте пароль</h1>
                <label class="login__label">
                    <div class="login__label-name">Логин или E-mail</div>
                    <input type="text" class="login__input" name="<?= $fields['login'];?>" maxlength="255" value="<?= \htmlspecialcharsbx($arResult['LAST_LOGIN']);?>">
                </label>
                <input type="hidden" name="<?= $fields['checkword'];?>" maxlength="255" value="<?= \htmlspecialcharsbx($arResult[$fields['checkword']]);?>" />
                <label class="login__label">
                    <div class="login__label-name">Новый пароль</div>
                    <input type="password" class="login__input" name="<?= $fields['password'];?>" value="<?= \htmlspecialcharsbx($arResult[$fields['password']]);?>" maxlength="255" autocomplete="off">
                </label>
                <label class="login__label">
                    <div class="login__label-name">Подтвердите пароль</div>
                    <input type="password" class="login__input" name="<?= $fields['confirm_password'];?>" value="<?= \htmlspecialcharsbx($arResult[$fields['confirm_password']]);?>" maxlength="255" autocomplete="off" />
                </label>
                <div class="login__confirm-block login__confirm-block--center calculate__confirm-block">
                    <button type="submit" class="calculate__btn" name="<?= $fields['action'];?>" value="<?= Loc::getMessage('MAIN_AUTH_CHD_FIELD_SUBMIT');?>">
                        <span>Изменить и войти</span>
                        <svg viewBox="0 0 26 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M24.5 7.5H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1C18.5 1 19 3.36364 20.5 5C22.1874 6.84075 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14C18.5 14 19 11.6364 20.5 10C22.1874 8.15925 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
