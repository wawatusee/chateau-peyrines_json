<?php 
require_once("../model/menus_model.php");
$titleWebSite=["Château","Peyrines", "Grands vins de Bordeaux"];
$menus=new MenusModel("../json/menus.json");
$menuRS=$menus->getMenu("RS_menu");