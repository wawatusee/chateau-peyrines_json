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
                $this->catalogView.="<table class='prices'><tbody>";
                foreach($product->packaging as $packaging){
                    //Les prix selon la quantité
                    $this->catalogView.="<tr>";
                    $this->catalogView.="<td class='packaging'>".$packaging->quantité."</td>";
                    $this->catalogView.="<td class='cost'>".$packaging->price."</td>";
                    $this->catalogView.="</tr>";
                };
                $this->catalogView.="</tbody></table>";
            };

            $this->catalogView.="</section>";
        }
        return $this->catalogView;
    }

}