<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
$this->addExternalJs($this->GetFolder().'/feedback.js');
?>
<div class="feedback__img-box">
	<img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arResult["PREVIEW_PICTURE"]["ALT"]?>">
</div>
<div class="feedback__form-block">
	<div class="feedback__sub-title">
		<?=$arResult["PREVIEW_TEXT"]?>
	</div>
	<div class="feedback__title">
		<?=$arResult["NAME"]?>
	</div>
	<form class="feedback__form" id="feedback__form" action="javascript:void(0)">
		<?=bitrix_sessid_post()?>
		<label class="feedback__label feedback__label--name">
			<svg viewBox="0 0 13 20" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M6.66216 9.1543C6.56081 9.14415 6.43919 9.14415 6.3277 9.1543C3.91554 9.07312 2 7.09455 2 4.65937C2 2.17346 4.00676 0.154297 6.5 0.154297C8.98311 0.154297 11 2.17346 11 4.65937C10.9899 7.09455 9.07432 9.07312 6.66216 9.1543Z" /><path d="M1.72984 12.4277C-0.576613 13.9308 -0.576613 16.3802 1.72984 17.8739C4.35081 19.5811 8.64919 19.5811 11.2702 17.8739C13.5766 16.3709 13.5766 13.9215 11.2702 12.4277C8.65872 10.7298 4.36034 10.7298 1.72984 12.4277Z" />
			</svg>
			<input type="text" class="feedback__input" name="USER_NAME" placeholder="Иван Иванов" required>
		</label>
		<label class="feedback__label feedback__label--phone">
			<svg viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M19.97 16.4843C19.97 16.8443 19.89 17.2143 19.72 17.5743C19.55 17.9343 19.33 18.2743 19.04 18.5943C18.55 19.1343 18.01 19.5243 17.4 19.7743C16.8 20.0243 16.15 20.1543 15.45 20.1543C14.43 20.1543 13.34 19.9143 12.19 19.4243C11.04 18.9343 9.89 18.2743 8.75 17.4443C7.6 16.6043 6.51 15.6743 5.47 14.6443C4.44 13.6043 3.51 12.5143 2.68 11.3743C1.86 10.2343 1.2 9.0943 0.72 7.9643C0.24 6.8243 0 5.7343 0 4.6943C0 4.0143 0.12 3.3643 0.36 2.7643C0.6 2.1543 0.98 1.5943 1.51 1.0943C2.15 0.464297 2.85 0.154297 3.59 0.154297C3.87 0.154297 4.15 0.214297 4.4 0.334297C4.66 0.454297 4.89 0.634297 5.07 0.894297L7.39 4.1643C7.57 4.4143 7.7 4.6443 7.79 4.8643C7.88 5.0743 7.93 5.2843 7.93 5.4743C7.93 5.7143 7.86 5.9543 7.72 6.1843C7.59 6.4143 7.4 6.6543 7.16 6.8943L6.4 7.6843C6.29 7.7943 6.24 7.9243 6.24 8.0843C6.24 8.1643 6.25 8.2343 6.27 8.3143C6.3 8.3943 6.33 8.4543 6.35 8.5143C6.53 8.8443 6.84 9.2743 7.28 9.7943C7.73 10.3143 8.21 10.8443 8.73 11.3743C9.27 11.9043 9.79 12.3943 10.32 12.8443C10.84 13.2843 11.27 13.5843 11.61 13.7643C11.66 13.7843 11.72 13.8143 11.79 13.8443C11.87 13.8743 11.95 13.8843 12.04 13.8843C12.21 13.8843 12.34 13.8243 12.45 13.7143L13.21 12.9643C13.46 12.7143 13.7 12.5243 13.93 12.4043C14.16 12.2643 14.39 12.1943 14.64 12.1943C14.83 12.1943 15.03 12.2343 15.25 12.3243C15.47 12.4143 15.7 12.5443 15.95 12.7143L19.26 15.0643C19.52 15.2443 19.7 15.4543 19.81 15.7043C19.91 15.9543 19.97 16.2043 19.97 16.4843Z" />
			</svg>
			<input type="text" class="feedback__input input-phone" name="USER_PHONE" placeholder="+7(___) ___-__-__" required>
		</label>
		<button type="submit" class="feedback__btn">
			<span><?=$arResult["PROPERTIES"]["OTPRAVIT_KNOPKA"]["VALUE"]?></span>
			<svg viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M24.5 7.6543H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1.1543C18.5 1.1543 19 3.51793 20.5 5.1543C22.1874 6.99504 25 7.6543 25 7.6543" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14.1543C18.5 14.1543 19 11.7907 20.5 10.1543C22.1874 8.31355 25 7.6543 25 7.6543" stroke-width="2" stroke-linecap="round"/>
			</svg>
		</button>
		<label class="feedback__label feedback__label--checkbox label-checkbox check">
			<input type="checkbox" class="feedback__checkbox" name="POLICY" checked>
			<div class="feedback__checkbox-check">
				<svg viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M17 1.1543L6 12.1543L1 7.1543" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>
			<span><?=$arResult["PROPERTIES"]["POLITIKA_KONFIDENCIALNOSTI"]["VALUE"]?></span>
		</label>
	</form>
</div>
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
<section class="modal-sent" id="modal-sent">
    <div class="modal-sent__bg close"></div>
    <div class="modal-sent__content">
        <button type="button" class="modal-sent__close close" id="success_close"></button>
        <h2 class="modal-sent__title">Спасибо!</h2>
        <p class="modal-sent__desc">Ваша заявка успешно отравлена!</p>
        <button type="button" class="modal-sent__btn close" id="second_success_close">
            <span>Вернуться к сайту</span>
            <svg viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M24.5 8.11523H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1.61523C18.5 1.61523 19 3.97887 20.5 5.61523C22.1874 7.45598 25 8.11523 25 8.11523" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14.6152C18.5 14.6152 19 12.2516 20.5 10.6152C22.1874 8.77449 25 8.11523 25 8.11523" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
    </div>
</section>
<script>
	BX.Feedback.init({
		formID: 'feedback__form',
		buttonClass: 'feedback__btn',
		<?if(isset($_GET['ELEMENT_NAME'])&&$_GET["ELEMENT_NAME"]!="") echo "elementName: '".htmlspecialchars($_GET['ELEMENT_NAME'])."'";
		else if(isset($_GET['SECTION_NAME'])&&$_GET["SECTION_NAME"]!="") echo "sectionName: '".htmlspecialchars($_GET['SECTION_NAME'])."'";?>
	});
</script>