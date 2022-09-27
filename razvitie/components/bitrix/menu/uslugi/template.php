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
<ul class="header__services-list">
	<?foreach($arMenu as $arMenuItem1Lvl):?>
	    <li class="header__services-item">
	        <div class="header__services-item-name">
	            <span><?=$arMenuItem1Lvl["TEXT"]?></span>
	            <svg viewBox="0 0 16 10" fill="none" xmlns="http://www.w3.org/2000/svg">
	                <path d="M8.35565 8.64921L14.6493 2.35347C14.9248 2.07722 14.9248 1.62965 14.6493 1.3527C14.3737 1.07644 13.9262 1.07644 13.6506 1.3527L7.85634 7.14906L2.06206 1.3534C1.78651 1.07714 1.33894 1.07714 1.06268 1.3534C0.787128 1.62965 0.787128 2.07792 1.06268 2.35417L7.35633 8.64991C7.62903 8.92198 8.08364 8.92198 8.35565 8.64921Z" stroke-width="0.5"/>
	            </svg>
	        </div>
	        <ul class="header__services-sub-list">
	        	<?foreach($arMenuItem1Lvl["MENU_ITEMS"] as $arMenuItem2Lvl):?>
		            <li class="header__services-sub-item">
		                <a href="<?=$arMenuItem2Lvl["LINK"]?>" class="header__services-sub-item-link">
		                    <?=$arMenuItem2Lvl["TEXT"]?>
		                </a>
		            </li>
		        <?endforeach;?>
	        </ul>
	    </li>
	<?endforeach;?>
</ul>