<?
/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$this->addExternalJs($this->GetFolder().'/personal_ajax.js');

$name = $arResult["arUser"]["NAME"]||$arResult["arUser"]["LAST_NAME"]?$arResult["arUser"]["NAME"]." ".$arResult["arUser"]["LAST_NAME"]:"Имя не указано";
$city = $arResult["arUser"]["PERSONAL_CITY"]?$arResult["arUser"]["PERSONAL_CITY"]:"Город проживания не указан";
$email = $arResult["arUser"]["EMAIL"];
$phone = $arResult["arUser"]["PERSONAL_PHONE"]?$arResult["arUser"]["PERSONAL_PHONE"]:"";
$street = $arResult["arUser"]["PERSONAL_STREET"]?$arResult["arUser"]["PERSONAL_STREET"]:"Место проживания не указано";


?>
<div class="personal__block">
    <div class="personal__block-title">Основная информация</div>
    <form class="personal__form" action="javascript:void(0)">
    	<?=bitrix_sessid_post()?>
        <label class="calculate__item calculate__label-input">
            <div class="calculate__input-name">Ваше имя</div>
            <svg viewBox="0 0 13 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6.66216 9C6.56081 8.98985 6.43919 8.98985 6.3277 9C3.91554 8.91883 2 6.94025 2 4.50507C2 2.01917 4.00676 0 6.5 0C8.98311 0 11 2.01917 11 4.50507C10.9899 6.94025 9.07432 8.91883 6.66216 9Z" /><path d="M1.72984 12.2734C-0.576613 13.7765 -0.576613 16.2259 1.72984 17.7196C4.35081 19.4268 8.64919 19.4268 11.2702 17.7196C13.5766 16.2166 13.5766 13.7672 11.2702 12.2734C8.65872 10.5755 4.36034 10.5755 1.72984 12.2734Z" />
            </svg>
            <input type="text" class="calculate__input calculate__input--text" name="NAME" value="<?=$name?>" maxlength="255" disabled required>
        </label>
        <label class="calculate__item calculate__label-input personal__textarea">
            <div class="calculate__input-name">Ваш город</div>
            <svg viewBox="0 0 17 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.89926 19.9718C10.5757 19.1151 16.9583 15.4489 16.9583 9.29165C16.9583 4.62024 13.1714 0.833313 8.49996 0.833313C3.82855 0.833313 0.041626 4.62024 0.041626 9.29165C0.041626 15.4489 6.42426 19.1151 8.10065 19.9718C8.3543 20.1014 8.64561 20.1014 8.89926 19.9718ZM8.49996 12.9166C10.502 12.9166 12.125 11.2937 12.125 9.29165C12.125 7.28961 10.502 5.66665 8.49996 5.66665C6.49793 5.66665 4.87496 7.28961 4.87496 9.29165C4.87496 11.2937 6.49793 12.9166 8.49996 12.9166Z"/>
            </svg>
            <textarea class="calculate__input calculate__input--text" rows="1" name="CITY" maxlength="255" disabled required><?=$city?></textarea>
        </label>
        <div class="personal__buttons">
            <button type="button" class="personal__edit-btn">
                <span>Редактировать</span>
                <svg viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.65019 8.49969L2.35445 14.7933C2.0782 15.0689 1.63062 15.0689 1.35367 14.7933C1.07742 14.5178 1.07742 14.0702 1.35367 13.7947L7.15004 8.00038L1.35437 2.20611C1.07812 1.93055 1.07812 1.48298 1.35437 1.20673C1.63062 0.931171 2.0789 0.931171 2.35515 1.20673L8.65089 7.50037C8.92295 7.77308 8.92295 8.22768 8.65019 8.49969Z" stroke-width="0.5"/>
                </svg>
            </button>
            <button class="calculate__btn">
                <span>Подтвердить</span>
                <svg viewBox="0 0 26 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M24.5 7.5H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1C18.5 1 19 3.36364 20.5 5C22.1874 6.84075 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14C18.5 14 19 11.6364 20.5 10C22.1874 8.15925 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
    </form>
