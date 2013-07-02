<?php
// ----------------------------------------------------------

function categTree($conn, $id, $cid = NULL, $p = "product"){
	$html = "";
	$data =  $conn->select("SELECT `id_shop_category`, `sub_id`, `category_name`, `active` FROM `shop_category` WHERE `sub_id`=? ORDER BY `order`", array( $id) );	
	for($i =0; $i < count( $data ); $i++){
		$data[$i] = array_map("clean", $data[$i]);
		$html .= '<li><a class="'.($cid == (int)$data[$i]["id_shop_category"] ? "curr" : "").( $data[$i]["active"] == 0 ? ' i' : '').'" title="ID: '.$data[$i]["id_shop_category"].
				'" href="./index.php?m=shop&amp;c='.$p.'&amp;sp=view&amp;cid='.$data[$i]["id_shop_category"].'" >'.
			    $data[$i]["category_name"]."</a>";
		
		if(hasChild($data[$i]['id_shop_category'])){
			$html .= '<ul>'.categTree($conn, $data[$i]['id_shop_category'], $cid, $p).'</ul>';
 		}
		
		$html .= '</li>';		
	}
	return $html;
}

function parseToFloat($n){
	return floatval(str_replace(",",".",$n));
}


function getChildersID($id){
	global $conn;
	$ids = array( $id );
	if(hasChild($id)){
		$ids = iterate($id, $ids);
	}
	return $ids;	
}

function iterate($id, $ids){
	global $conn;
	$data =  $conn->select("SELECT `id_shop_category`, `sub_id` FROM `shop_category` WHERE `sub_id`=?", array( $id) );
	for($i =0; $i < count( $data ); $i++){
		$ids[] = $data[$i]['id_shop_category'];
		if(hasChild($data[$i]['id_shop_category'])){
			$ids = iterate( $data[$i]['id_shop_category'], $ids);
 		}
	}
	return $ids;	
}

// BC ----------------------------------------------------------------------

function categoryBC($conn, $id, $char, $p = "product"){
	$all = '<a href="./index.php?m=shop&amp;c='.$p.'&amp;sp=view&amp;f=1" >Všetky kategórie</a>';
	if($id == 0 && !isset($_GET['f'])) { 
		return '<a href="./index.php?m=shop&amp;c='.$p.'&amp;sp=view" >Nekategorizovaný tovar</a>'; 
	}elseif($id == 0 && isset($_GET['f'])){
		return $all; 
	}
	$data =  $conn->select("SELECT `id_shop_category`, `sub_id`, `category_name` FROM `shop_category` WHERE `id_shop_category`=? LIMIT 1", array( $id ));	
	if($data == null){
		return;
	}
	$data[0] = array_map("clean", $data[0]);
	$html = '<a href="./index.php?m=shop&amp;c='.$p.'&amp;sp=view&amp;cid='.$data[0]['id_shop_category'].'" >'.$data[0]['category_name'].'</a>';
	
	if($data[0]['sub_id'] != 0){
		 return categoryBC($conn, $data[0]['sub_id'], $char).' '.$char.' '.$html;
	}
	return $all."&raquo;".$html;
}

// CATEGORY OPTIONs ----------------------------------------------------------------------

function getCategoryOpts($conn, $id, $j = 0 ){
	$html = "";
	$data =  $conn->select("SELECT `id_shop_category`, `sub_id`, `category_name` FROM `shop_category` WHERE `sub_id`=? ORDER BY `order`", array( $id) );	
	
	
	
	for($i =0; $i < count( $data ); $i++){
		if($id == 0) $j = 0;
		$child = hasChild($data[$i]['id_shop_category']);
			
		$html .= '<option style="padding-left: '.($j+5).'px;background-position:'.(-250 + $j).'px 0" '.($id == 0 ? ' class="head"' : '' ).' value="'.$data[$i]['id_shop_category'].'">'.$data[$i]['category_name'].'</option>';
		
		if($child) {
			$j += 15; 
			$html .=  getCategoryOpts($conn, $data[$i]['id_shop_category'], $j );	
		}
	}
	return $html;
}

function hasChild($id){
	global $conn;
	return (count($conn->select("SELECT `id_shop_category` FROM `shop_category` WHERE `sub_id`=".$id. " LIMIT 1")) == 1 ? true : false);
}

// GATEGORY by ID ----------------------------------------------------------------------

function getCategoryById($conn, $id){
	return  $conn->select("SELECT * FROM `shop_category` WHERE `id_shop_category`=? LIMIT 1", array( $id ) );
}


// ----------------------------------------------------------------------

function convertToFloat($price){
	if(is_float($price)){
		return number_format($price, 2); 
	}else{
		return number_format(floatval(str_replace(",",".", $price)), 2); 
	}
}

// ----------------------------------------------------------------------

function isFloat($n, $len = 2){
	return (preg_match ("/^[+]?(([0-9]+)|([0-9]+[\.,]{1}[0-9]{0,$len}))$/" ,$n) == 1) && $n >= 0;
}

// ----------------------------------------------------------------------


function isPositiveInt($n, $min = 0, $max = 2){
	return (preg_match ("/^[0-9]{".$min.",".$max."}$/" ,$n) == 1);
}


// ----------------------------------------------------------------------

function isDate($d){
	return (preg_match ("/^\d{1,2}\.\d{1,2}\.\d{4}$/" ,$d) == 1);

}

function sqlRequest($conn, $data, $type = "update", $contionue = array("act", "table", "id", "_", "cb", "url", "skip", "sendMail")){
	
	$result["cols"] = array();
	$result["insert"] = array();
	$result["data"] = array();
	
	if($type == "update"){
		foreach($data as $key => $val){
			if(!in_array($key,  $contionue)){
				$result["cols"][] = "`$key`=?";	
				$result["data"][] = $val;
			}
		}
		$result["cols"] = implode(", ", $result["cols"]);
	}else{
		foreach($data as $key => $val){
			if(!in_array($key,  $contionue)){
				$result["cols"][] = "`$key`";	
				$result["insert"][] = "?";	
				$result["data"][] = $val;
			}
		}
		$result["insert"] = "(".implode(", ", $result["insert"]).")";
		$result["cols"] = "(".implode(", ", $result["cols"]).")";
		
		$conn->insert("INSERT INTO `".$data['table']."` ".$result['cols']." VALUES ".$result['insert'], $result['data'] );
		return;
	}
	return $result;
}


function sendOrderInfoMail($config, $orderId){
   global $conn; 
	
    $mc = new MailContent($conn, $orderId);
    $mc->generateMailContent();
	
    if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){
        $mail = new PHPMailer();
		$mail->AddAddress( trim( $mc->getCustomerMail()) );
		$mail->SetFrom("sender@chicfashion.sk", $config["c_name"]);
		$mail->AddReplyTo(trim($config["s_fa_mail"]), $config["c_name"]);
        $mail->WordWrap = 120; 
        $mail->IsHTML(true);
        $mail->Subject = $mc->getSubject();
        $mail->Body    = $mc->getBody();
        $mail->Send();
    }
    
}



?>