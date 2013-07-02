<?php 

// PRODUCT FNCs

// PRINT & SEARCH PRODUCT ----------------------------------------------------------------------

function printProducts($conn, $id, $q = NULL, $s = NULL){
	global $config;
	
	$config['offset'] = ($s == 1 ? 0 :  ($s * $config["adminPagi"]) - $config["adminPagi"]);
	
	if($q != NULL){
		$sql = "SELECT count(*) FROM  `shop_product` WHERE ".(is_numeric($q) ? "`id_shop_product`=" : "`title_sk` REGEXP ")."?";
		
		$data = $conn->select($sql, array( $q ));
		
		$config['count'] = $data[0]["count(*)"];
		
		$sql = "SELECT `id_shop_product`, `active`, `hits`, `create`, `title_sk`, `home`, `avatar1`, `home` , `id_shop_product_status` FROM  `shop_product`
				WHERE ".(is_numeric($q) ? "`id_shop_product`=" : "`title_sk` REGEXP ")."? 
				LIMIT ".$config['offset'].", ".$config['adminPagi'];	
		
		$data = $conn->select($sql, array( $q ));		
	}else{
		$filter = filterProducts($id);

		$sql = "SELECT count(*) FROM `shop_product` ".$filter['where'];
		
		$data = $conn->select($sql, $filter['data']);
		
		$config['count'] = $data[0]["count(*)"];
		$sql = "SELECT `id_shop_product`, `active`, `hits`, `create`, `title_sk`, `home`, `avatar1`, `home` , `id_shop_product_status` FROM  `shop_product`".
				$filter['where']." ORDER BY ". orderProducts() . "
				LIMIT ".$config['offset'].", ".$config['adminPagi'];
		$data = $conn->select($sql , $filter['data']);
	}
	
	if(count($data) == 0){
		return "<p class=\"alert\">Požiadavke nevyhovuje žiadny záznam</p>";
	}
	
	$html = "";

	for($i = 0; $i < count($data); $i++ ){
		$data[$i] = array_map("clean", $data[$i]);
		$tc = "";
		 $tc .= ($data[$i]['avatar1'] != "" ? '<a href="#'.$data[$i]['avatar1'].'" title="Tovar má nahratý avatár" class="tca1"></a>' : '');
		 $tc .= ($data[$i]['home'] == 1 ? '<a title="Tovar sa zobrazuje na úvodnej stránke e-shopu" class="thome"></a>' : '');
		 $tc .= ($data[$i]['id_shop_product_status'] == 2 || $data[$i]['id_shop_product_status'] == 3 ? '<a title="Tovar je zľavnený" class="tsale"></a>' : '');
		$html .= '<tr id="id'.$data[$i]['id_shop_product'].'">'.
				 '<td class="np">'.$tc.'</td>'.
				 '<td class="c">'.$data[$i]['id_shop_product'].'</td>'.
				 '<td class="w250"><a class="edit" title="Upraviť tovar: '.$data[$i]['title_sk'].'" href="./index.php?m=shop&amp;c=product&amp;sp=edit&amp;pid='.$data[$i]['id_shop_product'].'">'.
				 crop($data[$i]['title_sk'], 44).'</a></td>'.
				 '<td class="c w50"><a href="#id'.$data[$i]['id_shop_product'].'" title="Zmeniť aktivnosť ?" class="'.($data[$i]['active'] == 1 ? "a1" : "a0" ).'" ></a></td>'.
				 '<td class="c">'.$data[$i]['hits'].'</td>'.
				 '<td class="c">'.strftime("%d.%m.%Y/%H:%M", $data[$i]['create']).'</td>'. 
				 '<td><a class="del" title="Odstrániť stránku ?" href="#id'.$data[$i]['id_shop_product'].'" ></a></td></tr>';
	}
	return $html; 
}

// PRINT & SEARCH Manufacturers ----------------------------------------------------------------------

