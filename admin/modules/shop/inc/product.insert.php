<?php
	session_start();
	
	function __autoload($class){
		include_once "./../../../inc/class.".$class.".php";
	}
	
	require_once "./../../../config.php";
	ini_set("display_errors", 1);
	ini_set('log_errors', 1);
	ini_set('error_log', $config['root_dir'].'/logs/php_errors.txt');
	try{
		$conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);
	
	
		$auth = new Authenticate($conn);
		if(!$auth->isLogined()){  
			exit('Boli ste odhlaseny zo systemu.');
		} 
		
		if(strlen($_GET['title_sk']) == 0){
			header("Location: http://".$_SERVER['HTTP_HOST']."/admin/index.php?m=shop&c=product&sp=new");
		}
		
		$conn->insert("INSERT INTO `shop_product` (`author`, `create`, `title_sk`) VALUES (? ,".time().", ?)", array( $_SESSION['id'], trim($_GET['title_sk']) ));
		
		header("Location: http://".$_SERVER['HTTP_HOST']."/admin/index.php?m=shop&c=product&sp=edit&pid=".$conn->getInsertId());
		exit();
		
	}catch(MysqlException $e){
		exit('Nastala neocakavana chyba, operaciu prosim zopakujte.');
	}
	
?>