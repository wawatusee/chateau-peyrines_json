<!DOCTYPE html>
<?php require_once("../config/config.php");?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/style.css">
    <script type="text/javascript" src="js/menu.js"></script>
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
            <h2>Présentation du site</h2>
            <p>Sur ce site:</p>
            <ul>
                <li>La présentation du site Chateau-Peyrines(cette page)</li>
                <li>Les produits de Chateau Peyrines, à savoir principalement du vin, vendu dans différents contenus et les moyens mis à disposition pour vous les approprier.</li>
                <li>La carte et les dates de tournée de Chateau Peyrines</li>
                <li>Les moyens de contacter un des travailleurs ou intervenants de chateau Peyrines</li>
                <li>En bas de chaque page, une série de liens cliquables, vous permet d'accéder à différentes rubriques, si vous êtes concernés, vous savez sur lesquelles cliquer.</li>
            </ul>
        </section>
        <section id="vins-tarifs">
            <h1>Catalogue</h1>
            <h2>Tarifs des vins</h2>
            <?php
            require_once("../model/catalog_model.php");
            //Chargement du catalogue depuis la base de données
            $catalog=new CatalogModel("../json/catalog.json");
            $catalogueProducts=$catalog->getCatalog();
            var_dump($catalogueProducts);
            //Affichage du catalogue
            require_once("../view/catalog_view.php");
            $categoriesCatalog=$catalogueProducts->catalog;
            var_dump($categoriesCatalog);
            echo $categoriesCatalog[0]->showroom;
            $viewCatalog=new CatalogView;
            $showRoomView=$viewCatalog->getView($categoriesCatalog);
            echo $showRoomView;
            ?>
            <section>
                <h3>Vins Blancs et Rosé</h3>
                    <section class="showcase">
                        <div class="product">
                            <div class="desc" >
                                <details>
                                    <summary>2021 Blanc sec  A.O.C Entre-deux-Mers Haut Benauge</summary>
                                    <p>élaboré avec un assemblage des 3  cépages  sémillion, sauvignon, et muscadelle</p>
                                </details>
                            </div>
                            <div class="prices">
                                <div class="price">
                                    <div class="packaging">+36btl</div>
                                    <div class="cost">7.80 €</div>
                                </div>
                                <div class="price">
                                    <div class="packaging">+60btl</div>
                                    <div class="cost">7.50 €</div>
                                </div>
                                <div class="price">
                                    <div class="packaging">+72btl</div>
                                    <div class="cost">7.30 €</div>
                                </div>
                            </div>
                        </div>
                        <div class="product">
                            <div class="desc" >
                                2020 Blanc Moelleux A.O.C Bordeaux Haut Benauge
                            </div>
                            <div class="prices">
                                <div class="price">
                                    <div class="packaging">+36btl</div>
                                    <div class="cost">8.20 € </div>
                                </div>
                                <div class="price">
                                    <div class="packaging">+60btl</div>
                                    <div class="cost">7.90 €</div>
                                </div>
                                <div class="price">
                                    <div class="packaging">+72btl</div>
                                    <div class="cost">7.70 €</div>
                                </div>
                            </div>
                        </div>
                        <div class="product">
                            <div class="desc" >
                                2021 Rosé A.O.C Bordeaux
                            </div>
                            <div class="prices">
                                <div class="price">
                                    <div class="packaging">+36btl</div>
                                    <div class="cost">7.80 € </div>
                                </div>
                                <div class="price">
                                    <div class="packaging">+60btl</div>
                                    <div class="cost">7.50 €</div>
                                </div>
                                <div class="price">
                                    <div class="packaging">+72btl</div>
                                    <div class="cost">7.30 €</div>
                                </div>
                            </div>
                        </div>
                        <div class="product">
                            <div class="desc" >
                                Méthode Traditionnelle:''Les Bulles de Peyrines''
                            </div>
                            <div class="prices">
                                <div class="price">
                                    <div class="packaging">+36btl</div>
                                    <div class="cost">11.20 € </div>
                                </div>
                                <div class="price">
                                    <div class="packaging">+60btl</div>
                                    <div class="cost">10.90 €</div>
                                </div>
                                <div class="price">
                                    <div class="packaging">+72btl</div>
                                    <div class="cost">10.70 €</div>
                                </div>
                            </div>
                        </div>
                    </section>
            </section>
            <section>
            <h3>Vins Rouges</h3>
                <section class="showcase">
                            <div class="product">
                                <div class="desc" >
                                    2015 Rouge A.O.C Bordeaux Supérieur
                                </div>
                                <div class="prices">
                                    <div class="price">
                                        <div class="packaging">+36btl</div>
                                        <div class="cost">8.20 €</div>
                                    </div>
                                    <div class="price">
                                        <div class="packaging">+60btl</div>
                                        <div class="cost">7.90 €</div>
                                    </div>
                                    <div class="price">
                                        <div class="packaging">+72btl</div>
                                        <div class="cost">7.70 €</div>
                                    </div>
                                </div>
                            </div>
                            <div class="product">
                                <div class="desc" >
                                    2016 Rouge A.O.C Bordeaux Supérieur
                                </div>
                                <div class="prices">
                                    <div class="price">
                                        <div class="packaging">+36btl</div>
                                        <div class="cost">8.10 € </div>
                                    </div>
                                    <div class="price">
                                        <div class="packaging">+60btl</div>
                                        <div class="cost">7.80 €</div>
                                    </div>
                                    <div class="price">
                                        <div class="packaging">+72btl</div>
                                        <div class="cost">7.60 €</div>
                                    </div>
                                </div>
                            </div>
                            <div class="product">
                                <div class="desc" >
                                    Magnum (150cl) 2015 Rouge A.O.C Bordeaux Supérieur
                                </div>
                                <div class="prices">
                                    <div class="price">
                                        <div class="packaging">Magnum</div>
                                        <div class="cost">18.50 €</div>
                                    </div>
                                </div>
                            </div>
                        </section>
                </section>
            <section>
            <h3>Vin Rouge en vrac</h3>
                <section class="showcase">
                            <div class="product">
                                <div class="desc" >
                                    2018 Rouge A.O.C Bordeaux par 1 ou 2 colis
                                </div>
                                <div class="prices">
                                    <div class="price">
                                        <div class="packaging">Cubit de 22 litres</div>
                                        <div class="cost">99.00 €</div>
                                    </div>
                                    <div class="price">
                                        <div class="packaging">Fontaine de 20 litres</div>
                                        <div class="cost">90.00 €</div>
                                    </div>
                                    <div class="price">
                                        <div class="packaging">Fontaine de 10 litres</div>
                                        <div class="cost">46.00 €</div>
                                    </div>
                                </div>
                            </div>
                            <div class="product">
                                <div class="desc" >
                                    2018 Rouge A.O.C Bordeaux par 3 colis ou plus
                                </div>
                                <div class="prices">
                                    <div class="price">
                                        <div class="packaging">Cubit de 22 litres</div>
                                        <div class="cost">84.70€</div>
                                    </div>
                                    <div class="price">
                                        <div class="packaging">Fontaine de 20 litres</div>
                                        <div class="cost">77.00 €</div>
                                    </div>
                                    <div class="price">
                                        <div class="packaging">Fontaine de 10 litres</div>
                                        <div class="cost">39.95 €</div>
                                    </div>
                                </div>
                            </div>
                </section>
            </section>
        </section>
        <section id="tournee">
            <h1>Tournée</h1>
            <section>
                
            </section>
        </section>
    </main>
    <footer>
        <?php require_once('../inc/footer.php')?>
    </footer>
</body>
</html>