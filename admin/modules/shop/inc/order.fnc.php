<?php

function printOrders($conn, $s = NULL){
	global $config;
	
		$config['offset'] = ($s == 1 ? 0 :  ($s * $config["adminPagi"]) - $config["adminPagi"]);
		
		$result = whereFilter();
		
		$sql = "SELECT  o.`id_shop_order` ".
			"FROM  `shop_order` o ".
                        "JOIN `shop_item` i ON o.`id_shop_order` = i.`id_shop_order` ".
                        " JOIN  `shop_order_status` s ON o.`id_shop_order_status` = s.`id_shop_order_status` ".
			"WHERE  o.`id_shop_order_status`=s.`id_shop_order_status` ".$result['where'].
                        " GROUP BY o.`id_shop_order` ". heavingFilter();
                
		
		$data = $conn->select($sql, $result['data']);
		
		$config['count'] =  count($data);
		
		$sql = "SELECT  o.`id_shop_order`, o.`id_shop_order_status`, SUM( i.`price` * i.`count` ) total_price, 
                                o.`create`, s.`order_status_name`, o.`givenname`, o.`surname`, o.`dph`,o.`sale`
			FROM  `shop_order` o
                        JOIN `shop_item` i ON o.`id_shop_order` = i.`id_shop_order`
                        JOIN  `shop_order_status` s ON o.`id_shop_order_status` = s.`id_shop_order_status` 
			WHERE  o.`id_shop_order_status`=s.`id_shop_order_status` ".$result['where']." 
                        GROUP BY o.`id_shop_order` 
                        ".heavingFilter()."  
			ORDER BY ".orderBy()."
			LIMIT ".$config['offset'].", ".$config['adminPagi'];
				
		$data = $conn->select($sql, $result['data']);
		
	
	if(count($data) == 0){
		return "<p class=\"alert\">Požiadavke nevyhovuje žiadny záznam</p>";
	}
	
	$html = "";

	for($i = 0; $i < count($data); $i++ ){
		$data[$i] = array_map("clean", $data[$i]);
		
		$html .= '<tr">'.
				 '<td class="c w70"><a class="edit" title="Zobraziť objednávku?" href="./index.php?m=shop&amp;c=order&amp;sp=edit&amp;oid='.$data[$i]['id_shop_order'].'">'.$data[$i]['id_shop_order'].'</a></td>'.
				 '<td class="w70 c">'.number_format( calculateSale($data[$i]['total_price'], $data[$i]['sale']),2).' &euro;</td>'.
				 '<td class="w150 c">'.$data[$i]['givenname'].' '.$data[$i]['surname'].'</td>'.
				 '<td class="w150 c s'.$data[$i]['id_shop_order_status'].'">'.$data[$i]['order_status_name'].'</td>'.
				 '<td class="c">'.strftime("%d.%m.%Y/%H:%M", $data[$i]['create']).'</td>'. 
				 '<td><a class="del" title="Odstrániť objednávku ?" href="#id'.$data[$i]['id_shop_order'].'" ></a></td></tr>';
	}
	return $html; 
}

function calculateSale($val, $sale){
    if($sale == 0){
        return $val;
    }else{
        return $val * ( 1  - ($sale / 100));
    }
}
// ---------------------------------------------------------------------------

function printDelivery($conn, $newID = NULL){
	global $config;


	if($newID == NULL){
		$data = $conn->select("SELECT * FROM `shop_delivery`" , array());
	}else{
		$data = $conn->select("SELECT * FROM `shop_delivery` WHERE `id_shop_delivery`=? LIMIT 1" , array( intval($newID) ));
	}	

	if(count($data) == 0){
		return "<p class=\"alert\">Požiadavke nevyhovuje žiadny záznam</p>";
	}
	
	$html = "";

	for($i = 0; $i < count($data); $i++ ){
		$data[$i] = array_map("clean", $data[$i]);
		$html .= '<tr '.($newID != NULL ? ' class="mark" ' : '').'>'.
				 '<td class="w250"><a class="edit" title="Upraviť spôsob dodania?" href="./index.php?m=shop&amp;c=order&amp;sp=delivery&amp;did='.$data[$i]['id_shop_delivery'].'">'.$data[$i]['delivery_name'].'</a></td>'.
				 '<td class="w200">'.(strlen($data[$i]['label']) > 35 ? substr($data[$i]['label'], 0 , 35)."..." : $data[$i]['label'] ).'</td>'.
				 '<td class="c w50">'.$data[$i]['price'].'</td>'.
				 '<td class="c w45"><a href="#id'.$data[$i]['id_shop_delivery'].'" title="Zmeniť publikovanie?" class="'.($data[$i]['active'] == 1 ? "a1" : "a0" ).'" ></a></td>'.
				 '<td><a class="del" title="Odstrániť položku ?" href="#id'.$data[$i]['id_shop_delivery'].'" ></a></td></tr>';
	}
	return $html; 
}

