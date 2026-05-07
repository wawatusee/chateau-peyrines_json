<section id="tournee">
    <!-- On charge les données qui alimenteront la carte et la liste d'évènements -->
    <?php
    // Chemins relatifs depuis inc/pages/tournee.php
    require_once("../model/tour_model.php");
    $tour = new TourModel("../json/tournee.json");
    $tourFull = $tour->getTour();

    // Chargement des types d'événements
    $typesData = [];
    $typesPath = "../admin/json/events_types.json";
    if (file_exists($typesPath)) {
        $typesData = json_decode(file_get_contents($typesPath), true);
    } else {
        error_log("Fichier $typesPath introuvable.");
    }
    ?>
    <script id="typesData">
        const EVENT_TYPES = <?php echo json_encode($typesData ?? []); ?>;
    </script>
    <script id="tourData">
        const a_tour = <?php echo json_encode($tourFull, true); ?>
    </script>

    <h2>Tournée</h2>
    <article>
        <h3>Dates</h3>
        <div class="dates">
            <?php
            require_once("../view/tour_view.php");
            $viewTour = new TourView($typesData);
            echo $viewTour->getView($tourFull->events);
            ?>
        </div>
        <h3>Carte</h3>
        <div id="map">
            <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" crossorigin=""></script>
        </div>
        <script src="js/tour.js"></script> <!-- Chemin relatif à public/ -->
    </article>
</section>