</div>
<div class="personal__block">
    <div class="personal__block-title">Контактная информация</div>
    <form class="personal__form" action="javascript:void(0)">
    	<?=bitrix_sessid_post()?>
        <label class="calculate__item calculate__label-input">
            <div class="calculate__input-name">Ваш E-mail*</div>
            <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18.3333 7.17337V13.9584C18.3333 14.6502 18.0687 15.3157 17.5936 15.8186C17.1185 16.3214 16.469 16.6234 15.7783 16.6625L15.625 16.6667H4.37496C3.68318 16.6668 3.0176 16.4021 2.51477 15.927C2.01193 15.4519 1.70996 14.8024 1.67079 14.1117L1.66663 13.9584V7.17337L9.70996 11.3867C9.79944 11.4336 9.89895 11.4581 9.99996 11.4581C10.101 11.4581 10.2005 11.4336 10.29 11.3867L18.3333 7.17337ZM4.37496 3.33337H15.625C16.2963 3.33329 16.9437 3.58254 17.4417 4.03276C17.9396 4.48299 18.2526 5.10211 18.32 5.77004L9.99996 10.1284L1.67996 5.77004C1.74456 5.12863 2.03583 4.5313 2.50142 4.08543C2.967 3.63955 3.57636 3.37436 4.21996 3.33754L4.37496 3.33337H15.625H4.37496Z"/>
            </svg>
            <input type="text" class="calculate__input calculate__input--text" value="<?=$email?>" name="EMAIL" maxlength="255" disabled>
        </label>
        <label class="calculate__item calculate__label-input">
            <div class="calculate__input-name">Ваш телефон*</div>
            <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.97 16.33C19.97 16.69 19.89 17.06 19.72 17.42C19.55 17.78 19.33 18.12 19.04 18.44C18.55 18.98 18.01 19.37 17.4 19.62C16.8 19.87 16.15 20 15.45 20C14.43 20 13.34 19.76 12.19 19.27C11.04 18.78 9.89 18.12 8.75 17.29C7.6 16.45 6.51 15.52 5.47 14.49C4.44 13.45 3.51 12.36 2.68 11.22C1.86 10.08 1.2 8.94 0.72 7.81C0.24 6.67 0 5.58 0 4.54C0 3.86 0.12 3.21 0.36 2.61C0.6 2 0.98 1.44 1.51 0.94C2.15 0.31 2.85 0 3.59 0C3.87 0 4.15 0.0600001 4.4 0.18C4.66 0.3 4.89 0.48 5.07 0.74L7.39 4.01C7.57 4.26 7.7 4.49 7.79 4.71C7.88 4.92 7.93 5.13 7.93 5.32C7.93 5.56 7.86 5.8 7.72 6.03C7.59 6.26 7.4 6.5 7.16 6.74L6.4 7.53C6.29 7.64 6.24 7.77 6.24 7.93C6.24 8.01 6.25 8.08 6.27 8.16C6.3 8.24 6.33 8.3 6.35 8.36C6.53 8.69 6.84 9.12 7.28 9.64C7.73 10.16 8.21 10.69 8.73 11.22C9.27 11.75 9.79 12.24 10.32 12.69C10.84 13.13 11.27 13.43 11.61 13.61C11.66 13.63 11.72 13.66 11.79 13.69C11.87 13.72 11.95 13.73 12.04 13.73C12.21 13.73 12.34 13.67 12.45 13.56L13.21 12.81C13.46 12.56 13.7 12.37 13.93 12.25C14.16 12.11 14.39 12.04 14.64 12.04C14.83 12.04 15.03 12.08 15.25 12.17C15.47 12.26 15.7 12.39 15.95 12.56L19.26 14.91C19.52 15.09 19.7 15.3 19.81 15.55C19.91 15.8 19.97 16.05 19.97 16.33Z"/>
            </svg>
            <input type="text" class="calculate__input calculate__input--text input-phone" value="<?=$phone?>" name="PHONE" maxlength="255" disabled>
        </label>
        <label class="calculate__item calculate__label-input personal__textarea">
            <div class="calculate__input-name">Ваш адрес</div>
            <svg viewBox="0 0 17 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.89926 19.9718C10.5757 19.1151 16.9583 15.4489 16.9583 9.29165C16.9583 4.62024 13.1714 0.833313 8.49996 0.833313C3.82855 0.833313 0.041626 4.62024 0.041626 9.29165C0.041626 15.4489 6.42426 19.1151 8.10065 19.9718C8.3543 20.1014 8.64561 20.1014 8.89926 19.9718ZM8.49996 12.9166C10.502 12.9166 12.125 11.2937 12.125 9.29165C12.125 7.28961 10.502 5.66665 8.49996 5.66665C6.49793 5.66665 4.87496 7.28961 4.87496 9.29165C4.87496 11.2937 6.49793 12.9166 8.49996 12.9166Z"/>
            </svg>
            <textarea class="calculate__input calculate__input--text" rows="1" maxlength="255" name="STREET" disabled><?=$street?></textarea>
        </label>
        <div class="personal__buttons">
            <button type="button" class="personal__edit-btn">
                <span>Редактировать</span>
                <svg viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.65019 8.49969L2.35445 14.7933C2.0782 15.0689 1.63062 15.0689 1.35367 14.7933C1.07742 14.5178 1.07742 14.0702 1.35367 13.7947L7.15004 8.00038L1.35437 2.20611C1.07812 1.93055 1.07812 1.48298 1.35437 1.20673C1.63062 0.931171 2.0789 0.931171 2.35515 1.20673L8.65089 7.50037C8.92295 7.77308 8.92295 8.22768 8.65019 8.49969Z" stroke-width="0.5"/>
                </svg>
            </button>
            <button class="calculate__btn">
                <span>Подтвердить</span>
                <svg viewBox="0 0 26 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M24.5 7.5H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1C18.5 1 19 3.36364 20.5 5C22.1874 6.84075 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14C18.5 14 19 11.6364 20.5 10C22.1874 8.15925 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
    </form>
</div>