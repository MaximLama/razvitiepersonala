<?php
$arUrlRewrite=array (
  6 => 
  array (
    'CONDITION' => '#^/obuchenie-i-attestatsiya/([\\w,-]+)/([\\w,-]+)/(\\?([^/]+))?$#',
    'RULE' => 'SECTION_CODE=$1&ELEMENT_CODE=$2',
    'ID' => '',
    'PATH' => '/obuchenie-i-attestatsiya/detail.php',
    'SORT' => 100,
  ),
  4 => 
  array (
    'CONDITION' => '#^/obuchenie-i-attestatsiya/([\\w,-]+)/(\\?([^/]+))?$#',
    'RULE' => 'SECTION_CODE=$1',
    'ID' => '',
    'PATH' => '/obuchenie-i-attestatsiya/section.php',
    'SORT' => 100,
  ),
  3 => 
  array (
    'CONDITION' => '#^/otzyvy/([\\w,-]+)/(\\?([^/]+))?$#',
    'RULE' => 'ELEMENT_CODE=$1',
    'ID' => '',
    'PATH' => '/otzyvy/detail.php',
    'SORT' => 100,
  ),
  0 => 
  array (
    'CONDITION' => '#^\\/?\\/mobileapp/jn\\/(.*)\\/.*#',
    'RULE' => 'componentName=$1',
    'ID' => NULL,
    'PATH' => '/bitrix/services/mobileapp/jn.php',
    'SORT' => 100,
  ),
  2 => 
  array (
    'CONDITION' => '#^/bitrix/services/ymarket/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/ymarket/index.php',
    'SORT' => 100,
  ),
  1 => 
  array (
    'CONDITION' => '#^/rest/#',
    'RULE' => '',
    'ID' => NULL,
    'PATH' => '/bitrix/services/rest/index.php',
    'SORT' => 100,
  ),
);
