<footer>
    <div class="footerNav">
        <nav class="navfooterbloc">
            <h4>Menu principal</h4>
            <?php echo $menuMain_view?>
        </nav>
        <nav class="navfooterbloc">
            <h4>Ils en parlent</h4>
            <a href="https://youtu.be/h3RULNKo_wo" target="_blank" rel="noopener noreferrer">TASTED by Andreas Larsson</a>
            <a href="https://www.hachette-vins.com/guide-vins/producteurs/47281/scea-behaghel-ch-peyrines/" target="_blank" rel="noopener noreferrer">Hachette vins</a>
            <a href="https://www.concourslyon.com/wine/33873/chateau-peyrines" target="_blank" rel="noopener noreferrer">Concours international de lyon</a>
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