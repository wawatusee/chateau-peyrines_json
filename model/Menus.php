<?php
class Menus{
    private $srcJson;
    private $menus;
    private $types;
    public function __construct($srcJson){
        $this->srcJson=$srcJson;
        $this->menus=json_decode(file_get_contents($srcJson));
    }
    public function getMenu(string $menuType){
        $menu_array=$this->menus->$menuType;
        return $menu_array;
    }
}