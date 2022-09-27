<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	$arMenu = array();
	foreach($arResult as $arMenuItem){
		switch($arMenuItem["DEPTH_LEVEL"]){
			case 1:{
				$arMenu[] = $arMenuItem;
				break;
			}
			case 2:{
				$arMenu[count($arMenu)-1]["MENU_ITEMS"][] = $arMenuItem;
				break;
			}
			default: continue;
		}
	}
?>
<?foreach($arMenu as $arMenuItem):?>
	<?if(isset($arMenuItem["MENU_ITEMS"])):?>
		<div class="header__about-menu">
		    <div class="header__about-menu-btn">
		        <a href="<?=$arMenuItem["LINK"]?>" class="header__link"><?=$arMenuItem["TEXT"]?></a>
		        <svg viewBox="0 0 11 6" fill="none" xmlns="http://www.w3.org/2000/svg">
		            <path d="M5.5 0.949747H10.4497L5.5 5.89949L0.550253 0.949747H5.5Z" />
		        </svg>
		    </div>
		    <div class="header__about-list-bg">
		        <ul class="header__about-list">
		        	<?foreach($arMenuItem["MENU_ITEMS"] as $arMI):?>
			            <li class="header__about-item">
			                <a href="<?=$arMI["LINK"]?>" class="header__about-item-link"><?=$arMI["TEXT"]?></a>
			            </li>
			        <?endforeach;?>
		        </ul>
		    </div>
		</div>
	<?else:?>
		<a href="<?=$arMenuItem["LINK"]?>" class="header__link"><?=$arMenuItem["TEXT"]?></a>
	<?endif;?>
<?endforeach;?>