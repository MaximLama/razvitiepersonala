<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 *
 *  _________________________________________________________________________
 * |	Attention!
 * |	The following comments are for system use
 * |	and are required for the component to work correctly in ajax mode:
 * |	<!-- items-container -->
 * |	<!-- pagination-container -->
 * |	<!-- component-end -->
 */

$this->setFrameMode(true);
?>
<?if(count($arResult["ITEMS"])):?>
	<section class="offers">
	    <div class="container">
	        <div class="offers__title-content">
	            <div class="offers__title">Пакетные предложения</div>
	            <div class="offers__navigation-block navigation-block">
	                <div class="navigation-block__pagination-block">
	                    <div class="offers__current-slide navigation-block__current-slide"></div>
	                    <div class="offers__pagination navigation-block__pagination"></div>
	                    <div class="offers__total-slide navigation-block__total-slide"></div>
	                </div>
	                <div class="navigation-block__arrows">
	                    <svg class="offers__arrow-left navigation-block__arrow" viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
	                        <path d="M1.5 7.88477H25" stroke-width="2" stroke-linecap="round"/><path d="M7.5 1.38477C7.5 1.38477 7 3.7484 5.5 5.38477C3.81265 7.22551 1 7.88477 1 7.88477" stroke-width="2" stroke-linecap="round"/><path d="M7.5 14.3848C7.5 14.3848 7 12.0211 5.5 10.3848C3.81265 8.54402 1 7.88477 1 7.88477" stroke-width="2" stroke-linecap="round"/>
	                    </svg>
	                    <svg class="offers__arrow-right navigation-block__arrow" viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
	                        <path d="M24.5 7.88477H1" stroke-width="2" stroke-linecap="round"/><path d="M18.5 1.38477C18.5 1.38477 19 3.7484 20.5 5.38477C22.1874 7.22551 25 7.88477 25 7.88477" stroke-width="2" stroke-linecap="round"/><path d="M18.5 14.3848C18.5 14.3848 19 12.0211 20.5 10.3848C22.1874 8.54402 25 7.88477 25 7.88477" stroke-width="2" stroke-linecap="round"/>
	                    </svg>
	                </div>
	            </div>
	        </div>
	        <div class="offers__slider swiper">
	            <div class="offers__wrapper swiper-wrapper">
	            	<?foreach($arResult["ITEMS"] as $key=>$arItem):?>
		                <div class="offers__item swiper-slide">
		                    <div class="offers__item-info-block">
		                        <div class="offers__item-title-block">
		                            <div class="offers__item-number">
		                                <span>0<?=$key+1?></span>
		                            </div>
		                            <div class="offers__item-title">
		                                <?=$arItem["~NAME"]?>
		                            </div>
		                        </div>
		                        <div class="offers__item-text-block">
		                            <div class="offers__item-text-block-name">
		                                При единоразовой покупке <?=count($arItem["PROPERTIES"]["TOVARY"]["VALUE"])?> модулей:
		                            </div>
		                            <?
		                            $rsTovary = CIBlockElement::GetList(
		                            	array("SORT"=>"ASC"),
		                            	array("ID"=>$arItem["PROPERTIES"]["TOVARY"]["VALUE"], "IBLOCK_ID"=>26),
		                            	false,
		                            	false,
		                            	array("ID", "IBLOCK_ID", "NAME")
		                            );
		                            $tovary = [];
		                            while($tovar = $rsTovary->GetNext()){
		                            	$tovary[] = $tovar;
		                            }
		                            ?>
		                            <ul class="offers__list">
		                            	<?foreach($tovary as $tovar):?>
			                                <li class="offers__list-item">
			                                    <?=$tovar["~NAME"]?>
			                                </li>
		                                <?endforeach;?>
		                            </ul>
		                        </div>
		                    </div>
		                    <div class="offers__item-price-block">
		                        <div class="offers__price-box">
		                            <div class="offers__price offers__price--current"><?=substr(CCurrencyLang::CurrencyFormat($arItem["PROPERTIES"]["NEW_PRICE"]["VALUE"], "RUB"), 0, strlen(CCurrencyLang::CurrencyFormat($arItem["PROPERTIES"]["NEW_PRICE"]["VALUE"], "RUB"))-8)?></div>
		                            <div class="offers__price offers__price--old"><?=substr(CCurrencyLang::CurrencyFormat($arItem["PROPERTIES"]["OLD_PRICE"]["VALUE"], "RUB"), 0, strlen(CCurrencyLang::CurrencyFormat($arItem["PROPERTIES"]["OLD_PRICE"]["VALUE"], "RUB"))-8)?></div>
		                        </div>
		                        <a href="javascript:void(0)" class="offers__price-btn buy" id=<?=$arItem["ID"]?>>
		                            <span>Купить</span>
		                            <svg viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg">
		                                <path d="M8.65019 8.26922L2.35445 14.5629C2.0782 14.8384 1.63062 14.8384 1.35367 14.5629C1.07742 14.2873 1.07742 13.8397 1.35367 13.5642L7.15004 7.76991L1.35437 1.97564C1.07812 1.70008 1.07812 1.25251 1.35437 0.976257C1.63062 0.700703 2.0789 0.700703 2.35515 0.976257L8.65089 7.2699C8.92295 7.54261 8.92295 7.99721 8.65019 8.26922Z" stroke-width="0.5"/>
		                            </svg>
		                        </a>
		                    </div>
		                </div>
		            <?endforeach;?>
	            </div>
	        </div>
	    </div>
	</section>
<?endif;?>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"modal-cart",
	Array(
		"AREA_FILE_SHOW" => "file",
		"AREA_FILE_SUFFIX" => "inc",
		"EDIT_TEMPLATE" => "standard.php",
		"PATH" => "/bitrix/templates/razvitie/includes/modal_cart.php"
	)
);?>