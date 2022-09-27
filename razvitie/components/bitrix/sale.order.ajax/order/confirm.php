<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 * @var $APPLICATION CMain
 */
?>
<section >
    <div class="calculate__complete">
        <div class="complete__title">Спасибо!</div>
        <div class="complete__img-box">
            <img src="<?=SITE_TEMPLATE_PATH?>/img/login-complete-true.svg" alt="">
        </div>
        <div class="complete__text">
            <div>Оплата прошла успешно. Мы&nbsp;свяжемся с&nbsp;вами в&nbsp;ближайшее время.</div>
            <div class="complete__text--bold">Номер вашего заказа №<?=$_REQUEST['ORDER_ID']?></div>
        </div>
        <a href="/obuchenie-i-attestatsiya/" class="complete__link">Посмотреть другие курсы</a>
    </div>
</section>
