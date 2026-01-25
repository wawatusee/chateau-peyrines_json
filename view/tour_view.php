<?php
class TourView
{
    private $tourView = "";
    private $eventTypes;

    public function __construct($eventTypes)
    {
        $this->eventTypes = $eventTypes;
    }

    /*public function getView(array $tourArray)
    {
        $this->tourView .= "<table class='tourtable'><thead><tr><th>Date</th><th>Lieu</th><th>Type</th><th>Événement</th></tr></thead><tbody>";
        foreach ($tourArray as $event) {
            $this->tourView .= "<tr>";
            $this->tourView .= "<td>" . htmlspecialchars($event->date) . "</td>";
            $this->tourView .= "<td>" . htmlspecialchars($event->location->nom) . "</td>";

            // Conversion de l'ID technique en libellé
            $typeName = $event->type;
            if (is_array($this->eventTypes)) {
                foreach ($this->eventTypes as $type) {
                    if ($type['id'] === $event->type) {
                        $typeName = $type['name'];
                        break;
                    }
                }
            }
            $this->tourView .= "<td>" . htmlspecialchars($typeName) . "</td>";
            $this->tourView .= "<td>" . ($event->text ? htmlspecialchars($event->text) : '') . "</td>";
            $this->tourView .= "</tr>";
        }
        $this->tourView .= "</tbody></table>";
        return $this->tourView;
    }
}*/
public function getView(array $tourArray) {
    $this->tourView .= "<table class='tourtable'><thead><tr><th>Date</th><th>Lieu</th><th>Type</th><th>Détails</th></tr></thead><tbody>";

    foreach($tourArray as $event) {
        // Afficher uniquement les événements confirmés (état = 1)
        if (isset($event->état) && $event->état == '1') {
            $this->tourView .= "<tr>";
            $this->tourView .= "<td>" . htmlspecialchars($event->date) . "</td>";
            $this->tourView .= "<td>" . htmlspecialchars($event->location->nom) . "</td>";

            // Conversion de l'ID technique en libellé
            $typeName = $event->type;
            foreach ($this->eventTypes as $type) {
                if ($type['id'] === $event->type) {
                    $typeName = $type['name'];
                    break;
                }
            }
            $this->tourView .= "<td>" . htmlspecialchars($typeName) . "</td>";
            $this->tourView .= "<td>" . ($event->text ? htmlspecialchars($event->text) : '') . "</td>";
            $this->tourView .= "</tr>";
        }
    }
    $this->tourView .= "</tbody></table>";
    return $this->tourView;
}
}
?>