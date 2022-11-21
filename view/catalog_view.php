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
                
                foreach($product->packaging as $packaging){
                    //Les prix selon la quantité
                    $this->catalogView.="<table class='prices'><tbody>";
                    $this->catalogView.="<th class='packaging'>".$packaging->quantité."</th>";
                    $this->catalogView.="<tr><td>btl</td><td class='cost'>".$packaging->price."</td></tr>";
                    $this->catalogView.="</tbody></table>";
                };
                
            };

            $this->catalogView.="</section>";
        }
        return $this->catalogView;
    }

}