// ---------------------------------------------------------------------------

function printPayments($conn, $newID = NULL){
	global $config;


	if($newID == NULL){
		$data = $conn->select("SELECT * FROM `shop_payment`" , array());
	}else{
		$data = $conn->select("SELECT * FROM `shop_payment` WHERE `id_shop_payment`=? LIMIT 1" , array( intval($newID) ));
	}	

	if(count($data) == 0){
		return "<p class=\"alert\">Požiadavke nevyhovuje žiadny záznam</p>";
	}
	
	$html = "";

	for($i = 0; $i < count($data); $i++ ){
		$data[$i] = array_map("clean", $data[$i]);
		$html .= '<tr '.($newID != NULL ? ' class="mark" ' : '').'>'.
				 '<td class="w200"><a class="edit" title="Upraviť spôsob platby?" href="./index.php?m=shop&amp;c=order&amp;sp=payment&amp;pyid='.$data[$i]['id_shop_payment'].'">'.$data[$i]['payment_name'].'</a></td>'.
				 '<td class="w250">'.(strlen($data[$i]['label']) > 65 ? substr($data[$i]['label'], 0 , 60)."..." : $data[$i]['label'] ).'</td>'.
				 '<td class="c w45"><a href="#id'.$data[$i]['id_shop_payment'].'" title="Zmeniť publikovanie?" class="'.($data[$i]['active'] == 1 ? "a1" : "a0" ).'" ></a></td>'.
				 '<td><a class="del" title="Odstrániť položku ?" href="#id'.$data[$i]['id_shop_payment'].'" ></a></td></tr>';
	}
	return $html; 
}

// ---------------------------------------------------------------------------
// FILTER  
function orderBy(){
	if(isset($_GET['order'])){
		switch ($_GET['order']) {
			case 1:
				return "o.`id_shop_order` DESC";
			case 2:
				return "o.`id_shop_order` ASC";
			case 3:
				return "o.`total_price` DESC";
			case 4:
				return "o.`total_price` ASC";
			case 5:
				return "u.`givenname`";
			case 6:
				return "u.`surname`";
			default:
			   return "o.`id_shop_order` DESC";
		}
	}else{
		return "o.`id_shop_order` DESC";
	}
}

// ---------------------------------------------------------------------------
// WHERE FILTER  
function whereFilter(){
	$result['where'] = "";
	$result['data'] = array();
	
	if(isset($_GET['oid']) && strlen($_GET['oid']) > 0){
		if(isPositiveInt($_GET['oid'], 1, 10)){
			$result['where'] .= " AND o.`id_shop_order`=? ";
			$result['data'][] = (int)$_GET['oid'];
		}else{
			throw new InvalidArgumentException("Číslo objednávky obsahuje neplatnú hodnotu.");
		}
	}else{
		
		if(isset($_GET['sid']) && $_GET['sid'] != 0){
			$result['where'] .= " AND o.`id_shop_order_status`=?";
			$result['data'][] = intval($_GET['sid']);
		}
		
		if(isset($_GET['dateFrom']) && strlen($_GET['dateFrom']) > 0){
			if(isDate($_GET['dateFrom'])){
				$result['where'] .= " AND o.`create`>=?";
				$result['data'][] = strtotime($_GET['dateFrom']." 00:00:00");
			}else{
				throw new InvalidArgumentException("Nesprávný formát<strong> dátumu od</strong>.");
			}
		}
		
		if(isset($_GET['dateTo']) && strlen($_GET['dateTo']) > 0){
			if(isDate($_GET['dateTo'])){
				$result['where'] .= " AND o.`create`<=?";
				$result['data'][] = strtotime($_GET['dateTo']." 23:59:59");
			}else{
				throw new InvalidArgumentException("Nesprávný formát <strong>dátumu do</strong>.");
			}
		}
		
		
	}
	return $result;
}


