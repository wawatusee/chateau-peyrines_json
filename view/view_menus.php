<?php
class ViewMenu {

    public function getViewMainMenu(array $menuArray, $singlePage = false) {

        $html = '<ul class="menu-list">';

        foreach ($menuArray as $item) {

            $href = $singlePage 
                ? '#'.$item->page 
                : '?page='.$item->page;

            $active = (isset($_GET['page']) && $_GET['page'] === $item->page)
                ? ' class="active"'
                : '';

            $html .= "<li><a href=\"$href\"$active>{$item->titre}</a></li>";
        }

        $html .= '</ul>';

        return $html;
    }
}
