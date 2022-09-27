<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
foreach($arResult as $arItem):
?>
	<li class="header__burger-item">
        <a href="<?=$arItem["LINK"]?>">
            <?=$arItem["TEXT"]?>
        </a>
    </li>	
<?endforeach?>