function heavingFilter(){
   $heaving = "";
    if(isset($_GET['priceFrom']) && strlen($_GET['priceFrom']) > 0){
        if(isFloat($_GET['priceFrom'])){
                $heaving = " HAVING SUM( i.`price` * i.`count` ) >= ". floatval($_GET['priceFrom']);
        }else{
                throw new InvalidArgumentException("Nesprávný formát  <strong>ceny od</strong>.");
        }
    }

    if(isset($_GET['priceTo']) && strlen($_GET['priceTo']) > 0){
        if(isFloat($_GET['priceTo'])){
                if($heaving == ""){
                   $heaving = " HAVING SUM( i.`price` * i.`count` ) <= ". floatval($_GET['priceTo']); 
                }else{
                    $heaving .= " AND SUM( i.`price` * i.`count` ) <= ". floatval($_GET['priceTo']); 
                }
        }else{
                throw new InvalidArgumentException("Nesprávný formát  <strong>ceny do</strong>.");
        }
    }
    return $heaving;
}





function printOrderInfo($data){
		$html = '<div class="fa-box"><h3>Adresa doručenia</h3><table>';
				
		if(isset($data["d_givenname"]) && $data["d_givenname"] != "" || isset($data["d_surname"]) && $data["d_surname"] != ""){		
			$html .= (isset($data["d_company"]) && $data["d_company"] != "" ? '<tr><td class="bold">Firma: </td><td>'.$data["d_company"].'</td></tr>' : "" );
			$html .= '<tr><td class="bold">Meno a priezvisko: </td><td>'.$data["d_givenname"].' '.$data["d_surname"].'</td></tr>'.
					'<tr><td class="bold">Telefón:</td><td>'.$data["d_mobil"].'</td></tr>'.
					'<tr><td class="bold">Ulica: </td> <td>'.$data["d_street"].'</td></tr>'.
					'<tr><td class="bold">Mesto:</td><td>'.$data["d_zip"].' '.$data["d_city"].'</td></tr>';
		}else{
			$html .= (isset($data["company"]) && $data["company"] != "" ? '<tr><td class="bold">Firma: </td><td>'.$data["company"].'</td></tr>' : "" );
			$html .= '<tr><td class="bold">Meno a priezvisko: </td><td>'.$data["givenname"].' '.$data["surname"].'</td></tr>'.
					'<tr><td class="bold">Telefón:</td><td>'.$data["mobil"].'</td></tr>'.
					'<tr><td class="bold">Ulica: </td> <td>'.$data["street"].'</td></tr>'.
					'<tr><td class="bold">Mesto:</td><td>'.$data["zip"].' '.$data["city"].'</td></tr>';
		
		}
		$html .= '</table></div><div class="fa-box"><h3>Fakturačné údaje</h3><table>';
		
		$html .= (isset($data["company"]) && $data["company"] != "" ? '<tr><td class="bold">Firma: </td><td>'.$data["company"].'</td></tr>' : "" );
		$html .= '<tr><td class="bold">Meno a priezvisko: </td><td>'.$data["givenname"].' '.$data["surname"].'</td></tr>'.
				'<tr><td class="bold">Telefón:</td><td>'.$data["mobil"].'</td></tr>'.
				'<tr><td class="bold">Ulica: </td> <td>'.$data["street"].'</td></tr>'.
				'<tr><td class="bold">Mesto:</td><td>'.$data["zip"].' '.$data["city"].'</td></tr>';
		$html .= (isset($data["ico"]) && $data["ico"] != "" ? '<tr><td class="bold">IČO: </td><td>'.$data["ico"].'</td></tr>' : "" );
		$html .= (isset($data["dic"]) && $data["dic"] != "" ? '<tr><td class="bold">DIČ: </td><td>'.$data["dic"].'</td></tr>' : "" );
		$html .= '<tr><td class="bold">Telefón: </td><td>'.$data["mobil"].'</td></tr></table></div>';
		
		$html .= '<div class="fa-box"><h3>Ostatné informácie</h3><table>'.
				 '<tr><td class="bold">Dátum prijatia: </td><td>'.strftime("%d.%m.%Y/%H:%M", $data['create']).'</td></tr>';
		$html .= (isset($data['edit']) && $data['edit'] != "" ? '<tr><td class="bold">Posledná zmena: </td><td>'.strftime("%d.%m.%Y/%H:%M", $data['edit']).'</td></tr>' : '' );
		$html .= (isset($data['login']) ? '<tr><td class="bold">Objednávku odoslal: </td><td><a href="./index.php?m=shop&amp;c=user&amp;sp=edit&uid='.$data['id_user'].'" >'.$data['login'].'</a></td></tr>' : '' ); 
		if(isset($data['email']) && $data['email'] != ""){
			$html .=  '<tr><td class="bold">E-mail odosielateľa: </td><td><a href="mailto:'.$data['email'].'">'.$data['email'].'</a></td></tr>'; 		 
		}else{
			$html .= (isset($data['mail']) ? '<tr><td class="bold">E-mail odosielateľa: </td><td><a href="mailto:'.$data['mail'].'">'.$data['mail'].'</a></td></tr>' : '' ); 		 	
		}
		$html .= ($data['ip'] != "" ? '<tr><td class="bold">Odoslané z IP: </td><td>'.$data['ip'].'</td></tr>'.
				 	'<tr><td class="bold">Host: </td><td class="small" >'.gethostbyaddr($data['ip']).'</td></tr>' : '');
		
	return $html.'</table></div>';
}


