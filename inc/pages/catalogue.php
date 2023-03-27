    <section id="catalogue">
        <h1>Tarifs de nos vins livrés ou au chateau</h1>
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