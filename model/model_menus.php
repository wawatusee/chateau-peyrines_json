<?php
//Cette fonction doit être transformée en classe "Menu", avec une hydratation sur tous les types de menus contenus dans le JSON et un get pour chaque
function getJSONSetMenu($jsonUrl,$menuType){
//Transforme le json reçu en argument en tableau contenant les objets(page-titre) permettant de construire des liens html
$menus=json_decode(file_get_contents("$jsonUrl"));
$menu_array=$menus->$menuType;
return $menu_array;
}