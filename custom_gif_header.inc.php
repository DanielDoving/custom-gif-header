<?php

require_once(__DIR__ . '/config.inc.php');
include_once(__DIR__ . '/classes/custom_gif_header.def.php');

$css_path = __DIR__ . '/css/custom.css';

if (file_exists($css_path)) {
    $custom_css = "<style>";
    $custom_css .= file_get_contents($css_path);
    $custom_css .= "</style";
}

(new \custom_gif_header())
    ->set_header($custom_css);

