<?php


function getMailText( $keyName = NULL){
	global $conn;
	
	$data = array();
	
	$sql = "SELECT `key`, `val` FROM `shop_config_text`";
	
	if($keyName != NULL){
		$sql .= " WHERE `key`=? LIMIT 1";
		$data[] = $keyName;
	}
	
	$data = $conn->select($sql, $data);
	$result = array();
	for($i = 0; $i < count($data); $i++){
			$result[ $data[$i]["key"]] = $data[$i]["val"] ; 
	}
	return $result;
}




function updateConfig($arr, $table = "config"){
		global $conn;
		foreach($arr as $key => $val){
			if(strpos($key, "s_") !== false){
				if(strlen($val) != 0){
					$val = substr($val, 0, 254);
				}
				$conn->update("UPDATE `$table` SET `val`=? WHERE `key`=? LIMIT 1", array( $val , $key));
			}
		}
	}

?>