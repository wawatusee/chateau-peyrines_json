<?php class ViewMenu{
    private $viewMenu=" ";
    public function getViewMainMenu(array $menuArray, $singlePage=false){
        foreach($menuArray as $item){
            if($singlePage){
                $this->viewMenu.="<a href=#".$item->page.">".$item->titre."</a>";
            }else $this->viewMenu.="<a href="."?page=".$item->page.">".$item->titre."</a>";
            
        }
        $viewMenu=$this->viewMenu;
        return $viewMenu;
    }

}