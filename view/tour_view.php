<?php class TourView{
 private $tourView=" ";
    public function getView(array $tourArray){
        foreach($tourArray as $date){
            //Comment décomposer chaque produit sans twig
            //Chaque catégorie de produits
            $this->tourView.="<h3>".$date->date."</H3>";
        }
        return $this->tourView;
    }

}
