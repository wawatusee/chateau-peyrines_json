    <section id="tournee">
        <!--On charge les données qui alimenteront la carte et la liste d'évènements-->
        <?php require_once("../model/tour_model.php");
        $tour=new TourModel("../json/tournee.json");
        $tourFull=$tour->getTour()?>
        <h2>Tournée</h2>
        <article>
            <script id="tourData">
            <!--Récupération du json de la tournée-->
            const a_tour=<?php echo json_encode($tourFull,true)?>
            </script>
            <h3>Dates</h3>
            <div class="dates">
            <!--/*Liste des dates des dits events(tableau)*/-->
            <?php require_once("../view/tour_view.php");
            $viewTour=new TourView;
            $displayDates= $viewTour->getView($tourFull->events);
            echo $displayDates;?>
            </div>
            <h3>Carte</h3>
            <div id="map">
            <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
            <!--Ici la carte des lieux d'events (leaflet+openmap)-->
            </div>
            <script src="js/tour.js"></script>
        </article>
    </section>