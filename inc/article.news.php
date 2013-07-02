<?php
    function news(){
        global $conn, $meta, $lang;
        $pagi = 12;
        $html = "";

        $data = $conn->select("SELECT count(*) FROM `article` WHERE `active`=1 AND `sub_id`=?", array($meta['id_article']));
        $count = $data[0]["count(*)"];

        $offset = ($_GET['s'] == 1 ? 0 :  ($_GET['s'] * $pagi) - $pagi);
        $data = $conn->select("SELECT `id_article`, `sub_id`, `type`, `title_${lang}`, `header_${lang}`, `avatar1`, `create` 
                               FROM `article` 
                               WHERE `active`=1 AND `sub_id`=? 
                               ORDER BY `id_article` DESC 
                               LIMIT $offset, $pagi", array($meta['id_article']));
        
        if($pagi < $count){
            $url = ($_GET['s'] == 1 ? $_SERVER['REQUEST_URI'] : substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], "?") ) );
            $nav = new Navigator($count, $_GET['s'], $url , $pagi);
            $nav->setSeparator("?s=");
            $html .= $nav->simpleNumNavigator();
        }
        for($i = 0; $i < count($data); $i++ ){
          $html .= '<div class="news">'.
            '<img src="/i/150-150-crop/avatars/'.(strlen($data[$i]["avatar1"]) ==0 ? "noimage.jpg" : $data[$i]["avatar1"]).
            '" alt="'.$data[$i]["title_${lang}"].'" />'.
            '<strong>'.$data[$i]["title_${lang}"].'</strong><p>'.crop($data[$i]["header_${lang}"], 255).'</p>'.
            '<div class="date abs">'.strftime("%d.%m.%Y", $data[$i]['create']).
            '</div><a class="btn abs" href="'.linker($data[$i]["id_article"], 1, $lang ).'">Čítať viac</a></div>';
        }
        return $html;
}
	
	
?>
<div id="article">
	<h1>Chic fashion novinky</h1>
        <?php echo news(); ?>
</div>