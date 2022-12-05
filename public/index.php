<!DOCTYPE html>
<?php require_once("../config/config.php");?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/style.css">
    <script type="text/javascript" src="js/menu.js"></script>
    <script type="text/javascript" src="js/tour.js"></script>
    <title>Chateau-Peyrines</title>
</head>
<body>
    <header>
        <?php require_once('../inc/header.php')?>
    </header>
    <nav>
        <?php require_once('../inc/nav.php')?>
    </nav>
    <main role=main>
        <section id="accueil" style="padding-left:16px">
            <h1>Accueil</h1>
            <article>
                <h2>Présentation du site</h2>
                <p>Sur ce site:</p>
                <ul class="information">
                    <li>La présentation du site Chateau-Peyrines(cette page)</li>
                    <li>Les produits de Chateau Peyrines, à savoir principalement du vin, vendu dans différents contenus et les moyens mis à disposition pour vous les approprier.</li>
                    <li>La carte et les dates de tournée de Chateau Peyrines</li>
                    <li>Les moyens de contacter un des travailleurs ou intervenants de chateau Peyrines</li>
                    <li>En bas de chaque page, une série de liens cliquables, vous permet d'accéder à différentes rubriques, si vous êtes concernés, vous savez sur lesquelles cliquer.</li>
                </ul>
            </article>
        </section>
        <section id="vins-tarifs">
            <h1>Catalogue</h1>
            <h2>Tarifs des vins</h2>
            <p>Téléchargement tarifs 2022-2023 <a href="/public/docs/PEYRINES Tarifs Expéditions 22-2023.pdf">tarifs expéditions-2022-2023</a></p>
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
            <?php require_once("../model/tour_model.php");
            $tour=new TourModel("../json/tournee.json");
            $tourFull=$tour->getTour()?>
            <h1>Tournée</h1>
            <section>
                <article>
                    <h3>Carte</h3>
                    <!--Ici la carte des lieux d'events (leaflet+openmap)-->
                    <h3>Dates</h3>
                    <!--/*Liste des dates des dits events(tableau)*/-->
                   <?php require_once("../view/tour_view.php");
                   $viewTour=new TourView;
                   $displayDates= $viewTour->getView($tourFull->events);
                   echo $displayDates;
                   ?>
                </article>
            </section>
        </section>
    </main>
    <footer>
        <?php require_once('../inc/footer.php')?>
    </footer>
</body>
</html>