<!DOCTYPE html>
<?php require_once("../config/config.php");?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="00xcLmYAyZXyMNUqTeggzhT2lDVH5gSZxB2BCkXpu4A" />
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
     integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI="
     crossorigin="">
    <script src="js/menu.js"></script>
    <link rel="shortcut icon" type="image/png" href="img/favicon.ico">
    <title><?php echo $titleWebSite[0]?></title>
</head>
<body>
        <!--Inclusion du header -->
    <?php require_once('../inc/header.php')?>
    <div id="navigation">
        <!--Inclusion du menu -->
        <?php require_once('../inc/nav.php')?>
    </div>
    <main>
        <section id="accueil">
            <h2>Accueil</h2>
            <section class="information">
                <h3>Présentation du site</h3>
                    <p>Château peyrines n'est pas un chateau, bien qu'il y ait eu un chateau autrefois, aujourd'hui vous y trouverez un domaine viticole. Sur ce domaine viticole, vous pourrez nous y voir, nous sommes des producteurs récoltant du Cabernet, du Sauvignon avec lesquels nous élaborons des vins.</p>
                    <p>Quels vins? Du vin blanc sec, du rouge supérieur(c'est comme ça qu'il est appelé), des Bulles de Peyrines(qui ne s'appelle pas Champagne parce que ce n'est pas comme ça qu'on l'appelle), du vin rosé, du vin blanc sec et puisque l'on nomme tout le monde on l'appelle Haut Benauge.
                        <br>Pour avoir plus de détails sur le moyen d'accéder à tous nos trésors voici un lien vers notre <a href="#vins-tarifs"><img class="picto-lien" src="/public/img/logo_bouteille-web.png" alt="dessin de bouteille"> Cataloque</a>.
                    </p>
                    <p>Nous sommes producteurs récoltant, je l'ai déja dit, c'est état de fait ne nous empêche pas de nous déplacer. Avec une camionette nous transportons quelques unes de nos bouteilles à divers endroits en France à divers moments <br>
                        Afin que vous puissiez connaitre les dates de nos dégustations-vente, livraison et autres déplacements voici un lien vers les dates de la <a href="#tournee"><img class="picto-lien" src="/public/img/logo_tour-web.png" alt="dessin de camionette">tournée</a>. </p>
                    <p>Si enfin, vous désirez, nous parler, nous écrire ou venir nous voir, voici un autre lien vers la page <a href="#contact">Contact</a></p>
                    <p>Dans le pied de site, une série de liens cliquables, vous permet d'accéder à différentes rubriques, si vous êtes concernés, vous savez sur lesquelles cliquer.</p>
            </section>
        </section>
        <section id="catalogue">
            <h2>Catalogue</h2>
            <section id="vins-tarifs">
            <h3>Tarifs des vins</h3>
            <p>Téléchargement tarifs 2022-2023 <a href="/public/docs/peyrines-tarifs-expeditions-22-2023.pdf">tarifs expéditions-2022-2023</a></p>
            <?php
            require_once("../model/catalog_model.php");
            //Chargement du catalogue depuis la base de données
            $catalog=new CatalogModel("../json/catalog.json");
            $catalogFull=$catalog->getCatalog();
            $categoriesCatalog=$catalogFull->catalog;
            //Affichage du catalogue
            require_once("../view/catalog_view.php");
            $viewCatalog=new CatalogView;
            $showRoomView=$viewCatalog->getView($categoriesCatalog);
            echo $showRoomView;
            ?>
            </section>
            <fieldset class="conditionsvente">
                <legend>Conditions de vente</legend>
                <p>La livraison se fait à partir de 36 bouteilles.</p>
                <p>Pour profiter du Tarif dégressif, commandez avec des ami(e)s et faites expédier à une seule adresse.</p>
                <p>Ces prix s’entendent Toutes Taxes Comprises et Transport Compris pour la France Métropolitaine.</p>
                <p>Règlement à la commande par chèque , à l’ordre de : SCEA BEHAGHEL Ou par Virement Bancaire sur demande du RIB.</p>
                <p>Ces tarifs sont valables de : Octobre 2022 à Septembre 2023</p>
                <p>L’abus d’alcool est dangereux pour la santé. À consommer avec modération.</p>
            </fieldset>
        </section>
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
                <h3>Carte</h3>
                <div id="map">
                <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
                <!--Ici la carte des lieux d'events (leaflet+openmap)-->
                </div>
                <h3>Dates</h3>
                <!--/*Liste des dates des dits events(tableau)*/-->
                <?php require_once("../view/tour_view.php");
                $viewTour=new TourView;
                $displayDates= $viewTour->getView($tourFull->events);
                echo $displayDates;?>
                <script src="js/tour.js"></script>
            </article>
        </section>
        <section id="contact">
            <h2>Contact</h2>
            <div class="information">
                <p>Adresse : Chateau Peyrines, 33410 Mourens, France <br>Visite & dégustation sur place</p>
                <p>Téléphone :+33 05 56 61 98 05</p>
                <p>Mail : contact@chateau-peyrines.com</p>
            </div>
        </section>
    </main>
    <!--Inclusion du footer -->
    <?php require_once('../inc/footer.php')?>
</body>
</html>