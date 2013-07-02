<?php
	session_start();
	if(!isset($_POST['act'])){
		die('ERROR.');	
	}
	function __autoload($class){
		include_once "./../../../inc/class.".$class.".php";
	}
	
	
	require_once "./../../../config.php";
	require_once "./../../../inc/fnc.main.php";
	require_once "./fnc.shop.php";
	require_once "./setting.fnc.php";
	ini_set("display_errors", 0);
	ini_set('log_errors', 1);
	ini_set('error_log', $config['root_dir'].'/logs/php_errors.txt');

	
	try{
		$conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);
	
		$data = array( "err" => 1, "msg" => "Operaciu sa nepodarilo vykonat, skuste to znova." );
		
		$auth = new Authenticate($conn);
		if(!$auth->isLogined()){  
			$data["msg"] = "Pre dlhú nečinnosť ste boli odhlásený.";
			echo json_encode( $data ); 
			exit();
		} 
	
	if(isset($_POST['id'])) $_POST['id'] = (int)$_POST['id'];
	
	switch((int)$_POST['act']){
		
		
		// SAVE PRODUCT -------------------------------------
		case 2 :		
			if($_POST['title_sk'] == ""){
				$data['msg'] = "Názov tovaru musí byť vyplenný.";
				break;
			}elseif(!isFloat($_POST['price']) || 
					(isset($_POST['price_sale']) && !isFloat($_POST['price_sale'])) || 
					(isset($_POST['price_standard']) && !isFloat($_POST['price_standard']))){
				$data['msg'] = "Cena/y sú v nesprávnom tvare.";
				break;
			}else{
				$_POST['price'] = parseToFloat($_POST['price']);
				$_POST['price_sale'] = parseToFloat($_POST['price_sale']);
				$_POST['price_standard'] = parseToFloat($_POST['price_standard']);
			}
			if(!isPositiveInt($_POST['guarantee']) || $_POST['guarantee'] < 0 ){
				$data['msg'] = "Záruka tovaru musí byť nezáporné celé číslo.";
				break;
			}elseif(count($conn->select("SELECT `id_shop_product` FROM `shop_product` WHERE `title_sk`=? AND `id_shop_product`<>? LIMIT 1", array( $_POST["title_sk"], $_POST['id'] ))) == 1){
				$data['msg'] = "Tovar s názvom: <strong>".$_POST["title_sk"]."</strong> sa už v databáze nachádza.";
				break;
			}elseif($_POST['id_shop_category'] != 0 && !isUsed("shop_category", "id_shop_category", $_POST['id_shop_category'])){
					$data["msg"] = '<strong>Kategória</strong> do ktorej sa pokúšate zaradiť tovar neexistuje, stlačte F5.';
					break;
			}elseif(!isPositiveInt($_POST['store_in'], 1 , 5)){
				$data['msg'] = "Hodnoty skladov obsahujú neplatnú hodnotu.";
				break;
			}elseif(strlen($_POST['ean']) > 0 && !isUnique($conn, "shop_product", "ean", $_POST['ean'], $_POST['id'])){
				$data['msg'] = "EAN kód: <strong>".$_POST['ean']."</strong> sa už v databáze nachádza.";
				break;
			}	
			$_POST['active'] = (!isset($_POST['active']) || $_POST['active'] != "on" ? 0 : 1 );
			$_POST['home'] = (!isset($_POST['home']) || $_POST['home'] != "on" ? 0 : 1 );
                        $_POST['top'] = (!isset($_POST['top']) || $_POST['top'] != "on" ? 0 : 1 );
			
			$req = sqlRequest($conn, $_POST);
			$req["data"][] =   $_POST['id'];
			$conn->update('UPDATE `'.$_POST['table'].'` SET '.$req["cols"].' WHERE `id_'.$_POST['table'].'`=? LIMIT 1',$req["data"] );
			$data = array( "err" => 0, "msg" => "Zmeny boli úspešne uložené." );	 
		break;
			
		case 3:
			updateConfig($_POST, "shop_config_text");
			$data['msg']  = "Zmeny boli úspešne uložené.";
			$data['err']  = 0;
		break;	
		
	
		default :
	}
	}catch(MysqlException $ex){}
	
	echo json_encode( $data );
	
	exit();
?>