// ---------------------------------------------------------------------------

function getFullOrderByID($id){
	global $conn;
	
	$data = $conn->select("SELECT o.`id_shop_order`, o.`id_shop_delivery`,  o.`id_user`, o.`id_shop_payment`, o.`id_shop_order_status`, o.`create`, o.`edit`, o.`editor`, o.`ip`, o.`total_price`, o.`dph`, o.`givenname`, o.`surname`, o.`mobil`, o.`street`, o.`city`, o.`zip`, o.`company`, o.`ico`, o.`dic`, o.`d_givenname`, o.`d_surname`, o.`d_company`, o.`d_mobil`,  o.`d_street`, o.`d_city`, o.`d_zip`,o.`sale`, o.`mail`, o.`price_delivery`, o.`note`, cu.`shop_currency_name`
FROM  `shop_order` o, `shop_order_status` s, `shop_currency` cu
WHERE  o.`id_shop_order_status`=s.`id_shop_order_status` AND o.`id_shop_order`=? LIMIT 1", array( $id ));
	
	return $data[0];
}

// ---------------------------------------------------------------------------
function getOrderUserById($id){
	global $conn; 
	$data = $conn->select("SELECT `login`, `email` FROM `user` WHERE `id_user`=? LIMIT 1", array( $id ));
	return $data[0];	
}


// ---------------------------------------------------------------------------

function getOrderItems($id, $newID){
	global $conn;
	if($newID == NULL){
		return $conn->select("SELECT i.`id_shop_item`, i.`id_shop_product`, i.`price`, i.`count`, i.`id_shop_variant`, p.`title_sk`, p.`ean`, m.`shop_manufacturer_name`
					   FROM `shop_item` i, `shop_product` p, `shop_manufacturer` m
					   WHERE i.`id_shop_product`=p.`id_shop_product` AND p.`id_shop_manufacturer`=m.`id_shop_manufacturer` AND i.`id_shop_order`=?", array( $id ));
	}else{
		return $conn->select("SELECT i.`id_shop_item`, i.`id_shop_product`, i.`price`, i.`count`, i.`id_shop_variant`, p.`title_sk`, p.`ean`, m.`shop_manufacturer_name`
					   FROM `shop_item` i, `shop_product` p, `shop_manufacturer` m
					   WHERE i.`id_shop_product`=p.`id_shop_product` AND p.`id_shop_manufacturer`=m.`id_shop_manufacturer` AND i.`id_shop_item`=? LIMIT 1", array( $newID ));
	}
}

// ---------------------------------------------------------------------------

function getVariantName($id){
	global $conn;
	$data = $conn->select("SELECT `shop_variant_name` FROM `shop_variant` WHERE `id_shop_variant`=? LIMIT 1", array( $id ));
	return $data[0]['shop_variant_name'];
}


// ---------------------------------------------------------------------------

function printPrice($price, $count = 1, $dph = 0, $returnNumber = FALSE){
    global $config;
    
    $dph = ($dph == 0 ? 1 : ( $dph / 100 + 1));
    if($returnNumber) return round($price * $dph, 2) * $count;
    return number_format(round($price * $dph, 2) * $count, 2, ",", " ").' '.$config['s_currency'];
}

function printOrderItems($conn, $id, $curr , $dph, $dID, $dPrice, $newID = NULL){
	global $config;
	$config['s_order_price_dph'] = $config['s_order_price'] = 0;
 	
	$data = getOrderItems($id, $newID);
	
	$html = "";

	for($i = 0; $i < count($data); $i++ ){
		$data[$i] = array_map("clean", $data[$i]);
		$config['s_order_price'] += printPrice($data[$i]['price'], $data[$i]['count'], 0, true);
                $config['s_order_price_dph'] += printPrice($data[$i]['price'], $data[$i]['count'], $dph, true);
		$html .= '<tr '.($newID != NULL ? ' class="mark" ' : '').'>'.
				 '<td class="w30 c">'.($i+1).'</a></td>'.
				 '<td class="w100 c">'.(strlen($data[$i]['ean']) > 0 ? $data[$i]['ean'] : '-' ).'</td>'.
				 '<td><a href="./index.php?m=shop&c=product&sp=edit&pid='.$data[$i]['id_shop_product'].'" target="_blank" title="Zobraziť produkt?" class="view">'.($data[$i]['shop_manufacturer_name'] != "Nepriradený"  ? $data[$i]['shop_manufacturer_name']." / "  : '' ).crop($data[$i]['title_sk'], 80).'</a></td>'.
				 '<td class="c">'.(intval($data[$i]['id_shop_variant']) != 0 ?  getVariantName($data[$i]['id_shop_variant'])  : '-' ).'</td>'.
				 '<td class="r ks">'.$data[$i]['count'].'</td>'.
				 '<td class="w60 r">'.printPrice($data[$i]['price']).'</td>'.
				 '<td class="w90 r">'.printPrice($data[$i]['price'], 1, $dph).'</td>'.
				 '<td class="w90 r">'.printPrice($data[$i]['price'], $data[$i]['count'], $dph).'</td>'.
				 '<td class="w50 print"><a href="#id'.$data[$i]['id_shop_item'].'-'.$data[$i]['id_shop_product'].'-'.$data[$i]['id_shop_variant'].'" class="edit">Upraviť</a></td>'. 
				 '<td class="print"><a class="del" title="Odstrániť položku ?" href="#id'.$data[$i]['id_shop_item'].'" ></a></td></tr>';
	}
	$deliveryInfo = getDeliveryInfo( $dID );		
	$html .= '<tr '.($newID != NULL ? ' class="mark" ' : '').'>'.
			 '<td class="w30 c">'.($i+1).'</a></td>'.
			 '<td class="w100 c">-</td>'.
			 '<td><a href="./index.php?m=shop&c=order&sp=delivery&did='.$dID.'" target="_blank" title="Zobraziť?" class="view">'.$deliveryInfo['delivery_name'].'</a></td>'.
			 '<td class="c">-</td>'.'<td class="r ks">1</td>'.
			 '<td class="w60 r">'.printPrice($dPrice).'</td>'.
			 '<td class="w60 r">'.($deliveryInfo['dph'] == 0 ? printPrice($dPrice) : printPrice($dPrice, 1, $dph) ).'</td>'.
			 '<td class="w90 r">'.($deliveryInfo['dph'] == 0 ? printPrice($dPrice) : printPrice($dPrice, 1, $dph) ).'</td>'.
			 '<td class="w50 print"></td>'. 
			 '<td class="print"></td></tr>';
	$config['s_order_price'] += $dPrice;	 
	$config['s_order_price_dph'] += printPrice($dPrice, 1, ($deliveryInfo['dph'] == 0 ? 0 :$dph ), true);
	return $html; 
}

function printSUMofOrder($dph, $sale, $dID, $dPrice ){
	global $config;
	if($sale == 0){
		return '<table><tr><td class="w200">Celková sumna: </td><td class="w100">'.printPrice($config['s_order_price'], 1, 0).'</td></tr>'.
				'<tr><td class="w200">DPH '.$dph.'%:</td><td class="w100">'.printPrice($config['s_order_price_dph'] - $config['s_order_price'],1,0).'</td></tr>'.
				'<tr><td class="w200">Celková suma s DPH:</td><td class="w100">'.printPrice($config['s_order_price_dph'], 1, 0).'</td></tr></table><div class="clear"></div>';
	}else{
			$salePrice = ($config['s_order_price'] - $dPrice) * ( (100 - $sale) / 100 );
		return	'<table><tr><td class="w200">Celková sumna pred zľavou: </td><td class="w100">'.number_format($config['s_order_price'], 2) . ' ' .$config['s_currency'].'</td></tr>'.
			'<tr><td class="w200">Zľava z objednávky '.$sale.'%:</td><td class="w100">-'.number_format($config['s_order_price'] - $salePrice , 2) . ' ' .$config['s_currency'].'</td></tr>'.
			'<td class="w200">Celková sumna po zľave: </td><td class="w100">'.number_format($salePrice , 2) . ' ' .$config['s_currency'].'</td></tr>'.
			'<tr><td class="w200">DPH '.$dph.'%:</td><td class="w100">'.getDphPrice($salePrice + $dPrice, $dph, $dID, $dPrice). ' ' .$config['s_currency'].'</td></tr>'.
			'<tr><td class="w200">Celková suma s DPH:</td><td class="w100">'.number_format(getDphPrice($salePrice + $dPrice, $dph, $dID, $dPrice)+$salePrice + $dPrice , 2) . ' ' .$config['s_currency'].'</td></tr></table><div class="clear"></div>';	
	}
}


/* DELIVERY data  ---------------------- */

function getDeliveryName( $id ){
	global $conn;
	$r =  $conn->select("SELECT `delivery_name` FROM `shop_delivery` WHERE `id_shop_delivery`=? LIMIT 1", array( $id ) );	
	return $r[0]['delivery_name'];
}


function getDeliveryInfo( $id ){
	global $conn;
	$r =  $conn->select("SELECT `dph`, `delivery_name` FROM `shop_delivery` WHERE `id_shop_delivery`=? LIMIT 1", array( $id ) );	
	return $r[0];
}

/** 
*	@return string / HTML options
*/

function getProductVariants( $pid, $first = 0, $skip = NULL){
	global $conn, $config;
	$html = "";
	$array =  $conn->select("SELECT `id_shop_variant`, `shop_variant_name`, `price` FROM `shop_variant` WHERE `id_shop_product`=?", array( $pid ) );	
	
	$c = count($array); 
	if($first == 0){
		$html .= "<option value=\"0\">Bez varianty</option>\n";
	}else{
		for($j=0; $j < $c;$j++){
			if($array[$j]["id_shop_variant"] == $first){
				$html .= "<option value=\"".$array[$j]["id_shop_variant"]."\">".($array[$j]["price"] != "" ? '('.$array[$j]["price"].' '.$config['s_currency'].') - ' : '').$array[$j]["shop_variant_name"]."</option>".
						 "<option value=\"0\">Bez varianty</option>\n";
				break;
			}
		}
	}
	for($j=0; $j < $c;$j++){
		if($array[$j]["id_shop_variant"] != $first){
			$html .= "<option value=\"".$array[$j]["id_shop_variant"]."\">".($array[$j]["price"] != "" ? '('.$array[$j]["price"].' '.$config['s_currency'].') - ' : '').$array[$j]["shop_variant_name"]."</option>";
		}
	}
	return $html;
}


/* Delivery option menu ----- */

function getDeliveryOpts( $id ){
	global $conn, $config;
	$html = "";
	$array = $conn->select("SELECT * FROM `shop_delivery`");
	
	for($j=0; $j < count($array);$j++){
		if($array[$j]["id_shop_delivery"] == $id){
			$html .= "<option value=\"".$array[$j]["id_shop_delivery"]."\">".$array[$j]["delivery_name"].' ('.
					 $array[$j]["price"]." ". $config['s_currency'].")</option>\n";
			break;
		}
	}	
	
	for($j=0; $j < count($array);$j++) {   
		if($array[$j]["id_shop_delivery"] == $id ){ continue; }
			 $html .= "<option value=\"".$array[$j]["id_shop_delivery"]."\">".$array[$j]["delivery_name"].' ('.
					 $array[$j]["price"]." ". $config['s_currency'].")</option>\n";
	}   
	
	return $html;
}

/** 
*	@return array with keys (`price`, `price_sale`, `id_shop_product_status` ) or NULL
*/

function getProductPrice($id){
	global $conn;
	$data = $conn->select("SELECT `price`, `price_sale`, `id_shop_product_status` FROM `shop_product` WHERE `id_shop_product`=? LIMIT 1", array( $id ));
	if(count($data) != 1) return false;
	if($data[0]['id_shop_product_status'] == 2 || $data[0]['id_shop_product_status'] == 3 && $_GET['price_sale'] != 0){
		return $data[0]['price_sale'];
	}else{
		return $data[0]['price'];
	}
}

/**
*	@return float or NULL
*/
function getVariantPrice($id){
	global $conn;
	$data = $conn->select("SELECT `price` FROM `shop_variant` WHERE `id_shop_variant`=? LIMIT 1", array( $id ));
	return $data[0]['price'];
}





/* price fucntion ---------------*/

function getDphPrice($price, $dph, $dID, $dPrice){
	if($dID == 2) { 
		return number_format(( $price - $dPrice) * ($dph / 100 + 1 ) - ( $price - $dPrice), 2); 
	}else{
		return number_format($price * ($dph / 100 + 1 ) - $price, 2);
	}
}

function getOrderInfo( $id ){
	global $conn;
	$data = $conn->select("SELECT o.`dph`, o.`sale`, o.`id_shop_delivery`, o.`price_delivery`, c.`shop_currency_name` FROM `shop_order` o, `shop_currency` c  WHERE `id_shop_order`=? AND o.`id_shop_currency`=c.`id_shop_currency` LIMIT 1", array( $id ));
	return $data[0];
}

/*
function updateOrderPrice($id, $newSale = NULL){
	global $conn, $config;
	if(isset($config['s_order_price']) && isFloat($config['s_order_price'])){
		if($newSale != NULL){
			$price = $config['s_order_price'] * ( (100 - $newSale) / 100 );
		}else{
			$price = floatval($config['s_order_price']);
		}
		$conn->update("UPDATE `shop_order` SET `total_price`=? WHERE `id_shop_order`=? LIMIT 1", array($price  , $id));
		return true;
	}
	return false;
}
*/

function getProductIdByName( $name ){
	global $conn;
	$data = $conn->select("SELECT `id_shop_product` FROM `shop_product` WHERE `title_sk`=? LIMIT 1", array( trim($name) ));
	return (count($data) == 1 ? $data[0]['id_shop_product'] : false );
}

function getProductIdById( $id ){
	global $conn;
	$data = $conn->select("SELECT `id_shop_product` FROM `shop_product` WHERE `id_shop_product`=? LIMIT 1", array( $id ));
	return (count($data) == 1 ? $data[0]['id_shop_product'] : false );
}

// dc function edited
function getOrderItemPrice($pid, $vid){
	if(intval( $vid ) == 0 || $vid < 50){
		return getProductPrice( $pid );
	}else{
		$variantPrice = getVariantPrice( $vid );
		if(isset($variantPrice) && $variantPrice != "" && $variantPrice > 0){
			return $variantPrice;
		}else{
			return getProductPrice( $pid );
		}
	}
}

function checkItemInOrder($oid, $pid, $vid, $itemID = NULL){
	global $conn;
	if($itemID == NULL){
		return (count($conn->select("SELECT `id_shop_item` FROM `shop_item` WHERE `id_shop_product`=? AND `id_shop_variant`=? AND `id_shop_order`=? LIMIT 1", 
								array( $pid, $vid, $oid ))) == 1);
	}else{
		return (count($conn->select("SELECT `id_shop_item` FROM `shop_item` WHERE `id_shop_product`=? AND `id_shop_variant`=? AND `id_shop_order`=? AND `id_shop_item`<>? LIMIT 1", 
								array( $pid, $vid, $oid, $itemID ))) == 1);
	}
}


function loadEditForm($oid){
 global $conn;
$d = getFullOrderByID( $oid );

return '<div class="ih">Fakturačné údaje</div> <div class="i"><label><em>*</em>Meno:</label><input  maxlength="45" type="text" class="text w200 required" name="givenname" value="'.$d['givenname'].'" /></div><div class="i odd"><label><em>*</em>Priezvisko:</label><input maxlength="45" type="text" class="text w200 required" name="surname" value="'.$d['surname'].'" /></div><div class="i odd"><label><em>*</em>E-mail:</label><input maxlength="45" type="text" class="text w300 required email" name="mail" value="'.$d['mail'].'"/></div> <div class="i"><label><em>*</em>Telefón:</label><span class="price">+421</span><input maxlength="10" type="text" class="text w200 required fiveplus" name="mobil"  value="'.$d['mobil'].'"</div><div class="i odd"><label><em>*</em>Ulica:</label><input maxlength="100" type="text" class="text w300 required" name="street"value="'.$d['street'].'" </div><div class="i"><label><em>*</em>Mesto:</label><input maxlength="45" type="text" class="text w200 required" name="city" value="'.$d['city'].'" /><span><em>*</em>PSČ: </span><input  maxlength="5" type="text" class="text w45 c required fiveplus" name="zip" value="'.$d['zip'].'" /></div><div class="ih">Firemné údaje</div><p class="info">Nechajte prázdne ak je zákazník FO.</p><div class="i odd"><label>Názov firmy:</label><input maxlength="45" type="text" class="text w300" name="company" '.(isset($d['company']) ? ' value="'.$d['company'].'"' : '').' /></div><div class="i"><label>IČO:</label><input maxlength="8" type="text" class="text w100" name="ico"'.(isset($d['ico']) ? ' value="'.$d['ico'].'"' : '').'/><span>DIČ: </span><input  maxlength="10" type="text" class="text w100" name="dic" '.(isset($d['dic']) ? ' value="'.$d['dic'].'"' : '').' /></div><div class="ih">Dodacie údaje</div><p class="info">Nechajte prázdne ak sa dodacia adresa zhoduje s fakturačnou.</p></div><div class="i odd"><label>Meno:</label><input  maxlength="45" type="text" class="w200" name="d_givenname" '.(isset($d['d_givenname']) ? ' value="'.$d['d_givenname'].'"' : '').' /><div class="i odd"><label>Priezvisko:</label><input maxlength="45" type="text" class="text w200" name="d_surname"  '.(isset($d['d_surname']) ? ' value="'.$d['d_surname'].'"' : '').' /></div><div class="i"><label>Názov firmy:</label><input maxlength="45" type="text" class="text w300" name="d_company"'.(isset($d['d_company']) ? ' value="'.$d['d_company'].'"' : '').' /></div><div class="i odd"><label>Telefón:</label><span class="price">+421</span><input maxlength="10" type="text" class="text w200" name="d_mobil"'.(isset($d['d_mobil']) ? ' value="'.$d['d_mobil'].'"' : '').' /></div><div class="i"><label>Ulica:</label><input maxlength="100" type="text" class="text w300" name="d_street"'.(isset($d['d_street']) ? ' value="'.$d['d_street'].'"' : '').' /></div><div class="i odd"><label>Mesto:</label><input maxlength="45" type="text" class="text w200" name="d_city"'.(isset($d['d_city']) ? ' value="'.$d['d_city'].'"' : '').' /><span>PSČ: </span><input maxlength="5" type="text" class="text w45 c" name="d_zip"'.(isset($d['d_zip']) ? ' value="'.$d['d_zip'].'"' : '').' /></div><input type="hidden" name="table" value="shop_order" /><input type="hidden" name="act" value="20" />';
}
