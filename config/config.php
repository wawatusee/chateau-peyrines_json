<?php 
require_once("../model/model_menus.php");
$titleWebSite="Chateau-Peyrines";
require_once('../model/menus.php');
$menus=new Menus("../json/menus.json");
$menuMain=$menus->getMenu("Main_menu");
$menuRS=$menus->getMenu("RS_menu");