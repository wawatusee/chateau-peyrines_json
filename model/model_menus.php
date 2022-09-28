<?php
function getJSONSetMenu($jsonUrl){
//Transforme le json reçu en argument en tableau contenant les objets(page-titre) permettant de construire des liens html
$menus=json_decode(file_get_contents("$jsonUrl"));
$menuMain_array=$menus->Main_menu;
return $menuMain_array;
}