<?php
	session_start();
	if(!isset($_GET['act'])){
		die('ERROR.');	
	}
	function __autoload($class){
		include_once "./../../../inc/class.".$class.".php";
	}
	
	
	
	require_once "./../../../config.php";
	require_once "./../../../inc/fnc.main.php";
	require_once "./fnc.shop.php";
	require_once "./product.fnc.php";
	require_once "./category.fnc.php";
	require_once "./user.fnc.php";
	require_once "./order.fnc.php";
	require_once "./setting.fnc.php";
	
	ini_set("display_errors", 1);
	ini_set('log_errors', 1);
	ini_set('error_log', $config['root_dir'].'/logs/php_errors.txt');
	
	try{
		$conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);
	
		$data = array( "err" => 1, "msg" => "Operaciu sa nepodarilo vykonat, skuste to znova." );
		
		$auth = new Authenticate($conn);
		if(!$auth->isLogined()){  
			$data["msg"] = "Pre dlhú nečinnosť ste boli odhlásený.";
			echo $_GET["cb"] . "(" .json_encode( $data ) . ")"; 
			exit();
		} 
		
		$config = array_merge($config, getConfig($conn, "`config`", "full"));
		$config["adminPagi"] = $config["s_adminPagi"];
	
		if(isset($_GET['id'])) $_GET['id'] = (int)$_GET['id'];
	
	switch((int)$_GET['act']){
		
		
		// SHOP SEARCH -------------------------------------
		case 1 :
                    switch($_GET['table']){
                        case  "shop_product" :
                                $data['html'] = printProducts($conn, null, $_GET['q'], 1);
                        break;
                        case  "shop_manufacturer" :
                                $data['html'] = printManufacturers($conn, $_GET['q'], 1);
                        break;
                        case  "user" :
                                $data['html'] = printCustomers($conn, $_GET['q'], 1);
                        break;

                    }
                    $nav = new Navigator($config['count'], 1 , './index.php?q='.$_GET['q'].'&amp;'.preg_replace("/&s=[0-9]/", "", $_GET['url']) , $config["adminPagi"]);
                    $nav->setSeparator("&amp;s=");
                    $data['pagi'] = $nav->smartNavigator();
                    $data['err'] = 0;
		break;
		
		
		// ADD new Variant -------------------------------------
		case 2 :
                    if(strlen($_GET['shop_variant_name']) == 0 || strlen($_GET['shop_variant_name']) > 100){
                            $data["msg"] = "Maximálna dĺžka názvu je 100 znakov.";
                            break;
                    }elseif($_GET["price"] != "" && !isFloat($_GET["price"])){
                            $data["msg"] = "Cena nie je v správnom tvare.";
                            break;
                    }elseif($_GET["weight"] != "" && !isFloat($_GET["weight"], 3)){
                            $data["msg"] = "Hnotnosť nie je v správnom tvare.";
                            break;
                    }
                    $_GET["price"] = parseToFloat($_GET["price"]);
                    $_GET["weight"] = parseToFloat($_GET["weight"]);
                    sqlRequest($conn, $_GET, "insert");
                    $id = $conn->getInsertId();
                    $data['append'] =  printVariants($conn, $_GET['id_shop_product'], $id);
                    $data['selector'] = ".shop_variant";
                    $data["err"] = 0;
                    $data["msg"] = "Úspešne pridané";
		break;
		
		// INLINE EDITING update -------------------------------------
		case 3 :
				switch($_GET['table']){
				// varinaty -
				case  "shop_variant" : 
					if(strlen($_GET['shop_variant_name']) == 0 || strlen($_GET['shop_variant_name']) > 100){
						$data["msg"] = "Maximálna dĺžka názvu je 100 znakov.";
						break;break;
					}elseif($_GET["price"] != "" && !isFloat($_GET["price"])){
						$data["msg"] = "Cena nie je v správnom tvare.";
						break;break;
					}elseif($_GET["weight"] != "" && !isFloat($_GET["weight"], 3)){
						$data["msg"] = "Hnotnosť nie je v správnom tvare.";
						break;break;
					}
					$_GET["price"] = parseToFloat($_GET["price"]);
					$_GET["weight"] = parseToFloat($_GET["weight"]);
				break;
				// attributes -
				case  "shop_attr" :
					if(strlen($_GET['key']) == 0 || strlen($_GET['key']) > 40){
						$data["msg"] = "Maximálna dĺžka kľúča je 40 znakov.";
						break;break;
					}elseif($_GET['val'] != "" && strlen($_GET['val']) > 100){
						$data["msg"] = "Maximálna dĺžka hodnoty je 40 znakov.";
						break;break;
					}
				break;
				// Manufacturer -
				case "shop_manufacturer" :
					if(strlen($_GET['shop_manufacturer_name']) == 0 || strlen($_GET['shop_manufacturer_name']) > 50){
						$data["msg"] = "Maximálna dĺžka názvu je 50 znakov.";
						break;break;
					}elseif(isset($_GET['web']) &&  strlen($_GET['web']) > 50 ){
						$data["msg"] = "Maximálna dĺžka názvu web stránky je 50 znakov.";
						break;break;
					}
				break;	
				// Status -
				case "shop_product_status" :
					if(strlen($_GET['shop_product_status_name']) == 0 || strlen($_GET['shop_product_status_name']) > 150){
						$data["msg"] = "Maximálna dĺžka nzvu je 150 znakov.";
						break;break;
					}elseif(isset($_GET['label']) &&  strlen($_GET['label']) > 250 ){
						$data["msg"] = "Maximálna dĺžka popisu web stránky je 250 znakov.";
						break;break;
					}
				break;
				// avaibility -
				case "shop_product_avaibility" :
					if(strlen($_GET['shop_product_avaibility_name']) == 0 || strlen($_GET['shop_product_avaibility_name']) > 150){
						$data["msg"] = "Maximálna dĺžka nzvu je 150 znakov.";
						break;break;
					}elseif(isset($_GET['label']) &&  strlen($_GET['label']) > 250 ){
						$data["msg"] = "Maximálna dĺžka popisu web stránky je 250 znakov.";
						break;break;
					}
				break;
				// shop_category
				case "shop_category" :
					if(strlen($_GET['category_name']) == 0 || strlen($_GET['category_name']) > 100){
						$data["msg"] = "Maximálna dĺžka názvu kategórie je 100 znakov.";
						break;break;
					}elseif(strlen($_GET['label']) > 0 && strlen($_GET['label']) > 255){
						$data["msg"] = "Maximálna dĺžka popisu kategórie je 255 znakov.";
						break;break;
					}elseif(!isUniqueCateg($conn, $_GET['id'], $_GET['category_name'], $_GET['sub_id']) ){
						if($_GET['id'] != 0) { 
							$name = getCategoryById($conn, $_GET['id']); 
						}
						$data["msg"] = "Podkategória s názvom <strong>".$_GET['category_name'].
									'</strong> sa už v '.($_GET['id'] == 0 ? 'hlavnej kategórii' : $name[0]['category_name']).' nachádza.';
						break;break;
					}
					$_GET['link_sk'] = SEOlink($_GET['category_name']);
				break;
				
				case "shop_delivery" :
					if(strlen($_GET['delivery_name']) == 0 || strlen($_GET['delivery_name']) > 100){
						$data["msg"] = "Maximálna dĺžka názvu je 100 znakov.";
						break;break;
					}elseif(isset($_GET['label']) &&  strlen($_GET['label']) > 250 ){
						$data["msg"] = "Maximálna dĺžka popisu dostupnosti je 250 znakov.";
						break;break;
					}elseif($_GET["price"] != "" && !isFloat($_GET["price"])){
						$data["msg"] = "Cena nie je v správnom tvare.";
						break;break;
					}
					$_GET["price"] = parseToFloat($_GET["price"]);
				break;
				
				case "shop_order" :
					$_GET['editor'] = $_SESSION['id'];
					$_GET['edit'] = time();
					
					if(isset($_GET['id_shop_delivery'])){
						$dPrice = $conn->select("SELECT `price` FROM `shop_delivery` WHERE `id_shop_delivery`=? LIMIT 1", array($_GET['id_shop_delivery'] ));
						$sql = ", `price_delivery`=".parseToFloat($dPrice[0]['price']);
					}
				break;
				
				}
					
				$req = sqlRequest($conn, $_GET);
				$req["data"][] =   $_GET['id'];
				$conn->update('UPDATE `'.$_GET['table'].'` SET '.$req["cols"].' '.(isset($sql)? $sql : '').' WHERE `id_'.$_GET['table'].'`=? LIMIT 1',$req["data"] );
				if(isset($_GET['sale']) && isPositiveInt($_GET['sale'], 1 ,3)){
					$orderInfo = getOrderInfo($_GET['id']);
					$data['html'] = printOrderItems($conn, $_GET['id'] , $orderInfo["shop_currency_name"], $orderInfo["dph"], $orderInfo["id_shop_delivery"], $orderInfo["price_delivery"] );
					$data['pagi'] =  printSUMofOrder($orderInfo["dph"], $orderInfo["sale"], $orderInfo["id_shop_delivery"], $orderInfo["price_delivery"]);
					$data['selector'] = "tbody.shop_item";
				}
				if(isset($_GET['id_shop_delivery'])){
					$orderInfo = getOrderInfo($_GET['id']);
					$data['html'] = printOrderItems($conn, $_GET['id'] , $orderInfo["shop_currency_name"], $orderInfo["dph"], $orderInfo["id_shop_delivery"], $orderInfo["price_delivery"] );
					$data['pagi'] =  printSUMofOrder($orderInfo["dph"], $orderInfo["sale"], $orderInfo["id_shop_delivery"], $orderInfo["price_delivery"]);
					$data['selector'] = "tbody.shop_item";
				}
                                
                                if(isset($_GET['sendMail']) && $_GET["sendMail"] == "on"){
                                    sendOrderInfoMail($config, $_GET['id']);
                                }
				$data["err"] = 0;
				$data["msg"] = "Zmeny boli úspešne uložené.";
				$data["update"] = 1;
		break;
			
		// ADD new attributes -------------------------------------
		case 4 :
                        if(strlen($_GET['key']) == 0 || strlen($_GET['key']) > 40){
                                $data["msg"] = "Maximálna dĺžka kľúča je 40 znakov.";
                                break;
                        }elseif($_GET['val'] != "" && strlen($_GET['val']) > 100){
                                $data["msg"] = "Maximálna dĺžka hodnoty je 40 znakov.";
                                break;
                        }

                        sqlRequest($conn, $_GET, "insert");
                        $id = $conn->getInsertId();
                        $data['append'] =  printAttrs($conn, $_GET['id_shop_product'], $id);
                        $data['selector'] = ".shop_attr";
                        $data["err"] = 0;
                        $data["msg"] = "Úspešne pridané";
		break;		
		
		// ADD new Manufacturer -------------------------------------
		case 5 :
                        if(strlen($_GET['shop_manufacturer_name']) == 0 || strlen($_GET['shop_manufacturer_name']) > 50){
                                $data["msg"] = "Maximálna dĺžka názvu je 50 znakov.";
                                break;
                        }elseif(isset($_GET['web']) &&  strlen($_GET['web']) > 50 ){
                                $data["msg"] = "Maximálna dĺžka názvu web stránky je 50 znakov.";
                                break;
                        }

                        sqlRequest($conn, $_GET, "insert");
                        $id = $conn->getInsertId();
                        $data['append'] =   printManufacturers($conn, NULL , 1,  $id);
                        $nav = new Navigator($config['count'], 1 , './index.php?'
                                    .preg_replace("/&s=[0-9]/", "", $_GET['url']) , $config["adminPagi"]);
                        $nav->setSeparator("&amp;s=");
                        $data['pagi'] = $nav->smartNavigator();
                        $data['selector'] = "tbody.shop_manufacturer";
                        $data["err"] = 0;
                        $data["msg"] = "Úspešne pridané";
		break;		
	
		
		// ADD new Status -------------------------------------
		case 6 :
                        if(strlen($_GET['shop_product_status_name']) == 0 || strlen($_GET['shop_product_status_name']) > 150){
                                $data["msg"] = "Maximálna dĺžka názvu je 150 znakov.";
                                break;
                        }elseif(isset($_GET['label']) &&  strlen($_GET['label']) > 250 ){
                                $data["msg"] = "Maximálna dĺžka popisu web stránky je 250 znakov.";
                                break;
                        }

                        sqlRequest($conn, $_GET, "insert");
                        $id = $conn->getInsertId();
                        $data['append'] =   printStatues($conn, $id);
                        $data['selector'] = "tbody.shop_product_status";
                        $data["err"] = 0;
                        $data["msg"] = "Úspešne pridané";
		break;		
		
		// ADD new Avaibility -------------------------------------
		case 7 :
                    if(strlen($_GET['shop_product_avaibility_name']) == 0 || strlen($_GET['shop_product_avaibility_name']) > 150){
                            $data["msg"] = "Maximálna dĺžka názvu je 150 znakov.";
                            break;
                    }elseif(isset($_GET['label']) &&  strlen($_GET['label']) > 250 ){
                            $data["msg"] = "Maximálna dĺžka popisu web stránky je 250 znakov.";
                            break;
                    }

                    sqlRequest($conn, $_GET, "insert");
                    $id = $conn->getInsertId();
                    $data['append'] =   printAvaibility($conn, $id);
                    $data['selector'] = "tbody.shop_product_avaibility";
                    $data["err"] = 0;
                    $data["msg"] = "Úspešne pridané";
		break;		
		
		// ADD new CATEGORY -------------------------------------
		case 8 :
                    if(strlen($_GET['category_name']) == 0 || strlen($_GET['category_name']) > 100){
                            $data["msg"] = "Maximálna dĺžka názvu kategórie je 100 znakov.";
                            break;
                    }elseif(strlen($_GET['label']) > 0 && strlen($_GET['label']) > 255){
                            $data["msg"] = "Maximálna dĺžka popisu kategórie je 255 znakov.";
                            break;
                    }elseif(!isUniqueCateg($conn, $_GET['id'], $_GET['category_name']) ){
                            if($_GET['id'] != 0) { 
                                    $name = getCategoryById($conn, $_GET['id']); 
                            }
                            $data["msg"] = "Podkategória s názvom <strong>".$_GET['category_name'].
                                            '</strong> sa už v '.($_GET['id'] == 0 ? 'hlavnej kategórii' : $name[0]['category_name']).' nachádza.';
                            break;
                    }
                    $order = $conn->select("SELECT MAX(`order`) FROM `shop_category` WHERE `sub_id`=".$_GET['id']);
                    if(!isset($order[0]["MAX(`order`)"])) { $order[0]["MAX(`order`)"] = 1; }else{ $order[0]["MAX(`order`)"]++; }
                    $conn->insert("INSERT INTO `".$_GET['table']."` (`sub_id`, `category_name`, `order`, `link_sk`, `edit`, `editor`, `label` ) VALUES  (?,?,?,?,?,?,?)", 
                                    array( $_GET['id'], $_GET['category_name'], $order[0]["MAX(`order`)"], SEOlink($_GET['category_name']), time(), $_SESSION['id'], $_GET['label']));

                    $id = $conn->getInsertId();
                    $data['append'] =   printCategories($conn, $_GET['id'], $id);
                    $data['selector'] = "tbody.shop_category";
                    $data["err"] = 0;
                    $data["msg"] = "Úspešne pridané";
		break;	
		
		
		// UPDATE CUSTOMER -------------------------------------
		case 9 :
                        $r = validateUser($_GET);
                        if(!$r['valid']){
                                $data['msg'] = $r['msg'];
                                break;
                        }
                        $sql = "UPDATE `user` u
                                        LEFT JOIN `shop_customer` c
                                        ON c.`id_user`=u.`id_user` 
                                        SET u.`active`=?, u.`email`=?, u.`givenname`=?, u.`surname`=?, u.`id_user_type`=?,
                                                c.`street`=?, c.`city`=?, c.`zip`=?, c.`mobil`=?, c.`company`=?, c.`ico`=?, c.`dic`=?,
                                                c.`d_givenname`=?, c.`d_surname`=?, c.`d_street`=?, c.`d_city`=?, c.`d_zip`=?, c.`d_mobil`=?, c.`d_company`=?
                                        WHERE u.`id_user`=?";

                        $r = array($_GET['active'], $_GET['email'],$_GET['givenname'],$_GET['surname'],$_GET['id_user_type'],
                            $_GET['street'], $_GET['city'], $_GET['zip'], $_GET['mobil'], $_GET['company'], $_GET['ico'], $_GET['dic'], 
                            $_GET['d_givenname'], $_GET['d_surname'], $_GET['d_street'], $_GET['d_city'], $_GET['d_zip'], $_GET['d_mobil'], $_GET['d_company'], 
                            $_GET['id']
                            );

                        $conn->update($sql, $r);
                        $data['msg'] = "Zmeny boli úspešne uložené.";
                        $data['err'] = 0;
                        $data['update'] = 1; 
		break;		
		
		
		case 10:
                    if($_SESSION['type'] <= 2){
                            $data["msg"] = "Uživatelia typu <strong>Editor</strong> nemajú právo pridávať užívateľov.";
                            break;
                    }elseif(strlen($_GET['pass1']) < 5 || strlen(trim($_GET['pass2'])) < 5){
                            $data["msg"] = "Heslo musí masť minimálne 5 znakov.";
                            break;
                    }elseif($_GET['pass1'] != $_GET['pass2']){
                            $data["msg"] = "Heslá sa nezhodujú.";
                            break;
                    }elseif (!filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
                            $data["msg"] = "Neplatná e-mailová adresa.";
                            break;
                    }elseif(checkUserEmail($conn, $_GET['email'])){
                            $data["msg"] = "E-mail ".$_GET['email']." sa už v databáze nachádza.";
                            break;
                    }elseif(!preg_match("/^\w*$/", $_GET['login'])){
                            $data["msg"] = "Login obsahuje nepovolené znaky.";
                            break;
                    }elseif(!checkUserRights($conn, intval($_SESSION['id']), $_GET['id_user_type'])){
                            $data["msg"] = "Nemáte oprávnenie pridelovať vyžšie práva, než sú vaše.";
                            break;
                    }elseif(loginExists($conn, $_GET['login'])){
                            $data["msg"] = "Uívateľ s loginom: <strong>".$_GET['login']. "</strong> sa už v databáze nachádza.";
                            break;
                    }
                    $r = validateUser($_GET);
                    if(!$r['valid']){
                            $data['msg'] = $r['msg'];
                            break;
                    }
                    $salt = createSalt();
                    $conn->insert("INSERT INTO `user` (`id_user_type`, `login`, `pass`, `salt`, `active`, `blocked`, `reg_time`, `email`, `givenname`, `surname`) VALUES (?,?,?,?,?,?,?,?,?,?)", 
                                array( 	$_GET['id_user_type'], 
                                        $_GET['login'], 
                                        hash_hmac('sha256', $_GET['pass1'], $salt), 
                                        $salt, 
                                        $_GET['active'], 
                                        0,
                                        time(), 
                                        $_GET['email'], 
                                        $_GET['givenname'],
                                        $_GET['surname']) 
                                    );
                        $id = $conn->getInsertId();
                        $conn->insert("INSERT INTO `shop_customer` (`id_user`, `street`, `city`, `zip`, `company`, `ico`, `dic`, `d_givenname`,`d_surname`, `d_street`, `d_city`, `d_zip`, `d_company`) 
                                                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)", 
                                array( 	$id, 
                                        $_GET['street'], 
                                        $_GET['city'], 
                                        $_GET['zip'], 
                                        $_GET['company'], 
                                        $_GET['ico'], 
                                        $_GET['dic'], 
                                        $_GET['d_givenname'], 
                                        $_GET['d_surname'], 
                                        $_GET['d_street'], 
                                        $_GET['d_city'], 
                                        $_GET['d_zip'],
                                        $_GET['d_company']) 
                                    );
                    $data['msg'] = "Užívateľ bol úspešne pridaný..";
                    $data['err'] = 0;
		break; 
		
		// ADD new DELIVERY Status -------------------------------------
		case 11 :
                    if(strlen($_GET['delivery_name']) == 0 || strlen($_GET['delivery_name']) > 100){
                            $data["msg"] = "Maximálna dĺžka názvu je 100 znakov.";
                            break;
                    }elseif(isset($_GET['label']) &&  strlen($_GET['label']) > 250 ){
                            $data["msg"] = "Maximálna dĺžka popisu dostupnosti je 250 znakov.";
                            break;
                    }elseif($_GET["price"] != "" && !isFloat($_GET["price"])){
                            $data["msg"] = "Cena nie je v správnom tvare.";
                            break;
                    }
                    $_GET["price"] = parseToFloat($_GET["price"]);
                    sqlRequest($conn, $_GET, "insert");
                    $id = $conn->getInsertId();
                    $data['append'] =   printDelivery($conn, $id);
                    $data['selector'] = "tbody.shop_delivery";
                    $data["err"] = 0;
                    $data["msg"] = "Úspešne pridané";
		break;
		
		// ADD new PEYMENT Status -------------------------------------
		case 111 :
                    if(strlen($_GET['payment_name']) == 0 || strlen($_GET['payment_name']) > 45){
                            $data["msg"] = "Maximálna dĺžka názvu je 100 znakov.";
                            break;
                    }elseif(isset($_GET['label']) &&  strlen($_GET['label']) > 255 ){
                            $data["msg"] = "Maximálna dĺžka popisu dostupnosti je 250 znakov.";
                            break;
                    }

                    sqlRequest($conn, $_GET, "insert");
                    $id = $conn->getInsertId();
                    $data['append'] =   printPayments($conn, $id);
                    $data['selector'] = "tbody.shop_payment";
                    $data["err"] = 0;
                    $data["msg"] = "Úspešne pridané";
		break;			
		
		
		// Order filter -------------------------------------
		case 12 :
                    try{
                        $data['html'] = printOrders($conn, 1);
                    }catch(InvalidArgumentException $ex){
                        $data['msg'] = $ex->getMessage(); 
                        break;
                    }
                    $url = "m=shop&c=order&sp=view&dateFrom=".$_GET['dateFrom']."&dateTo=".$_GET['dateTo']."&priceFrom=".$_GET['priceFrom']."&priceTo=".$_GET['priceTo']."&oid=".$_GET['oid']."&order=".$_GET['order'];
                    $nav = new Navigator($config['count'], 1 , './index.php?'.preg_replace("/&s=[0-9]/", "", $url) , $config["adminPagi"]);
                    $nav->setSeparator("&amp;s=");
                    $data['pagi'] = $nav->smartNavigator();
                    $data['selector'] = "tbody.shop_order";
                    $data["msg"] = "";
                    $data['update'] = 1;
                    $data['err'] = 0;
		break;
		
		// Order filter -------------------------------------
		case 122 :
                    try{
                            $data['html'] = printProducts($conn, $_GET['cid'],  NULL, 1);
                    }catch(InvalidArgumentException $ex){
                            $data['msg'] = $ex->getMessage(); 
                            break;
                    }
                    $url = "m=shop&c=product&sp=view&cid=".$_GET['cid']."&order=".$_GET['order'];
                    $url .= (isset($_GET['priceFrom']) && $_GET['priceFrom'] != "" ? "&priceFrom=".$_GET['priceFrom'] : "");
                    $url .= (isset($_GET['priceTo']) && $_GET['priceTo'] != "" ? "&priceTo=".$_GET['priceTo'] : "");
                    $url .= (isset($_GET['active']) && $_GET['active'] != "" ? "&active=".$_GET['active'] : "");
                    $url .= (isset($_GET['sale']) && $_GET['sale'] != "" ? "&sale=".$_GET['sale'] : "");
                    $url .= (isset($_GET['home']) && $_GET['home'] != "" ? "&code=".$_GET['home'] : "");	


                    $nav = new Navigator($config['count'], 1 , './index.php?'.preg_replace("/&s=[0-9]/", "", $url) , $config["adminPagi"]);
                    $nav->setSeparator("&amp;s=");
                    $data['pagi'] = $nav->smartNavigator();
                    $data['selector'] = "tbody.shop_product";
                    $data["msg"] = "";
                    $data['update'] = 1;
                    $data['err'] = 0;
		break;		
		
		// EDITing order ITEM -------------------------------------
                case 13 :	
                    if(count($_GET['ids']) != 3){ // in key[0] is id_shop_item, in key[1] is id_shop_product and in key[2] is id_shop_variant current item
                            break;
                    }
                    $data['msg'] = "Nepodarilo sa stiahnuť varianty produktu.";
                    $data['html'] = getProductVariants( $_GET['ids'][1], $_GET['ids'][2]);
                    $data['err'] = 0;
		break;
		
		
		// SAVE edited order ITEM -------------------------------------
		case 14 :	
                    if(count($_GET['ids']) != 3){  // in key[0] is id_shop_item, in key[1] is id_shop_product and in key[2] is id_shop_variant current item
                            break;
                    }elseif(!isPositiveInt($_GET['count'], 1, 5) || $_GET['count'] <= 0){
                            $data["msg"] = "Pole počet kusov obsahuje neplatnú hodntu.";
                            break;
                    //$oid, $pid, $vid)
                    }elseif(checkItemInOrder( $_GET['id'], $_GET['ids'][1], $_GET['id_shop_variant'], $_GET['ids'][0]))
                    {
                            $data["msg"] = "Produkt s priradenou variantou sa už v obejdnávke nachádza.</strong>";
                            break;	
                    }	

                    if(isset($_GET['ownprice']) && $_GET['ownprice'] == "on"){
                            if(!isFloat($_GET['price'])){
                                    $data["msg"] = "Cena obsahuje neplatnú, alebo zápornú hodnotu.";
                                    break;
                            }
                    }else{
                            $_GET['price'] = getOrderItemPrice($_GET['ids'][1], $_GET['id_shop_variant']);
                    }

                    $conn->update("UPDATE `shop_item` SET `count`=?, `id_shop_variant`=?, `price`=? WHERE `id_shop_item`=? LIMIT 1", 
                                                    array($_GET['count'], intval($_GET['id_shop_variant']), parseToFloat($_GET['price']), $_GET['ids'][0] ) );
                    $orderInfo = getOrderInfo($_GET['id']);
                    $data['html'] = printOrderItems($conn, $_GET['id'] , $orderInfo["shop_currency_name"], $orderInfo["dph"], $orderInfo["id_shop_delivery"], $orderInfo["price_delivery"] );
                    $data['pagi'] =  printSUMofOrder($orderInfo["dph"], $orderInfo["sale"], $orderInfo["id_shop_delivery"], $orderInfo["price_delivery"]);
                    $data['msg'] =  "Zmeny boli úspešne uložené.";
                    $data['err'] = 0;
		break;
		
		/**
		*  loading product variant to order item -------------------------------------
		*/
		case 15 :	
                    if(isPositiveInt($_GET['val'])){
                            $data['html'] = getProductVariants( intval($_GET['val']) );
                    }else{
                            $id = getProductIdByName( $_GET['val'] );
                            if($id ){
                                    $data['html'] = getProductVariants( $id );
                            }
                    }
                    $data["msg"] = "";
                    $data["err"] = 0 ; 
		break;
		
		/**
		*  Adding new item to order -------------------------------------
		*/
		case 16 :	
                    if(!isPositiveInt($_GET['count'], 1 , 5) || intval($_GET['count']) <= 0 ){
                            $data["msg"] = "Počet kusov musí byť kladné celé číslo.";
                            break;
                    }
                    if(isPositiveInt($_GET['q'], 1,  10)){
                            $id = getProductIdById(intval($_GET['q']));
                            $data["msg"] = "Produkt s ID: <strong>".$_GET['q']."</strong> sa v databáze nenachádza.</strong>";
                    }else{
                            $id = getProductIdByName( $_GET['q'] );
                            $data["msg"] = "Produkt s názvom: <strong>".$_GET['q']."</strong> sa v databáze nenachádza.</strong>";
                    }		
                    if(! $id ){
                            break;		
                    }
                    $data["msg"] = "Vyskytla sa neočakávaná chyba, operáciu skúste zopakovať.";
                    $_GET['id_shop_variant'] = intval($_GET['id_shop_variant']);

                    // check if item exists in order
                    //$oid, $pid, $vid)
                    if(checkItemInOrder($_GET['id_shop_order'] ,$id , $_GET['id_shop_variant']))
                    {
                            $data["msg"] = "Produkt s priradenou variantou sa už v obejdnávke nachádza.</strong>";
                            break;	
                    }	

                    $_GET['price'] = getOrderItemPrice( $id, $_GET['id_shop_variant']);

                    $conn->insert("INSERT INTO `shop_item` (`id_shop_order`, `id_shop_product`, `price`, `id_shop_variant`, `count`) VALUES (?,?,?,?,?)",
                                                    array($_GET['id_shop_order'] , $id, $_GET['price'], $_GET['id_shop_variant'] , $_GET['count']));

                    $orderInfo = getOrderInfo($_GET['id_shop_order']);
                    $data['html'] = printOrderItems($conn, $_GET['id_shop_order'] , $orderInfo["shop_currency_name"], $orderInfo["dph"], $orderInfo["id_shop_delivery"], $orderInfo["price_delivery"] );

                    $data['pagi'] =  printSUMofOrder($orderInfo["dph"], $orderInfo["sale"], $orderInfo["id_shop_delivery"], $orderInfo["price_delivery"]);
                    $data['selector'] = "tbody.shop_item";
                    $data['msg'] =  "";

                    $data['err'] = 0;
		break;
		
		case 17:
                    if($_SESSION['type'] <= 2){
                            $data["msg"] = "Nemáte právo editovať nastavenia internetového obchodu.";
                            break;
                    }elseif(!isPositiveInt($_GET['s_dph'],1 ,3) || $_GET['s_dph'] < 0 || $_GET['s_dph'] > 100 ){
                            $data["msg"] = "DPH obsahuje neplatnú hodnotu. Povolené su (1-99)";
                            break;	
                    }elseif(!isPositiveInt($_GET['s_adminPagi'],1 ,3) || $_GET['s_adminPagi'] < 0 || $_GET['s_adminPagi'] > 100 ){
                            $data["msg"] = "DPH obsahuje neplatnú hodnotu. Povolené su (1-99)";
                            break;	
                    }elseif(!isPositiveInt($_GET['s_shopPagi'],1 ,3) || $_GET['s_shopPagi'] < 0 || $_GET['s_shopPagi'] > 100 ){
                            $data["msg"] = "DPH obsahuje neplatnú hodnotu. Povolené su (1-99)";
                            break;	
                    }

                    updateConfig($_GET);
                    $data['msg'] =  "Zmeny boli úspešne uložené.";
                    $data['err'] = 0;
                    $data['update'] = 1;	
		break;
		
		case 18:
                    if($_SESSION['type'] <= 2){
                            $data["msg"] = "Nemáte právo editovať nastavenia internetového obchodu.";
                            break;
                    }elseif(!isPositiveInt($_GET['s_zip'],5,5)){
                            $data["msg"] = "PSČ nie je v správnom tvare.";
                            break;	
                    }elseif($_GET['s_ico'] != "" && !isPositiveInt($_GET['s_ico'],8 ,8)){
                            $data["msg"] = "IČO nie je v správnom tvare.";
                            break;	
                    }elseif($_GET['s_dic'] != "" && !isPositiveInt($_GET['s_dic'],10 ,10)){
                            $data["msg"] = "DIČ nie je v správnom tvare.";
                            break;	
                    }elseif($_GET['s_mobil'] != "" && !isPositiveInt($_GET['s_mobil'],9 ,10)){
                            $data["msg"] = "DIČ nie je v správnom tvare.";
                            break;	
                    }elseif($_GET['s_mobil'] != "" && !isPositiveInt($_GET['s_mobil'],9 ,10)){
                            $data["msg"] = "DIČ nie je v správnom tvare.";
                            break;	
                    }

                    updateConfig($_GET);
                    $data['msg'] =  "Zmeny boli úspešne uložené.";
                    $data['err'] = 0;
                    $data['update'] = 1;	
		break;
		
		case 19:
                    $data['html'] = loadEditForm($_GET['id']);
                    $data['msg'] =  "";
                    $data['err'] = 0;
		break;
		
		case 20 :
                    $r = validateUser($_GET);
                    if(!$r['valid']){
                            $data['msg'] = $r['msg'];
                            break;
                    }
                    $_GET['editor'] = $_SESSION['id'];
                    $_GET['edit'] = time();
                    $req = sqlRequest($conn, $_GET);
                    $req["data"][] =   $_GET['id'];
                    $conn->update('UPDATE `'.$_GET['table'].'` SET '.$req["cols"].' WHERE `id_'.$_GET['table'].'`=? LIMIT 1',$req["data"] );
                    $d = getFullOrderByID( $_GET['id'] );
                    if(isset($d['id_user']) && $d['id_user'] != ""){
                            $d = array_merge($d, getOrderUserById($d['id_user']) );
                    }
                    $data['html'] = printOrderInfo($d);
                    $data['msg'] =  "Zmeny boli úspešne uložené.";
                    $data['err'] = 0;	
		break;
		
		// ADD new Status -------------------------------------
		case 21 :
                    if(strlen($_GET['name']) == 0 || strlen($_GET['name']) > 55){
                            $data["msg"] = "Maximálna dĺžka názvu je 55 znakov.";
                            break;
                    }

                    sqlRequest($conn, $_GET, "insert");
                    $id = $conn->getInsertId();
                    $data['append'] =   printVariant($conn, $id);
                    $data['selector'] = "tbody.shop_product_variant";
                    $data["err"] = 0;
                    $data["msg"] = "Úspešne pridané";
		break;		
		
		// Product colors -------------------------------------
		case 22 :
                    $rows = array();
                    $conn->delete("DELETE FROM `shop_product_color` WHERE `id_shop_product`=?", array($_GET['id_shop_product'] ));

                    foreach ($_GET as $key => $val){
                            if($val == "on"){
                                    $rows[] = "(".$_GET['id_shop_product'].", ".intval($key).")";
                            }
                    }
                    if(count($rows) > 0){
                            $conn->insert("INSERT INTO `shop_product_color` (`id_shop_product`,`id_shop_color`) VALUES ".implode(",", $rows));
                    }
                    $data["err"] = 0;
                    $data["msg"] = "Úspešne uložené";
		break;	
                
		// show information email preview  -------------------------------------
		case 23 :
                    $mc = new MailContent($conn, $_GET['id']);
                    $mc->setOrderStatus($_GET['statusId']);
                    $mc->generateMailContent();
                    $data["err"] = 0;
                    $data["html"] = $mc->getBody();
                    $data["msg"] = "Úspešne uložené";
		break;	
		default :
	}
	}catch(MysqlException $ex){}
	
	echo $_GET["cb"] . "(" .json_encode( $data ) . ")";
	
	exit();
?>