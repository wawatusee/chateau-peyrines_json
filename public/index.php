<!DOCTYPE html>
<?php require_once("../config/config.php");?>
<html lang="fr">
<?php require_once('../inc/head.php')?>
<body>
        <!--Inclusion du header -->
    <?php require_once('../inc/header.php')?>
    <div id="navigation">
        <!--Inclusion du menu -->
        <?php require_once('../inc/nav.php')?>
    </div>
    <main>
    <?php
//CONTROLEUR CENTRAL
    //$pageArray charge le tableau déclaré dans config.php,
    //Ce tableau est utilisé pour réaliser le menu(view_menu.php)
    $pagesArray = PAGE_ARRAY;
    if (isset($_GET["page"])) {
    $page = $_GET["page"];
    $titre=$page;
        if ( in_array($page, $pagesArray) ) {
        require_once '../inc/pages/' . $page . '.php';
        } else {
        require_once '../inc/pages/accueil.php';
        }
    } else {
        require_once '../inc/pages/accueil.php';
    }
//FIN DE CONTROLEUR CENTRAL
    ?>
    </main>
    <!--Inclusion du footer -->
    <?php require_once('../inc/footer.php')?>
</body>
</html>