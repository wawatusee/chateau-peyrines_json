<?php class CatalogView{
 private $catalogView=" ";
    public function getView(array $catalogArray){
        foreach($catalogArray as $showroom){
            //Comment décomposer chaque produit sans twig
            //Chaque catégorie de produits
            $this->catalogView.="<section class='showcase'>";
            $this->catalogView.="<h4>".$showroom->showroom."</H4>";
            foreach ($showroom->Products as $product){
                //Chaque produit
                $this->catalogView.="<div class='product'>".$product->année." ".$product->product;
                $this->catalogView.="<details class='detailsvin'><summary>Vin</summary>";
                if (isset($product->image)){
                    $this->catalogView.="<img class='imgcatalog' alt='photo du produit' src='img/catalog/".$product->image."'>";
                };
                if (isset($product->detail)){
                    $this->catalogView.="<p>".$product->detail."</p>";
                };
                $this->catalogView.="</details>";
                $this->catalogView.="<div class='prices'>";
                foreach($product->packaging as $packaging){
                    //Les prix selon la quantité
                    $this->catalogView.="<table class='packaging'>";
                    $this->catalogView.="<thead class='quantite'><tr><th colspan='2'>".$packaging->quantité."</th></tr></thead>";
                    $this->catalogView.="<tbody><tr><td class='price'>".$packaging->price."</td><td>l'unité</td></tr>";
                    $this->catalogView.="</tbody></table>";
                };
                $this->catalogView.="</div></div>";
            };

            $this->catalogView.="</section>";
        }
        return $this->catalogView;
    }

}