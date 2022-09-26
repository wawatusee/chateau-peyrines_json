<?php
function getJSONSetMenu($jsonUrl){
$menus=json_decode(file_get_contents("$jsonUrl"));
$menuMain_array=$menus->Main_menu;
return $menuMain_array;
}