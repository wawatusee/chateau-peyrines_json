<footer>
    <div class="footerNav">
        <nav class="mainNav">
            <h4>Menu principal</h4>
            <?php echo $menuMain_view?>
        </nav>
        <nav class="extNav">
            <h4>Liens extérieurs</h4>
            Liens extérieurs
        </nav>
    </div>
    <nav id="menuRS" class="nav-rs">
        <?php 
                    foreach($menuRS as $item){
                        echo "<a href=".$item->page." target='_blank'><div class='rs ".$item->titre."'></div></a>";
                    }
        ?>
    </nav>
</footer>