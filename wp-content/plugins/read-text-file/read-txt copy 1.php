<?php
/*
Plugin Name: Read Text File
Plugin URI: http://www.aaronsonnenberg.com/wordpress-plugin-read-text
Description: A simple wordpress plugin to display a text file using a shortcode.
Version: 0.1
Author: Aaron Sonnenberg
Author URI: http://aaronsonnenberg.com
License: GPL
*/

DEFINE("RT_FILE", "");

//tell wordpress to register the shortcode
add_shortcode("read-text", "rt_handler");

function rt_handler($text) {
  $text=shortcode_atts(array("path" => RT_FILE), $text);
  $rt_text = rt_function($text);
  return $rt_text;
}

function rt_function ($text) {
$file = $_SERVER['DOCUMENT_ROOT'] .$text["path"]; 
$contents = file($file);
$string = implode($contents);
$rt_text = '<div style="clear:both;">'.nl2br($string).'</div>';
return $rt_text;
}
?>