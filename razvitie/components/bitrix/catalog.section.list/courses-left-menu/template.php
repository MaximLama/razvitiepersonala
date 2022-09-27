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
$sections = array();
foreach($arResult["SECTIONS"] as $sect):
	if($sect["DEPTH_LEVEL"]==1){
		$sect["SECTIONS"] = array();
		$sections[] = $sect;
		continue;
	}
	if($sect["DEPTH_LEVEL"]==2){
		$sections[count($sections)-1]["SECTIONS"][] = $sect;
	}
endforeach;
?>
<ul class="services__services-list">
	<?foreach($sections as $arSectFirstLvl):?>
	    <li class="services__services-item">
	        <div class="services__services-item-name">
	            <span><?=$arSectFirstLvl["NAME"]?></span>
	            <svg viewBox="0 0 16 10" fill="none" xmlns="http://www.w3.org/2000/svg">
	                <path d="M8.35565 8.64921L14.6493 2.35347C14.9248 2.07722 14.9248 1.62965 14.6493 1.3527C14.3737 1.07644 13.9262 1.07644 13.6506 1.3527L7.85634 7.14906L2.06206 1.3534C1.78651 1.07714 1.33894 1.07714 1.06268 1.3534C0.787128 1.62965 0.787128 2.07792 1.06268 2.35417L7.35633 8.64991C7.62903 8.92198 8.08364 8.92198 8.35565 8.64921Z" stroke-width="0.5"/>
	            </svg>
	        </div>
	        <ul class="services__services-sub-list">
	        	<?foreach($arSectFirstLvl["SECTIONS"] as $arSectSecondLvl):?>
		            <li class="services__services-sub-item">
		                <a href="<?=$arSectSecondLvl["SECTION_PAGE_URL"]?>" class="services__services-sub-item-link">
		                    <?=$arSectSecondLvl["NAME"]?>
		                </a>
		            </li>
		        <?endforeach;?>
	        </ul>
	    </li>
	<?endforeach;?>
</ul>