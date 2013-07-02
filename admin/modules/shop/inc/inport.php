<?php
	session_start();
	
	function __autoload($class){
		include_once "./../../../inc/class.".$class.".php";
	}
	
	
	require_once "./../../../config.php";
	require_once "./../../../inc/fnc.main.php";
	require_once "./fnc.shop.php";
	require_once "./setting.fnc.php";
	ini_set("display_errors",1);
	ini_set('log_errors', 1);
	ini_set('error_log', $config['root_dir'].'/logs/php_errors.txt');


	
		$conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);
	
		$data = array( "err" => 1, "msg" => "Operaciu sa nepodarilo vykonat, skuste to znova." );
		
	$s = "ALIEN WORKSHOP SUL PARENTH;61.19;Mikina Alien Workshop s kapucí bez zipu. Potisk na předním dílu. ...## 
GLOBE ARCADIA SUL;58.6;Pruhovaná mikina Globe s vychytávkou - ve šnůrkách kapuce jsou kvalitní...## 
GLOBE SULB HOODIE;58.67;Fashion pánská mikina Globe s atypickým střihem panelů, zipem a kapucí. ...##
WESC MULTI COLOUR LOGO SULB;98.67;Pánská designová mikina WeSC se zipem a kapucí. Materiál: 100% bavlna ...##
ANALOG FADER SULB KAP;80.51;Pánská mikina Analog se zipem a kapucí Materiál: 80% Bavlna, 20%...##
ANALOG REBATE ZIPSULB;66.26;Pánská mikina Analog se zipem a kapucí Materiál: 80% Bavlna , 20%...##
QUIKSILVER SHERPA BESTO WAVES ZIP KAP;98.06;Pánská technická mikina Quiksliver s kapucí, se zipem a se speciálním zátěrem...##
BURTON BURRTECH ULTRA ZIP KAP;81.54;Pánská mikina Burton s kapucí a zipem Materiál: 100% Bavlna, 300g Vlna...##
BURTON EASTSIDE ULTRA ZIP KAP;86.66;Pánská mikina Burton s kapucí a zipem Materiál: 65% Bavlna, 35%...##
BURTON FLACKET ULTRA ZIP KAP;96.61;Pánská mikina Burton se zapínáním na kovové cvoky Materiál: 80% Bavlna,...##";

	$arr = explode("##", $s);
	
	
	for($i = 0; $i < count($arr); $i ++){
		$a2 = explode(";", $arr[$i]);
		
		$conn->insert("INSERT INTO `shop_product` (`id_shop_category`,`title_sk`, `price`,`price_standard`, `content_sk`, `header_sk`, `create`, `author`) VALUES (?,?,?,?,?,?,?,?)", 
												array(3, $a2[0], (float)$a2[1], floatval($a2[1] +4) , $a2[2], $a2[2], time(), 1  ));
	}
	
?>