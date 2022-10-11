<?php class ViewMenu{
    private $viewMenu=" ";
    public function getViewMainMenu(array $menuArray){
        foreach($menuArray as $item){
            $this->viewMenu.="<a href=#".$item->page.">".$item->titre."</a>";
        }
        $viewMenu=$this->viewMenu;
        return $viewMenu;
    }

}