function printManufacturers($conn, $q = NULL, $s = NULL, $id = NULL ){
	global $config;
	
	$config['offset'] = ($s == 1 ? 0 :  ($s * $config["adminPagi"]) - $config["adminPagi"]);
	
	if($q != NULL){
		$sql = "SELECT count(*) FROM  `shop_manufacturer` 	WHERE `shop_manufacturer_name` REGEXP ? AND `id_shop_manufacturer`!=1";
		$data = $conn->select($sql, array( $q ));
		$config['count'] = $data[0]["count(*)"];
		$sql = "SELECT * FROM  `shop_manufacturer`  WHERE `shop_manufacturer_name` REGEXP ? AND `id_shop_manufacturer`!=1 LIMIT ".$config['offset'].", ".$config['adminPagi'];	
		$data = $conn->select($sql, array( $q ));		
	}else{
		
		$sql = "SELECT count(*) FROM `shop_manufacturer`";
		$data = $conn->select($sql, array() );
		$config['count'] = $data[0]["count(*)"];
		if($id == NULL){
			$sql = "SELECT * FROM  `shop_manufacturer` WHERE `id_shop_manufacturer`!=1 ORDER BY `shop_manufacturer_name` LIMIT ".$config['offset'].", ".$config['adminPagi'];
			$data = $conn->select($sql , array( ) );
		}else{
			$data = $conn->select("SELECT * FROM  `shop_manufacturer` WHERE `id_shop_manufacturer`=? LIMIT 1" , array( intval($id) ) );
		}
	}
	
	if(count($data) == 0){
		return "<p class=\"alert\">Požiadavke nevyhovuje žiadny záznam</p>";
	}
	
	$html = "";

	for($i = 0; $i < count($data); $i++ ){
		$data[$i] = array_map("clean", $data[$i]);
		$html .= '<tr '.($id != NULL ? 'class="mark"' : '').'><td class="c w45">'.$data[$i]['id_shop_manufacturer'].'</td>'.
				 '<td class="w200 il">'.$data[$i]['shop_manufacturer_name'].'</a></td>'.
				 '<td class="w200 il">'.$data[$i]['web'].'</a></td>'.
 				 '<td class="w50"><a href="#id'.$data[$i]['id_shop_manufacturer'].'" class="edit">Upraviť</a></td>'. 
				 '<td class="w45"><a class="del" title="Odstrániť stránku ?" href="#id'.$data[$i]['id_shop_manufacturer'].'" ></a></td></tr>';
	}
	return $html; 
}



// PRODUCT VARIANTs ----------------------------------------------------------------------

function printVariants($conn, $id, $newID = NULL){
	$html = "";
	if($newID == NULL){
		$data = $conn->select("SELECT * FROM `shop_variant` WHERE `id_shop_product`=?", array( $id ));
	}else{
		$data = $conn->select("SELECT * FROM `shop_variant` WHERE `id_shop_variant`=? LIMIT 1", array( $newID ));
	}
	for($i = 0; $i < count($data); $i++ ){
		$data[$i] = array_map("clean", $data[$i]);
		$html .= '<tr '.($newID != NULL ? 'class="mark"' : '').'>'.
				 '<td class="w400 il">'.$data[$i]['shop_variant_name'].'</td>'.
				 '<td class="c w100 il">'.$data[$i]['price'].'</td>'.
				 '<td class="c w100 il">'.$data[$i]['weight'].'</td>'.
				 '<td class="c w45"><a href="#id'.$data[$i]['id_shop_variant'].'" title="Zmeniť aktivnosť ?" class="'.($data[$i]['active'] == 1 ? "a1" : "a0" ).'" ></a></td>'.
				 '<td class="w50"><a href="#id'.$data[$i]['id_shop_variant'].'" class="edit">Upraviť</a></td>'. 
				 '<td class="w45"><a class="del" title="Odstrániť stránku ?" href="#id'.$data[$i]['id_shop_variant'].'" ></a></td></tr>';
	}
	return $html; 
}

// PRODUCT ATTRs ----------------------------------------------------------------------

function printAttrs($conn, $id, $newID = NULL){
	$html = "";
	if($newID == NULL){
		$data = $conn->select("SELECT * FROM `shop_attr` WHERE `id_shop_product`=?", array( $id ));
	}else{
		$data = $conn->select("SELECT * FROM `shop_attr` WHERE `id_shop_attr`=? LIMIT 1", array( $newID ));
	}
	for($i = 0; $i < count($data); $i++ ){
		$data[$i] = array_map("clean", $data[$i]);
		$html .= '<tr '.($newID != NULL ? 'class="mark"' : '').'>'.
				 '<td class="w200 il">'.$data[$i]['key'].'</td>'.
				 '<td class="c w400 il">'.$data[$i]['val'].'</td>'.
				 '<td class="w50"><a href="#id'.$data[$i]['id_shop_attr'].'" class="edit">Upraviť</a></td>'. 
				 '<td class="w45"><a class="del" title="Odstrániť stránku ?" href="#id'.$data[$i]['id_shop_attr'].'" ></a></td></tr>';
	}
	return $html; 
}



