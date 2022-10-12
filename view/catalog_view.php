<?php class CatalogView{
 private $catalogView=" ";
    public function getView(array $catalogArray){
        foreach($catalogArray as $showroom){

            //Comment décomposer chaque produit dans cette boucle?
            $this->catalogView.="<h3>".$showroom->showroom."</H3>";
            $this->catalogView.="<section class='showcase'>";
            //$this->catalogView.=$showroom->products[0]->product;
            $this->catalogView.="</section>";
        }
        return $this->catalogView;
    }
}