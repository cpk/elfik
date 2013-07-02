<?php
	if(!isset($_GET['s']))	{ $_GET['s'] = 1; }else{  $_GET['s'] = intval( $_GET['s'] ); }
	
	require_once "../admin/config.php";
	require_once "../admin/inc/fnc.main.php";
	require_once "../admin/page/fnc.page.php";
	require_once "../admin/page/fnc.shop.php";
		
	function __autoload($class) {
		require_once '../admin/inc/class.'.$class.'.php';
	}

	try{
		$lang = "sk";
		$conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);	
	
	$meta = getProduct( "full", intval($_GET['id']));
	$meta =  array_merge($meta[0], getConfig($conn, "config", "full"));
	
	$meta['s_currency'] = str_replace("EUR", "&euro;", $meta['s_currency']);
	
	$html = "";
	
	if(strlen($meta['avatar1']) !=0){
		$html .= '<a href="/data/avatars/'.$meta['avatar1'].'" class="thumb"  title="'.$meta['title_sk'].' | '.printPrice($meta).'">'.printStatus($meta['id_shop_product_status']).'<img class="zoom" src="/img/zoom.png" alt="Zobraziť" />
       	<img src="/i/242-242-crop/avatars/'.$meta['avatar1'].'" alt="'.$meta['title_sk'].'" /></a>';
                
	}else{
		$html .= '<img src="/i/242-242-crop/avatars/noimage.jpg" class="nthumb"/>';
	}
	if(strlen($meta['avatar2']) > 3)
            $html .= '<a href="/data/avatars/'.$meta['avatar2'].'" class="thumb av hidden" title="'.$meta['title_sk'].' | '.printPrice($meta).'" ><!-- next photo --></a>';
        if(strlen($meta['avatar3']) > 3)
            $html .= '<a href="/data/avatars/'.$meta['avatar3'].'" class="thumb av hidden" title="'.$meta['title_sk'].' | '.printPrice($meta).'" ><!-- next --></a>';

	$html .= '<div id="mnav"><p class="label mt">cena (s DPH)</p><p class="price">'.printPrice($meta).'</p><div class="content"><strong class="head">'.$meta['title_sk'].'</strong>'. $meta['content_sk'].'</div><p class="label"><a href="/velkostne-tabulky" title="Zobraziť veľkostné tabuľky" target="_blank">veľkostné tabuľky</a>vyberte veľkosť:</p><select name="variant">'.printVariants(intval($_GET['id'])).'</select><input type="hidden" name="pid" value="'.$_GET['id'].'" /></div>';
	
		
		echo $_GET["cb"] . "(" .json_encode( array( "html" => $html, "err" => 0, "msg" => "" ) ) . ")";
	}catch(MysqlException $e){
		echo $_GET["cb"] . "(" .json_encode( array( "err" => 1, "msg" => "Vyskytla sa neočakávaná chyba, operáciu zopakujte." ) ) . ")";
	} 
?>