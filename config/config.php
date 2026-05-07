<?php 
$singlePage=1;
require_once("../model/menus_model.php");
$titleWebSite=["Château","Peyrines", "Grands vins de Bordeaux"];
$menus=new MenusModel("../json/menus.json");
$menuRS=$menus->getMenu("RS_menu");
$pagesDuMenus=array();
 foreach($menus->getMenu("Main_menu") as $page){
     array_push($pagesDuMenus,$page->page) ;
 }
define('PAGE_ARRAY',$pagesDuMenus);