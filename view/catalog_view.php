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
                $this->catalogView.="<div class='product'>".$product->année." ".$product->product."<div class='prices'>";
                
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