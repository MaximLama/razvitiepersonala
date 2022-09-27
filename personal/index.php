<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<section class="personal">
    <div class="container">
        <h1 class="personal__title">Личные данные</h1>
        <div class="personal__content">
            <?$APPLICATION->IncludeComponent("bitrix:menu", "lk", Array(
                "ALLOW_MULTI_SELECT" => "N",    // Разрешить несколько активных пунктов одновременно
                    "CHILD_MENU_TYPE" => "left",    // Тип меню для остальных уровней
                    "DELAY" => "N", // Откладывать выполнение шаблона меню
                    "MAX_LEVEL" => "1", // Уровень вложенности меню
                    "MENU_CACHE_GET_VARS" => array( // Значимые переменные запроса
                        0 => "",
                    ),
                    "MENU_CACHE_TIME" => "3600",    // Время кеширования (сек.)
                    "MENU_CACHE_TYPE" => "N",   // Тип кеширования
                    "MENU_CACHE_USE_GROUPS" => "Y", // Учитывать права доступа
                    "ROOT_MENU_TYPE" => "personal", // Тип меню для первого уровня
                    "USE_EXT" => "N",   // Подключать файлы с именами вида .тип_меню.menu_ext.php
                ),
                false
            );?>
            <div class="personal__main" id="personal__main">
                <?if(!$USER->IsAuthorized()){
                    LocalRedirect('/auth/');
                }
				$APPLICATION->IncludeComponent(
					"bitrix:main.profile",
					"personal",
					Array(
						"CHECK_RIGHTS" => "N",
						"SEND_INFO" => "N",
						"SET_TITLE" => "Y",
						"USER_PROPERTY" => array(),
						"USER_PROPERTY_NAME" => ""
					)
				);?>
			</div>
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
	BX.PersonalForm.init({
		formClass: 'personal__form',
		sendClass: 'calculate__btn',
		containerId: 'personal__main',
		inputClass: 'calculate__input',
	});
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>