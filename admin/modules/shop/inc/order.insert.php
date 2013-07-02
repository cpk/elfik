<?php
	session_start();
	
	function __autoload($class){
		include_once "./../../../inc/class.".$class.".php";
	}
	
	require_once "./../../../config.php";
	require_once "./../../../inc/fnc.main.php";
	require_once "user.fnc.php";
	require_once "fnc.shop.php";
	
	ini_set("display_errors", 1);
	ini_set('log_errors', 1);
	ini_set('error_log', $config['root_dir'].'/logs/php_errors.txt');
	try{
		$conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);
	
	
		$auth = new Authenticate($conn);
		if(!$auth->isLogined()){  
			exit('Boli ste odhlaseny zo systemu.');
		} 
		$config = array_merge($config, getConfig($conn, "`config`", "shop"));
		
		$r = validateUser($_POST);
		if(!$r['valid']){
			$_SESSION['status'] = $r['msg'];
			header("Location: http://".$_SERVER['HTTP_HOST']."/admin/index.php?m=shop&c=order&sp=new");
		}
		
		$_POST["table"] = "shop_order";
		$_POST["create"] = time();
		$_POST["dph"] = $config['s_dph'];
		$price = $conn->select("SELECT `price` FROM `shop_delivery` WHERE `id_shop_delivery`=? LIMIT 1", array($_POST['id_shop_delivery'] ));
		$_POST["price_delivery"] = $price[0]["price"];
		sqlRequest($conn, $_POST, "insert");

		header("Location: http://".$_SERVER['HTTP_HOST']."/admin/index.php?m=shop&c=order&sp=edit&oid=".$conn->getInsertId());
		exit();
		
	}catch(MysqlException $e){
		exit('Nastala neocakavana chyba, operaciu prosim zopakujte.');
	}
	
?>