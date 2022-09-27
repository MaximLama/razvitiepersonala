<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $mobileColumns
 * @var array $arParams
 * @var string $templateFolder
 */
?>
<script id="basket-item-template" type="text/html">
	<div class="cart__item" id="basket-item-{{ID}}" data-entity="basket-item" data-id="{{ID}}">
		{{#COLUMN_LIST}}
			<a href="{{DETAIL_PAGE_URL}}" class="cart__img-box">
				{{#VALUE}}
                	<img src="{{{IMAGE_SRC}}}" alt="">
               	{{/VALUE}}
            </a>
		{{/COLUMN_LIST}}
		<div class="cart__info-block">
            <div class="cart__item-name"><a href="{{DETAIL_PAGE_URL}}">{{NAME}}</a></div>
            <div class="cart__item-activity">Обучение</div>
        </div>
        <div class="cart__price-block">
            <div class="cart__price">{{{SUM_PRICE_FORMATED}}}</div>
            <button class="cart__item-delete" data-entity="basket-item-delete">
                <svg viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.2188 15.3125V24.0625C14.2188 24.3526 14.334 24.6308 14.5391 24.8359C14.7442 25.041 15.0224 25.1562 15.3125 25.1562C15.6026 25.1562 15.8808 25.041 16.0859 24.8359C16.291 24.6308 16.4062 24.3526 16.4062 24.0625V15.3125C16.4062 15.0224 16.291 14.7442 16.0859 14.5391C15.8808 14.334 15.6026 14.2188 15.3125 14.2188C15.0224 14.2188 14.7442 14.334 14.5391 14.5391C14.334 14.7442 14.2188 15.0224 14.2188 15.3125ZM19.6875 14.2188C19.9776 14.2188 20.2558 14.334 20.4609 14.5391C20.666 14.7442 20.7812 15.0224 20.7812 15.3125V24.0625C20.7812 24.3526 20.666 24.6308 20.4609 24.8359C20.2558 25.041 19.9776 25.1562 19.6875 25.1562C19.3974 25.1562 19.1192 25.041 18.9141 24.8359C18.709 24.6308 18.5938 24.3526 18.5938 24.0625V15.3125C18.5938 15.0224 18.709 14.7442 18.9141 14.5391C19.1192 14.334 19.3974 14.2188 19.6875 14.2188ZM21.875 8.75H28.4375C28.7276 8.75 29.0058 8.86523 29.2109 9.07035C29.416 9.27547 29.5312 9.55367 29.5312 9.84375C29.5312 10.1338 29.416 10.412 29.2109 10.6171C29.0058 10.8223 28.7276 10.9375 28.4375 10.9375H27.2278L25.5828 25.76C25.4342 27.0977 24.7975 28.3337 23.7946 29.2313C22.7916 30.1289 21.4928 30.6252 20.1469 30.625H14.8531C13.5072 30.6252 12.2084 30.1289 11.2054 29.2313C10.2025 28.3337 9.56578 27.0977 9.41719 25.76L7.77 10.9375H6.5625C6.27242 10.9375 5.99422 10.8223 5.7891 10.6171C5.58398 10.412 5.46875 10.1338 5.46875 9.84375C5.46875 9.55367 5.58398 9.27547 5.7891 9.07035C5.99422 8.86523 6.27242 8.75 6.5625 8.75H13.125C13.125 7.58968 13.5859 6.47688 14.4064 5.65641C15.2269 4.83594 16.3397 4.375 17.5 4.375C18.6603 4.375 19.7731 4.83594 20.5936 5.65641C21.4141 6.47688 21.875 7.58968 21.875 8.75ZM17.5 6.5625C16.9198 6.5625 16.3634 6.79297 15.9532 7.2032C15.543 7.61344 15.3125 8.16984 15.3125 8.75H19.6875C19.6875 8.16984 19.457 7.61344 19.0468 7.2032C18.6366 6.79297 18.0802 6.5625 17.5 6.5625ZM9.97281 10.9375L11.5916 25.5194C11.6809 26.3219 12.063 27.0632 12.6648 27.6016C13.2665 28.14 14.0457 28.4376 14.8531 28.4375H20.1469C20.9539 28.4371 21.7325 28.1392 22.3338 27.6009C22.9351 27.0625 23.3169 26.3215 23.4062 25.5194L25.0294 10.9375H9.975H9.97281Z" />
                </svg>
            </button>
        </div>
	</div>
</script>