<?php class TourView{
 private $tourView=" ";
    public function getView(array $tourArray){
        $this->tourView.="<table class='tourtable'><thead>Tournée</thead>";
        foreach($tourArray as $date){
            //Affichage des dates d'event
            $this->tourView.="<tr>";
            $this->tourView.="<td>".$date->date."</td>";
            $this->tourView.="<td>".$date->location."</td>";
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
