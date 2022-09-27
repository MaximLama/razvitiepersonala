<?
require_once($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH."/includes/read_cache.php");
if(!(defined('LOADED')&&LOADED==="Y")){
    function getRequestResult($request){
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL,$request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
        return $server_output;
    }
    $org_url='https://yandex.ru/maps/org/razvitiye_personala/70898900027/';
    $GLOBALS['html']=getRequestResult($org_url);
    require_once("write_cache.php");
}
require_once($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH."/libs/simple_html_dom.php");
$htmlDOM = str_get_html($GLOBALS['html']);
$rating = $htmlDOM->find('.business-rating-badge-view__rating-text')[0]->innertext;
$rating = str_replace(",", ".", $rating);
$reviewsCount = trim($htmlDOM->find('.business-header-rating-view__text')[0]->innertext);
$reviewsCount = explode(' ', $reviewsCount)[0];
$htmlDOM->clear();
unset($htmlDOM);
$GLOBALS["reviews"]=array(
                        "rating"=>$rating,
                        "reviews"=>$reviewsCount
                    );