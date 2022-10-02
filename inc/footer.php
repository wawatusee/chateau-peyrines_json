<footer>
    <div class="footerNav">
        <nav id="rs" class="rs">Réseaux sociaux, genre:"Face book"</nav>
        <?php var_dump($menuRS);
                    foreach($menuRS as $item){
                        echo "<a href=".$item->page.">".$item->titre."</a>";
                    }
        ?>
        <nav class="mainNav"></nav>
        <nav class="extNav"></nav>
    </div>
</footer>