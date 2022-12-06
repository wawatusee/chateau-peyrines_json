<?php class TourView{
 private $tourView=" ";
    public function getView(array $tourArray){
        foreach($tourArray as $date){
            //Affichage de chaque date d'event
            $this->tourView.="<h3>".$date->date."</H3>";
        }
        return $this->tourView;
    }

}
