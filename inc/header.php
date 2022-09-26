<?php
 require_once("../config/config.php");
 $menuMain_model=$menuMain;
 ?>

<nav class="responsiveMenu" id="responsiveMenu">
        <?php foreach($menuMain_model as $item){
            echo "<a href=".$item->page.">".$item->titre."</a>";
        }?>
        <a href="javascript:void(0);" class="icon" onclick="responsiveMenu()">
            <img src="img/menu-toggle-icon.png" alt="bouton menu-toggle">
        </a>
</nav>