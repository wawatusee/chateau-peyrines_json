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
            <p>Présentation des produits, services et du site de Chateau Peyrines.</p>
            <section>
                <h3>Présentation du site</h3>
                <div class="presentation">
                    <a href="#accueil" class="lien"><img class="picto-lien" src="/public/img/picto-accueil.png" alt="dessin du chateau">Accueil</a><span>Bonjour à toi citoyen de plus de 18 ans, bienvenu sur le site de Château Peyrines.<br> Château Peyrines est appelé château car sur la terre où poussent nos vignes aujourd'hui, autrefois il y avait un château, celui des comtes de Peyrines. Quelques pierres sont encore visibles et les bouteilles de vin, pour le souvenir sont estampillées du blason de ces résidents féodaux. Nommer notre vignoble "Château", convenons, si vous le voulez bien que l'usage soit plutôt agréable.<br>
                Sur le site internet de Château-Peyrines.com vous trouverez un sobre descriptif de Château-Peyrines,  le catalogue des vins disponibles à la commande, les dates et lieux de la tournée et les divers moyens de contacter les acteurs de ce domaine.
                </span>
                </div>
                <div class="presentation">
                    <a href="#catalogue" class="lien"><img class="picto-lien" src="/public/img/picto-bouteille.png" alt="dessin de bouteille de vin">Catalogue</a><span>Le catalogue présente tous les vins de Château-Peyrines que vous pouvez commander : vin blanc sec, vin rouge supérieur, vin moelleux, bulles de Peyrines, vin rosé.
                    Le catalogue présente les tarifs de chacun de ces vins, selon qu'ils soient livrés ou achetés sur place, et adaptés aux quantités commandées.</span>
                </div>
                <div class="presentation">
                    <a href="#tournee" class="lien"><img class="picto-lien" src="/public/img/picto-tourne.png" alt="dessin de camionette">Tournée</a><span>Nous déplaçons avec une camionette quelques unes de nos bouteilles à divers endroits en France à divers moments <br>
                    Dégustations-vente, livraison et autres déplacements ci-dessus un lien vers les dates et lieux de cette tournée annuelle en France.</span>
                </div>
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
        <section id="contact">
            <h2>Contact</h2>
            <div class="presentation">
            <a href="https://goo.gl/maps/WXbuRqfbw21NUYKFA"><img src="/public/img/picto-map" alt="picto-map"></a><span>Adresse : Chateau Peyrines, 33410 Mourens, France <br>Visite & dégustation sur place</span>
            </div>
            <div class="presentation">
                <a href="tel:+33055661905"><img src="/public/img/picto-phone" alt="picto-phone"></a><span>Téléphone :+33 05 56 61 98 05</span>
             </div>
            <div class="presentation">
                <a href="mailto:contact@chateau-peyrines.com"><img src="/public/img/picto-mail" alt="picto-mail"></a><span>Mail : contact@chateau-peyrines.com</span>
            </div>
            
        </section>
    </main>
    <!--Inclusion du footer -->
    <?php require_once('../inc/footer.php')?>
</body>
</html>