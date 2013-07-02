<?php
	session_start();
	
	
	function __autoload($class){
		include_once "./../../../inc/class.".$class.".php";
	}
	
	
	require_once "./../../../config.php";
	require_once "./../../../inc/fnc.main.php";
	require_once "./fnc.shop.php";
	require_once "./order.fnc.php";
	
	ini_set("display_errors", 1);
	ini_set('log_errors', 1);
	ini_set('error_log', $config['root_dir'].'/logs/php_errors.txt');

	
	
	try{
		$conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);
		
		$config = array_merge($config, getConfig($conn, "`config`", $type = "shop"));
		
		if(is_file("./print/".$_GET['type'].".php")) { 
			include "./print/".$_GET['type'].".php";
		}
		
	}catch(MysqlException $ex){
		exit('DB ERROR.');
	}
	
	
?>