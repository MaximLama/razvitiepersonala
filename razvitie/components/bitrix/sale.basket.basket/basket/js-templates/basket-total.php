<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 */
?>
<!--{{#DISABLE_CHECKOUT}}{{/DISABLE_CHECKOUT}}-->
<script id="basket-total-template" type="text/html">
	<button class="cart__btn" data-entity="basket-checkout-button">
        <span>Оформить заказ</span>
        <svg viewBox="0 0 26 15" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M24.5 7.5H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1C18.5 1 19 3.36364 20.5 5C22.1874 6.84075 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14C18.5 14 19 11.6364 20.5 10C22.1874 8.15925 25 7.5 25 7.5" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </button>
	<div class="cart__total-box">
        <div class="cart__total-text">Итого:</div>
        <div class="cart__total" data-entity="basket-total-price">{{{PRICE_FORMATED}}}</div>
    </div>
</script>