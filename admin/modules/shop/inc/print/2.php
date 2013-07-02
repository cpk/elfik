<?php

function getCSVhead($data, $orderInfo){
	global $config;
	$salePrice = $config['s_order_price'] * ( (100 - $data['sale']) / 100 );
	return "Cislo_dokladu;Datum;Celkom_bez_DPH;Zlava;Cena_po_zlave;Celkom_s_DPH;Mena\n".
	  $_GET['oid'].";".strftime("%d.%m.%Y", $data['create']).';'.$config['s_order_price'].';'.$data['sale'].';'.number_format( $salePrice ,2).
			';'.number_format($salePrice * ($orderInfo["dph"] / 100 + 1 ), 2 ).';'.$orderInfo["shop_currency_name"].";\n";	

}


function orderToCSV($id, $orderInfo, $data2){
	global  $conn, $config;
	
	$config['s_order_price'] = 0;
	$data = getOrderItems($id, NULL);
	
	$txt =  "-----------------------------------------------------------------------------------------\n".
		 	"Vyrobca_nazov;Varianta;Pocet_ks;Cena_bez_DPH;Cena_bez_DPH_spolu;Cena_s_DPH_spolu;\n";
		 
	$csvData = '';
	header( 'Content-Type: text/csv' );
	header( 'Content-Disposition: attachment;filename=obj_'.$id.'.csv');
	$fp = fopen('php://output', 'w');
	
	for($i = 0; $i < count($data); $i++ ){
		$config['s_order_price'] += $data[$i]['price'] * $data[$i]['count'];
		
		$csvData  .= ($data[$i]['shop_manufacturer_name'] != "Nepriradený"  ? $data[$i]['shop_manufacturer_name']." / "  : '' ).$data[$i]['title_sk'].";".
			 		(intval($data[$i]['id_shop_variant']) != 0 ?  getVariantName($data[$i]['id_shop_variant'])  : '-' ).";".
					$data[$i]['count'].";".
					$data[$i]['price'].";".
					number_format($data[$i]['price'] * $data[$i]['count'], 2).";".
					number_format(($data[$i]['price'] * ( $orderInfo["dph"] / 100 + 1)) * $data[$i]['count'], 2 ).";\n";
	}
	fwrite($fp, getCSVhead($data2, $orderInfo).$txt.$csvData);

	fclose($fp);
}



$auth = new Authenticate($conn);
if(!$auth->isLogined()){  
	exit();
} 

$data = getFullOrderByID( $_GET['oid'] );
if(isset($data['id_user']) && $data['id_user'] != ""){
	$data = array_merge($data, getOrderUserById($data['id_user']) );
}
         
orderToCSV($_GET['oid'], getOrderInfo($_GET['oid']) , $data );

 
?>
