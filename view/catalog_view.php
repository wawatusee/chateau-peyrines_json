<?php class CatalogView{
 private $catalogView=" ";
    public function getView(array $catalogArray){
        foreach($catalogArray as $showroom){
            //Comment décomposer chaque produit sans twig
            //Chaque catégorie de produits
            $this->catalogView.="<h3>".$showroom->showroom."</H3>";
            $this->catalogView.="<section class='showcase'>";
            foreach ($showroom->Products as $product){
                //Chaque produit
                $this->catalogView.="<div class='product'>".$product->année." ".$product->product."</div>";
                $this->catalogView.="<div class='prices'>";
                foreach($product->packaging as $packaging){
                    //Les prix selon la quantité
                    $this->catalogView.="<div class='packaging'>".$packaging->quantité."</div>";
                    $this->catalogView.="<div class='cost'>".$packaging->price."</div>";
                };
                $this->catalogView.="</div>";
            };

            $this->catalogView.="</section>";
        }
        return $this->catalogView;
    }

}