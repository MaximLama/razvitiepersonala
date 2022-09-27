<?
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2014 Bitrix
 */

/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponentTemplate $this
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$this->addExternalJs($this->GetFolder().'/registration_control.js');

?>
<?if($USER->IsAuthorized()):

    LocalRedirect('/personal/');

else:?>
    <?
    if (count($arResult["ERRORS"]) > 0):
        foreach ($arResult["ERRORS"] as $key => $error)
            if (intval($key) == 0 && $key !== 0) 
                $arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;".GetMessage("REGISTER_FIELD_".$key)."&quot;", $error);

            ShowError(implode("<br />", $arResult["ERRORS"]));

    endif?>
<section class="login login--reg">
    <div class="container">
        <div class="login__content-bg">
            <a href="<?=$_SERVER['HTTP_REFERER']?>" class="login__forgot login__back-link">
                <span class="desktop">← Вернуться назад</span>
                <span class="mobile">← Назад</span>
            </a>
				<form method="post" action="javascript:void(0)" name="regform" enctype="multipart/form-data" id="register_form">
					<?
					if($arResult["BACKURL"] <> ''):
					?>
						<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
					<?
					endif;
					?>
					<div class="login__logo">
                        <img src="<?=SITE_TEMPLATE_PATH?>/img/logo.svg" alt="">
                    </div>
                    <h1 class="login__title">Вход в личный кабинет</h1>

                    <label class="login__label">
                        <div class="login__label-name">Ваше имя</div>
                        <input size="30" type="text" name="REGISTER[NAME]" value="<?=$arResult["VALUES"]["NAME"]?>" class="login__input"/>
                    </label>
                    <label class="login__label">
                        <div class="login__label-name">E-mail</div>
                        <input size="30" type="email" name="REGISTER[EMAIL]" value="<?=$arResult["VALUES"]["EMAIL"]?>" class="login__input"/>
                    </label>
                    <label class="login__label">
                        <div class="login__label-name">Логин</div>
                        <input size="30" type="text" name="REGISTER[LOGIN]" value="<?=$arResult["VALUES"]["LOGIN"]?>" class="login__input"/>
                    </label>
                    <label class="login__label">
                        <div class="login__label-name">Пароль</div>
                        <input size="30" type="password" name="REGISTER[PASSWORD]" value="<?=$arResult["VALUES"]["PASSWORD"]?>" autocomplete="off" class="login__input">
                    </label>
                    <label class="login__label">
                        <div class="login__label-name">Повторите пароль</div>
                        <input size="30" type="password" name="REGISTER[CONFIRM_PASSWORD]" value="<?=$arResult["VALUES"]["CONFIRM_PASSWORD"]?>" autocomplete="off" class="login__input">
                    </label>
                    <input type="hidden" name="register_submit_button" value="Регистрация">
                    <div class="login__confirm-block calculate__confirm-block">
                        <button class="calculate__btn">
                            <span>Зарегистрироваться</span>
                            <svg viewBox="0 0 26 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M24.5 7.5H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1C18.5 1 19 3.36364 20.5 5C22.1874 6.84075 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14C18.5 14 19 11.6364 20.5 10C22.1874 8.15925 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </div>
                    <a href="/auth/" class="login__forgot">Уже есть учетная запись? Войти</a>
				</form>

		</div>
    </div>
</section>
<section class="notice" id="notice">
    <button type="button" class="notice__close close" id="close"></button>
    <div class="notice__icon">
        <img src="<?=SITE_TEMPLATE_PATH?>/img/notice-icon.png" alt="">
    </div>
    <div class="notice__text-block">
        <div class="notice__title">Уведомление</div>
        <div class="notice__text">
            E-mail адрес введен неправильно
        </div>
    </div>
</section>
<script>
	BX.RegistrationForm.init({
		formID: 'register_form',
		buttonClass: 'calculate__btn'
	});
</script>
<?endif?>