<?php
$menuMain_model = $menus->getMenu("Main_menu");
require_once("../view/view_menus.php");
$menusView = new ViewMenu;
$menuMain_view = $menusView->getViewMainMenu($menuMain_model, $singlePage);
?>

<nav class="main-nav">
    <div class="menu-container">
        <?php echo $menuMain_view; ?>
        <button class="menu-toggle" onclick="responsiveMenu()">
            <img src="img/menu-toggle-icon-rouge.png" alt="Ouvrir le menu">
        </button>
    </div>
</nav>