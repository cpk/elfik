<?php
	session_start();
        
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
		$conf = getConfig($conn, "config", "full");
		$conf['s_currency'] = str_replace("EUR", "&euro;", $conf['s_currency']);
		
                $cart = new Cart($conf['s_currency'], $conf['s_dph']);
                
		switch($_GET['act']){
			
			case 1 :
				$cart->addProduct($_GET['pid']);
				$cart->calculate();
				$data =  array( "price" =>  $cart->getTotalPriceWithCurrencyAndDPH(), "msg" => "Tovar bol úspešne vložený do košíka.", "err" => 0 );
			break;
			
			case 2 :
				$cart->updateQuantity($_GET['id'], intval($_GET['q']));
				$cart->calculate();
				$data =  array( "price" => $cart->getTotalPriceWithCurrencyAndDPH(), 
                                                "err" => 0, 
                                                "html" => sum($cart));
			break;
			
			case 3 :
				$cart->deleteItem($_GET['id']);
				$cart->calculate();
				$data =  array( "price" => $cart->getTotalPriceWithCurrencyAndDPH(), 
                                                "err" => 0, 
                                                "html" => sum($cart));
			break;
			
			case 4 :
				$price = getDeliveryPriceDPH($_GET['did'], $conf['s_dph']);
				$_SESSION['dp'] = $_GET['did'].'-'.$_GET['pid'].'-'.$price;
				$data =  array( "err" => 0 );
				echo json_encode( $data );
			exit;
			
			case 5 :
                                //print_r($_SESSION);exit;
				if(isset($_SESSION['dp'])){
					$d = explode("-", $_SESSION['dp']);
				}
				if($_GET['givenname'] == "" || 
					$_GET['surname'] == "" || 
					$_GET['city'] == "" || 
					$_GET['street'] == ""){
					$data =  array( "err" => 1, "msg" => "Nie su vyplnené povinné hodnoty." ); break;
				}elseif(!isEmail($_GET['mail'])){
					$data =  array( "err" => 1, "msg" => "E-mail je v nesprávnom tvare." ); break;
				}elseif(!isPositiveInt($_GET['zip'], 5, 5)){
					$data =  array( "err" => 1, "msg" => "PSČ je v nesprávnom tvare." ); break;
				}elseif(!isPositiveInt($_GET['mobil'], 9, 10)){
					$data =  array( "err" => 1, "msg" => "Telefén je v nesprávnom tvare." ); break;
				}elseif(intval($_GET['is_fo']) == 1 && (!isPositiveInt($_GET['ico'], 8, 8) || !isPositiveInt($_GET['dic'], 10, 10) || $_GET['company'] == "")){
					$data =  array( "err" => 1, "msg" => "Názov firmy, IČO, alebo DIČ je v nesprávnom tvare" ); break;
				}elseif(intval($_GET['diff_addr']) == 1){
					
					if($_GET['d_givenname'] == "" || 
						$_GET['d_surname'] == "" || 
						$_GET['d_city'] == "" || 
						$_GET['d_street'] == ""){
						$data =  array( "err" => 1, "msg" => "Nie su vyplnené povinné hodnoty doručenia tovaru." ); break;
					}elseif(!isPositiveInt($_GET['d_zip'], 5, 5)){
						$data =  array( "err" => 1, "msg" => "PSČ v adrese dručenia je v nesprávnom tvare." ); break;
					}elseif($_GET['d_mobil'] != "" && !isPositiveInt($_GET['d_mobil'], 9, 10)){
						$data =  array( "err" => 1, "msg" => "Telefén je v nesprávnom tvare." ); break;
					}
				}elseif(count($d) != 3){
					$data =  array( "err" => 1, "msg" => "Nie je zovolená doprava a platba." ); break;
				}
				
			$cart->calculate();	
			$conn->insert("INSERT INTO `shop_order` (`id_shop_delivery`, `id_shop_payment`, `create`, `ip`, `dph`, `id_shop_currency`, `givenname`, `surname`, `mobil`, `street`, `city`, `company`, `ico`, `dic`, `d_givenname`, `d_surname`, `d_company`, `d_mobil`, `d_street`, `d_city`, `d_zip`, `zip`, `mail`, `price_delivery`) 
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
			array($d[0], $d[1], time(),$_SERVER['REMOTE_ADDR'], $conf["s_dph"], 1, $_GET['givenname'], $_GET['surname'], $_GET['mobil'], $_GET['street'], $_GET['city'], $_GET['company'], $_GET['ico'], $_GET['dic'], $_GET['d_givenname'], $_GET['d_surname'], $_GET['d_company'], $_GET['d_mobil'], $_GET['d_street'], $_GET['d_city'], $_GET['d_zip'], $_GET['zip'], $_GET['mail'], getDeliveryPrice($d[0]) ));
			
			$oid = $conn->getInsertId();
				$rows = array();
				
				foreach($_SESSION['cart'] as $ids => $info){
					$id = explode("-", $ids);
					$price = explode("-", $info);
					$rows[] = "(".$oid.", ".$id[0].", ".$price[1].", ".$id[1].", ".$price[0].")";
                                        $conn->update("UPDATE `shop_product` SET `store_in`=`store_in`-?, `sold_count`=`sold_count`+? WHERE `id_shop_product`=? LIMIT 1", 
                                                        array($price[0],$price[0],$id[0]));
				}
			
			$conn->insert("INSERT INTO `shop_item` (`id_shop_order`, `id_shop_product`, `price`, `id_shop_variant`, `count`) VALUES ".implode(",", $rows));	
			sendOrderInfoMail($conf, $oid );
                        
			session_destroy();
			session_unset();
			$data =  array( "html" => '<p class="success">Objednávka bola úspešne odoslaná.</p><p class="info">O ďalšom spracovaní Vás budeme informovať e-mailom, alebo telefonicky.</p>' , "err" => 0 , 'msg' => 'Objednávka bola úspešne odoslaná.');
			break;
		
		}
		
	}catch(InvalidArgumentException $e){
			$data =  array( "msg" => $e->getMessage() , "err" => 1 );
	}catch(MysqlException $e){
			$data =  array( "msg" => "Vyskytla sa neočakávaná chyba, operáciu skúste zopakovať." , "err" => 1 );
	}catch(Exception $e){
			$data =  array( "msg" => $e->getMessage() , "err" => 1 );
	} 
	
	echo $_GET["cb"] . "(" . json_encode( $data ) . ")";