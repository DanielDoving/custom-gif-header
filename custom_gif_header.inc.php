<?php

require_once(__DIR__ . '/config.inc.php');
include_once(__DIR__ . '/classes/custom_gif_header.def.php');

$css_path = __DIR__ . '/css/custom.css';
$js_path  = __DIR__ .'/js/gif_header.js';

$custom_css = null;
if (file_exists($css_path)) {
    $custom_css = "<style>";
    $custom_css .= file_get_contents($css_path);
    $custom_css .= "</style";
}
$custom_js = null;
if(file_exists($js_path)) {
    $custom_js = '<script>' . file_get_contents($js_path) . '</script>';
}


(new \custom_gif_header())
    ->set_header($custom_css, $custom_js);

