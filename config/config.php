<?php 
require_once("../model/menus_model.php");
$titleWebSite="Chateau-Peyrines";
$menus=new MenusModel("../json/menus.json");
$menuMain=$menus->getMenu("Main_menu");
$menuRS=$menus->getMenu("RS_menu");