// PRINT status ----------------------------------------------------------------------

function printStatues($conn, $id = NULL ){
	
	if($id == NULL){
		$data = $conn->select( "SELECT * FROM  `shop_product_status` WHERE `id_shop_product_status`!=1" , array( ) );
	}else{
		$data = $conn->select("SELECT * FROM  `shop_product_status` WHERE `id_shop_product_status`!=1 AND `id_shop_product_status`=? LIMIT 1" , array( intval($id) ) );
	}
	
	if(count($data) == 0){
		return "<p class=\"alert\">Požiadavke nevyhovuje žiadny záznam</p>";
	}
	
	$html = "";

	for($i = 0; $i < count($data); $i++ ){
		$data[$i] = array_map("clean", $data[$i]);
		$html .= '<tr '.($id != NULL ? 'class="mark"' : '').'>'.
				 '<td class="w200 il">'.$data[$i]['shop_product_status_name'].'</a></td>'.
				 '<td class="w200 il">'.$data[$i]['label'].'</a></td>'.
 				 '<td class="w50"><a href="#id'.$data[$i]['id_shop_product_status'].'" class="edit">Upraviť</a></td>'. 
				 '<td class="w45">'.($data[$i]['id_shop_product_status'] == 2 || $data[$i]['id_shop_product_status'] == 3 ? '' : '<a class="del" title="Odstrániť stránku ?" href="#id'.$data[$i]['id_shop_product_status'].'" ></a>').'</td></tr>';
	}
	return $html; 
}


// PRINT global variants ----------------------------------------------------------------------

function printVariant($conn, $id = NULL ){
	
	if($id == NULL){
		$data = $conn->select( "SELECT * FROM  `shop_product_variant` ");
	}else{
		$data = $conn->select("SELECT * FROM  `shop_product_variant` WHERE `id_shop_product_variant`=? LIMIT 1" , array( intval($id) ) );
	}
	
	if(count($data) == 0){
		return "<p class=\"alert\">Požiadavke nevyhovuje žiadny záznam</p>";
	}
	
	$html = "";

	for($i = 0; $i < count($data); $i++ ){
		$data[$i] = array_map("clean", $data[$i]);
		$html .= '<tr '.($id != NULL ? 'class="mark"' : '').'>'.
				 '<td class="il">'.$data[$i]['name'].'</a></td>'.
 				 '<td class="w60"><a href="#id'.$data[$i]['id_shop_product_variant'].'" class="edit">Upraviť</a></td>'. 
				 '<td class="w50"><a class="del" title="Odstrániť stránku ?" href="#id'.$data[$i]['id_shop_product_variant'].'" ></a></td></tr>';
	}
	return $html; 
}


// PRINT status ----------------------------------------------------------------------

function printAvaibility($conn, $id = NULL ){
	
	if($id == NULL){
		$data = $conn->select( "SELECT * FROM  `shop_product_avaibility`" , array( ) );
	}else{
		$data = $conn->select("SELECT * FROM  `shop_product_avaibility` WHERE `id_shop_product_avaibility`=? LIMIT 1" , array( intval($id) ) );
	}
	
	if(count($data) == 0){
		return "<p class=\"alert\">Požiadavke nevyhovuje žiadny záznam</p>";
	}
	
	$html = "";

	for($i = 0; $i < count($data); $i++ ){
		$data[$i] = array_map("clean", $data[$i]);
		$html .= '<tr '.($id != NULL ? 'class="mark"' : '').'>'.
				 '<td class="w200 il">'.$data[$i]['shop_product_avaibility_name'].'</a></td>'.
				 '<td class="w200 il">'.$data[$i]['label'].'</a></td>'.
 				 '<td class="w50"><a href="#id'.$data[$i]['id_shop_product_avaibility'].'" class="edit">Upraviť</a></td>'. 
				 '<td class="w45"><a class="del" title="Odstrániť stránku ?" href="#id'.$data[$i]['id_shop_product_avaibility'].'" ></a></td></tr>';
	}
	return $html; 
}



