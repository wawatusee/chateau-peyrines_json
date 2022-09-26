<?php
function getJSONSetMenu($jsonUrl){
$menu=json_decode(file_get_contents("$jsonUrl"));
return $menu;
}