<?php class TourView{
 private $tourView=" ";
    public function getView(array $tourArray){
        $this->tourView.="<table class='tourtable'><thead><tr><th>Date</th><th>Lieu</th><th>Type</th><th>Evénement</th></tr></thead>";
        foreach($tourArray as $date){
            //Affichage des dates d'event
            $this->tourView.="<tr>";
            $this->tourView.="<td>".$date->date."</td>";
            $this->tourView.="<td>".$date->location->nom."</td>";
            $this->tourView.="<td>".$date->type."</td>";
            if ($date->text){
                $this->tourView.="<td>".$date->text."</td>";
            }
            else $this->tourView.="<td></td>";
            $this->tourView.="</tr>";
        }
        $this->tourView.="</table>";
        return $this->tourView;
    }

}
