<?php 
  function printCustomers($conn,  $q = NULL, $s = NULL){
	global $config;
	
	$config['offset'] = ($s == 1 ? 0 :  ($s * $config["adminPagi"]) - $config["adminPagi"]);
	
	if($q != NULL){
		$sql = "SELECT count(*) FROM  `user` u INNER JOIN `shop_customer` c  ON u.`id_user`=c.`id_user` WHERE u.`login` LIKE '%${q}%' OR u.`givenname` LIKE '%${q}%' or u.`surname` LIKE '%${q}%' OR u.`id_user`=".intval($q);
		
		$data = $conn->select($sql, array( ));
		
		$config['count'] = $data[0]["count(*)"];
		
		$sql = "SELECT u.`id_user`, u.`login`, u.`active`, u.`reg_time`, u.`givenname`, u.`surname` 
				FROM  `user` u
				INNER JOIN `shop_customer` c
				ON u.`id_user`=c.`id_user`
				WHERE u.`login` LIKE '%${q}%' OR u.`givenname` LIKE '%${q}%' OR u.`surname` LIKE '%${q}%' OR u.`id_user`=".intval($q)." 
				LIMIT ".$config['offset'].", ".$config['adminPagi'];	
		
		$data = $conn->select($sql, array( ));		
	}else{
		
		$sql = "SELECT count(*) FROM `user` u INNER JOIN `shop_customer` c ON u.`id_user`=c.`id_user`";
		
		$data = $conn->select($sql, array( ));
		
		$config['count'] = $data[0]["count(*)"];
		$sql = "SELECT  u.`id_user`, u.`login`, u.`active`, u.`reg_time`, u.`givenname`, u.`surname` 
				FROM `user` u 
				INNER JOIN `shop_customer` c
				ON u.`id_user`=c.`id_user`
				LIMIT ".$config['offset'].", ".$config['adminPagi'];
		$data = $conn->select($sql , array( ));
		
	}
	
	if(count($data) == 0){
		return "<p class=\"alert\">Požiadavke nevyhovuje žiadny záznam</p>";
	}
	
	$html = "";

	for($i = 0; $i < count($data); $i++ ){
		$data[$i] = array_map("clean", $data[$i]);
		$html .= '<tr><td class="c w45">'.$data[$i]['id_user'].'</td>'.
				 '<td class="w200"><a class="edit" title="Upraviť užívateľa ?" href="./index.php?m=shop&amp;c=user&amp;sp=edit&amp;uid='.$data[$i]['id_user'].'">'.$data[$i]['login'].'</a></td>'.
				 '<td class="c w45"><a href="#id'.$data[$i]['id_user'].'" title="Zmeniť aktivnosť ?" class="'.($data[$i]['active'] == 1 ? "a1" : "a0" ).'" ></a></td>'.
				 '<td class="c">'.strftime("%d.%m.%Y/%H:%M", $data[$i]['reg_time']).'</td>'. 
				 '<td class="c">'.$data[$i]['givenname'].' '.$data[$i]['surname'].'</td>'.	 
				 '<td><a class="del" title="Odstrániť stránku ?" href="#id'.$data[$i]['id_user'].'" ></a></td></tr>';
	}
	return $html; 
}


function validateUser($d){
	$d = array_map("trim", $d);
	$result['valid'] = true;
	$result['msg'] = array();
	
	if($d['givenname'] == "" || strlen($d['givenname']) > 45 ){
		$result['msg'][] = "meno";
		$result['valid'] = false;
	}
	
	if($d['surname'] == "" || strlen($d['surname']) > 45 ){
		$result['msg'][] = "priezvisko";
		$result['valid'] = false;
	}
	
	if(!isPositiveInt($d['mobil'],9 , 10)){
		$result['msg'][] = "telefón";
		$result['valid'] = false;
	}
	
	if($d['street'] == "" || strlen($d['street']) > 100 ){
		$result['msg'][] = "ulica";
		$result['valid'] = false;
	}
	
	if($d['city'] == "" || strlen($d['city']) > 45 ){
		$result['msg'][] = "mesto";
		$result['valid'] = false;
	}
	
	if(!isPositiveInt($d['zip'],5 ,5) ){
		$result['msg'][] = "PSČ";
		$result['valid'] = false;
	}
	
	// Delivery address
	if(strlen($d['d_givenname']) > 0 && (strlen($d['d_givenname']) > 45)){
		$result['msg'][] = "meno doručenia";
		$result['valid'] = false;
	}
	
	if(strlen($d['d_surname']) > 0 && (strlen($d['d_surname']) > 45) ){
		$result['msg'][] = "priezvisko doručenia";
		$result['valid'] = false;
	}
	
	if((strlen($d['d_surname']) > 0 || strlen($d['d_givenname']) > 0) && !isPositiveInt($d['d_mobil'],9 , 10)){
		$result['msg'][] = "telefón doručenia";
		$result['valid'] = false;
	}
	
	if(strlen($d['d_street']) > 0 && ($d['d_street'] == "" || strlen($d['d_street']) > 100)){
		$result['msg'][] = "ulica doručenia";
		$result['valid'] = false;
	}
	
	if(strlen($d['d_city']) > 0 && ($d['d_city'] == "" || strlen($d['d_city']) > 45) ){
		$result['msg'][] = "mesto doručenia";
		$result['valid'] = false;
	}
	
	if(strlen($d['d_zip']) > 0 && !isPositiveInt($d['d_zip'],5 ,5) ){
		$result['msg'][] = "PSČ doručenia";
		$result['valid'] = false;
	}
	
	
	if(!$result['valid']){
		$result['msg'] = "Chybne vyplenené: ".implode(", ", $result['msg']).".";
	}
	return $result;
}

?>