// ---------------------------------------------------------------------------
// WHERE FILTER  
function filterProducts($id){
	$result['where'] = "";
	$result['data'] = array();
	

	// category
	if((!isset($_GET['f']) || $_GET['f']==0 ) && $id == 0){
		$result['where'] = " `id_shop_category`=0";
	}elseif(isset($_GET['f']) && $_GET['f']==1 && $id == 0){
		$result['where'] = " `id_shop_category`!=0";
	}else{
		$ids = getChildersID($id);
		$result['where'] = "(`id_shop_category`=".implode(" OR `id_shop_category`=", $ids).")";
	}
	
	if(isset($_GET['code']) && strlen($_GET['code']) > 0){
		$result['where'] .= " AND `id_shop_product`=".intval($_GET['code'])." OR `ean`=?";
		$result['data'][] = $_GET['code'];
	}
	
	if(isset($_GET['priceFrom']) && strlen($_GET['priceFrom']) > 0){
		if(isFloat($_GET['priceFrom'])){
			$result['where'] .= " AND `price`>=?";
			$result['data'][] = floatval($_GET['priceFrom']);
		}else{
			throw new InvalidArgumentException("Nesprávný formát  <strong>ceny od</strong>.");
		}
	}
	
	if(isset($_GET['priceTo']) && strlen($_GET['priceTo']) > 0){
		if(isFloat($_GET['priceTo'])){
			$result['where'] .= " AND `price`<=?";
			$result['data'][] = floatval($_GET['priceTo']);
		}else{
			throw new InvalidArgumentException("Nesprávný formát  <strong>ceny do</strong>.");
		}
	}
	
	if(isset($_GET['sale']) && $_GET['sale'] != ""){
		$result['where'] .= " AND `id_shop_product_status`=2 OR `id_shop_product_status`=3";
	}
	if(isset($_GET['home']) && $_GET['home'] != ""){
		$result['where'] .= " AND `home`=?";
		$result['data'][] = $_GET['home'];
	}
	if(isset($_GET['active']) && $_GET['active'] != ""){
		$result['where'] .= " AND `active`=".$_GET['active'];
	}
	
	if(strlen($result['where']) != 0) { $result['where'] = " WHERE ".$result['where']; }
	//print_r($result);
	return $result;
}


function orderProducts(){
	if(isset($_GET['order'])){
		switch ($_GET['order']) {
			case 1:
				return "`id_shop_product` DESC";
			case 2:
				return "`id_shop_product` ASC";
			case 3:
				return "`price` DESC";
			case 4:
				return "`price` ASC";
			case 5:
				return "`title_sk`";
			case 6:
				return "`title_sk` DESC";
			default:
			   return "`id_shop_product` DESC";
		}
	}else{
		return "`id_shop_product` DESC";
	}
}

function opts($id){
	return '<option value="">---</option><option value="1">Ano</option><option value="0">Nie</option>';	
		
}

function printColors($pid){
	global $conn;
	$html = "";
	$c = array();
	$data = $conn->select("SELECT `id_shop_color` FROM shop_product_color WHERE `id_shop_product`=?", array( $pid ));
	for($i = 0; $i < count($data); $i++){
		$c[] = $data[$i]['id_shop_color'];
	}
	$data = $conn->select("SELECT * FROM shop_color");
	for($i = 0; $i < count($data); $i++ ){
		$html .= ' <div class="color"><img src="./colors/'.SEOlink($data[$i]['color_name']).'.png" alt="" /><span>'.
				$data[$i]['color_name'].'</span><input type="checkbox" name="'.$data[$i]['id_shop_color'].'" '.
				( in_array($data[$i]['id_shop_color'], $c) ? 'checked="checked"' : '').' /></div>';
	}
	return $html;
}

function printNextPrev($pid, $cid){
	global $conn;
	$html = '';
	$data = $conn->select("SELECT `id_shop_product` FROM `shop_product` WHERE `id_shop_product`<? AND `id_shop_category`=? ORDER BY `id_shop_product` DESC LIMIT 1", array($pid, $cid));
	
	if($data){
		$html .= '<a class="fix next" href="./index.php?m=shop&amp;c=product&amp;sp=edit&amp;pid='.$data[0]['id_shop_product'].'" title="Zobraziť ďalší produkt v kategorii."></a>';
	}
	$data = $conn->select("SELECT `id_shop_product` FROM `shop_product` WHERE `id_shop_product`>? AND `id_shop_category`=? ORDER BY `id_shop_product` ASC LIMIT 1", array($pid, $cid));
	if($data){
		$html .= '<a class="fix prev" href="./index.php?m=shop&amp;c=product&amp;sp=edit&amp;pid='.$data[0]['id_shop_product'].'" title="Zobraziť predošlí produkt v kategorii."></a>';
	}
	return $html;
}

?>