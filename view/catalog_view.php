<?php class CatalogView{
 private $catalogView=" ";
    public function getView(array $catalogArray){
        foreach($catalogArray as $showroom){
            $this->catalogView.="<h3>".$showroom->showroom."</H3>";
        }
        return $this->catalogView;
    }
}