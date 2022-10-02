<?php
 $menuMain_model=$menuMain;
 require_once("../view/view_menus.php");
 $menusView=new ViewMenu;
 $menuMain_view=$menusView->getViewMainMenu($menuMain_model);
 ?>
<nav class="responsiveMenu" id="responsiveMenu">
        <?php
        echo $menuMain_view;
        ?>
        <a href="javascript:void(0);" class="icon" onclick="responsiveMenu()">
            <img src="img/menu-toggle-icon.png" alt="bouton menu-toggle">
        </a>
</nav>