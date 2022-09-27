<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<section class="personal">
<div class="container">
	<h1 class="personal__title">
		 История покупок
	</h1>
	<div class="personal__content">
		 <?$APPLICATION->IncludeComponent("bitrix:menu", "lk", Array(
			"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
				"CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
				"DELAY" => "N",	// Откладывать выполнение шаблона меню
				"MAX_LEVEL" => "1",	// Уровень вложенности меню
				"MENU_CACHE_GET_VARS" => array(	// Значимые переменные запроса
					0 => "",
				),
				"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
				"MENU_CACHE_TYPE" => "N",	// Тип кеширования
				"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
				"ROOT_MENU_TYPE" => "personal",	// Тип меню для первого уровня
				"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
			),
			false
		);?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:sale.personal.order.list",
			"history",
			Array(
				"ACTIVE_DATE_FORMAT" => "d.m.Y",
				"CACHE_GROUPS" => "Y",
				"CACHE_TIME" => "3600",
				"CACHE_TYPE" => "A",
				"DEFAULT_SORT" => "STATUS",
				"DISALLOW_CANCEL" => "N",
				"HISTORIC_STATUSES" => array(0=>"F",),
				"ID" => $ID,
				"NAV_TEMPLATE" => "",
				"ORDERS_PER_PAGE" => "20",
				"PATH_TO_BASKET" => "",
				"PATH_TO_CANCEL" => "",
				"PATH_TO_CATALOG" => "/catalog/",
				"PATH_TO_COPY" => "",
				"PATH_TO_DETAIL" => "",
				"PATH_TO_PAYMENT" => "payment.php",
				"REFRESH_PRICES" => "N",
				"RESTRICT_CHANGE_PAYSYSTEM" => array(0=>"0",),
				"SAVE_IN_SESSION" => "Y",
				"SET_TITLE" => "N",
				"STATUS_COLOR_F" => "gray",
				"STATUS_COLOR_N" => "green",
				"STATUS_COLOR_PA" => "gray",
				"STATUS_COLOR_PSEUDO_CANCELLED" => "red"
			)
		);?>
	</div>
</div>
 </section> <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>