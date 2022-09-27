<?php
  $cache_time = 24*60*60; // Время жизни кэша (сек)
  $file = "yandex_reviews.html";
  $cache_file = $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH."/cache/$file"; // Адрес нахождения файла (/cache/yandex_reviews.html)
  $GLOBALS['html'] = '';
  if (file_exists($cache_file)) {
    // Если файл с кэшем существует
    if ((time() - $cache_time) < filemtime($cache_file)) {
      // Если его время жизни ещё не прошло
      $GLOBALS['html'] = file_get_contents($cache_file);
      if($GLOBALS['html']!=='')
        define("LOADED", "Y"); // Завершаем скрипт, чтобы сэкономить время на дальнейшей обработке
    }
  }
  if(!(defined('LOADED')&&LOADED==="Y")){
    ob_start(); // Открываем буфер для вывода, если кэша нет, или он устарел
  }