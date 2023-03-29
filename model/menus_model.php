<?php
/**
 *Class MenusModel
 *Permet d'importer un fichier au format json et de renvoyer son contenu sous forme de tableau
 */
class MenusModel{
    /**
     * @var string chemin du fichier json qui va être traité 
     */
    private $srcJson;
    /**
     * @var array valeur de php du fichier json importé
     */
    private $menus;

    public function __construct($srcJson){
        $this->srcJson=$srcJson;
        $this->menus=json_decode(file_get_contents($srcJson));
    }
    public function getMenu(string $menuType){
        $menu_array=$this->menus->$menuType;
        return $menu_array;
    }
}