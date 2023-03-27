<footer>
    <section id="sitemap">
        <h2>Navigation</h2>
            <div class="footerNav">
                <nav class="navfooterbloc">
                    <h3>Menu principal</h3>
                    <?php echo $menuMain_view?>
                </nav>
                <nav class="navfooterbloc">
                    <h3>Ils en parlent</h3>
                    <a href="https://youtu.be/h3RULNKo_wo" target="_blank" rel="noopener noreferrer">TASTED by Andreas Larsson</a>
                    <a href="https://www.hachette-vins.com/guide-vins/producteurs/47281/scea-behaghel-ch-peyrines/" target="_blank" rel="noopener noreferrer">Hachette vins</a>
                    <a href="https://www.concourslyon.com/wine/33873/chateau-peyrines" target="_blank" rel="noopener noreferrer">Concours international de lyon</a>
                    <a href="https://agriculture.gouv.fr/la-haute-valeur-environnementale-une-mention-valorisante-pour-les-agriculteurs-et-leurs-pratiques" target="_blank" rel="noopener noreferrer">Haute valeur environnementale</a>
                </nav>
            </div>
    </section>
    <nav id="menuRS" class="nav-rs">
        <?php 
                    foreach($menuRS as $item){
                        echo "<a href=".$item->page." title='".$item->titre."' target='_blank'><div class='rs ".$item->titre."'></div></a>";
                    }
        ?>
    </nav>
</footer>