<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<ul class="footer__list">
	<?for($i = 0; $i<3;$i++):?>
	    <li class="footer__list-item">
	        <a href="<?=$arResult[$i]["LINK"]?>" class="footer__link"><?=$arResult[$i]["TEXT"]?></a>
	    </li>
    <?endfor;?>
</ul>
<ul class="footer__list">
	<?for($i=3;$i<count($arResult);$i++):?>
        <li class="footer__list-item">
            <a href="<?=$arResult[$i]["LINK"]?>" class="footer__link"><?=$arResult[$i]["TEXT"]?></a>
        </li>
    <?endfor;?>