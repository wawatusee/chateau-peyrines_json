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
                $this->catalogView.="<div class='product'>".$product->année." ".$product->product;
                
                foreach($product->packaging as $packaging){
                    //Les prix selon la quantité
                    $this->catalogView.="<table class='prices'>";
                    $this->catalogView.="<thead class='packaging'><tr><th colspan='2'>".$packaging->quantité."</th></tr></thead>";
                    $this->catalogView.="<tbody><tr><td class='price'>".$packaging->price."</td><td>la bouteille</td></tr>";
                    $this->catalogView.="</tbody></table>";
                };
                $this->catalogView.="</div>";
            };

            $this->catalogView.="</section>";
        }
        return $this->catalogView;
    }

}