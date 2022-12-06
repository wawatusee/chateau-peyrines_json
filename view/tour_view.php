<?php class TourView{
 private $tourView=" ";
    public function getView(array $tourArray){
        $this->tourView.="<table><thead>Tournée</thead>";
        foreach($tourArray as $date){
            //Affichage de chaque date d'event
            $this->tourView.="<tr>";
            $this->tourView.="<td>".$date->date."</td>";
            $this->tourView.="<td>".$date->location."</td>";
            $this->tourView.="<td>".$date->type."</td>";
            if ($date->text){
                $this->tourView.="<td>".$date->text."</td>";
            }
            $this->tourView.="</tr>";
        }
        $this->tourView.="</table>";
        return $this->tourView;
    }

}
