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
  //adaptado para mostrar las ultimas lineas primero
  //comienza en directorio raíz. se puede cambiar ruta en el shortcode
  $url = get_option( 'siteurl' )."/".$text["path"];
  $contents = file($url);
  if (count($contents)>500) {
    $limitado = array_reverse(array_slice($contents,-500)); }
  else { 
    $limitado = array_reverse($contents); }


  $string = implode($limitado); 
  //$string = implode(array_reverse($contents));
  $rt_text = '<div style="clear:both;">'.nl2br($string).'</div>';
  return $rt_text;
}
?>