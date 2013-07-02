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
		$data = $conn->select("SELECT `map` FROM `shop_delivery` WHERE `id_shop_delivery`=? LIMIT 1", array( $_GET['id'] ));
	
		echo $_GET["cb"] . "(" .json_encode( array( "err" => 0, "html" => $data[0]['map'] ) ) . ")";
	}catch(MysqlException $e){
		echo $_GET["cb"] . "(" .json_encode( array( "err" => 1, "msg" => "Vyskytla sa neočakávaná chyba, operáciu zopakujte." ) ) . ")";
	} 
?>