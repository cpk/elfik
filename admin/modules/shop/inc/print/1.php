<?php
function printPayment($id){
	global $conn;
	$data = $conn->select("SELECT `payment_name` FROM `shop_payment` WHERE `id_shop_payment`=? LIMIT 1", array( $id ));
	return $data[0]["payment_name"];
}


function printHeads($data){
	global $config;
	$html = '<!DOCTYPE html><html lang="en"><head><meta charset=utf-8><link rel="stylesheet" media="print, screen" href="../css/print.css" /></head><body><div id="invoice"><h1>Objednávka: '.$_GET['oid'].'</h1>';


	$html .= ($_GET['act'] == "pdf" ? '<h3>Dodávateľ</h3><div class="pbox"><div class="l">' : '<div class="pbox"><h3>Dodávateľ</h3><div class="'.($_GET['act'] != "pdf" ? 'pr' : '').' l">' );
    $html .= (isset($config['s_company']) && $config['s_company'] != "" ? '<strong>'.$config['s_company'].'</strong><br />' : '');
	$html .= (isset($config['s_name']) && $config['s_name'] != "" ? $config['s_name'].'<br />': ''); 
	$html .= (isset($config['s_street']) && $config['s_street'] != "" ? $config['s_street'].'<br />': '');
	$html .= $config['s_zip'].' '.$config['s_city'];               
	$html .= '</div><div class="'.($_GET['act'] != "pdf" ? 'pr' : '').' l">';		
	
	$html .= (isset($config['s_ico']) && $config['s_ico'] != "" ? '<strong>IČO: </strong>'.$config['s_ico'].'<br />': '');        
    $html .= (isset($config['s_dic']) && $config['s_dic'] != "" ? '<strong>DIČ: </strong>'.$config['s_dic'].'<br />': ''); 
	$html .= (isset($config['s_fa_mail']) && $config['s_fa_mail'] != "" ? '<strong>E-mail: </strong>'.$config['s_fa_mail'].'<br />': '');
	$html .= (isset($config['s_mobil']) && $config['s_mobil'] != "" ? '<strong>Telefón: </strong>'.$config['s_mobil'].'<br />': '');
	$html .= (isset($config['s_fax']) && $config['s_fax'] != "" ? '<strong>Fax: </strong>'.$config['s_fax'].'<br />': '');		
	$html .= (isset($config['s_bank1']) && $config['s_bank1'] != "" ? '<strong>Bankové spojenie: </strong>'.$config['s_bank1'].'<br />': '');		
	$html .= (isset($config['s_bank2']) && $config['s_bank2'] != "" ? '<strong>Bankové spojenie: </strong>'.$config['s_bank2'].'<br />': '');
	$html .= (isset($config['s_bank3']) && $config['s_bank3'] != "" ? '<strong>Bankové spojenie: </strong>'.$config['s_bank3']: '');	
	
	$html .= '</div><div class="clear"></div></div>';		
	

	$html .= ($_GET['act'] == "pdf" ? '<h3>Odberateľ</h3><div class="pbox"><div class="l">' : '<div class="pbox"><h3>Odberateľ</h3><div class="pr l">' );
    $html .= (isset($data['company']) && $data['company'] != "" ? '<strong>'.$data['company'].'</strong><br />' : '');
	$html .= $data['givenname'].' '.$data['surname'].'<br />'; 
	$html .= (isset($data['street']) && $data['street'] != "" ? $data['street'].'<br />': '');
	$html .= $data['zip'].' '.$data['city'].'<br />';               
	$html .= '</div><div class="'.($_GET['act'] != "pdf" ? 'pr' : '').' l">';		
	
	$html .= (isset($data['ico']) && $data['ico'] != "" ? '<strong>IČO: </strong>'.$data['ico'].'<br />': '');        
    $html .= (isset($data['dic']) && $data['dic'] != "" ? '<strong>DIČ: </strong>'.$data['dic'].'<br />': ''); 
	if(isset($data['email']) && $data['email'] != ""){
			$html .=  '<strong>E-mail: </strong>'.$data['email'].'<br />'; 		 
	}else{
			$html .= (isset($data['mail']) ? '<strong>E-mail: </strong>'.$data['mail'].'<br />' : '' ); 		 	
	}
	$html .= (isset($data['mobil']) && $data['mobil'] != "" ? '<strong>Telefón: </strong>'.$data['mobil'].'<br />': '');
	$html .= (isset($data['create']) && $data['create'] != "" ? '<strong>Čas vytvorenia: </strong>'.strftime("%d.%m.%Y/%H:%M", $data['create']).'<br />': '');		
	$html .= '<strong>Spôsob platby: </strong>'.printPayment($data['id_shop_payment']).'</div><div class="clear"></div></div>';
	
	
	
	$html .= ($_GET['act'] == "pdf" ? '<h3>Príjemca</h3><div class="pbox"><div class="l">' : '<div class="pbox"><h3>Príjemca</h3><div class="pr l">' );

	if((isset($data['d_givenname']) && $data['d_givenname'] != "") ||
	   (isset($data['d_surname']) && $data['d_surname'] != "") ||
	   (isset($data['d_company']) && $data['d_company'] != ""))
	{
		$html .= (isset($data['d_company']) && $data['d_company'] != "" ? '<strong>'.$data['d_company'].'</strong><br />' : '');
		$html .= $data['d_givenname'].' '.$data['d_surname'].'<br />'; 
		$html .= (isset($data['d_street']) && $data['d_street'] != "" ? $data['d_street'].'<br />': '');
		$html .= $data['d_zip'].' '.$data['d_city'];               
		$html .= '</div><div class="'.($_GET['act'] != "pdf" ? 'pr' : '').' l">';
		$html .= (isset($data['d_mobil']) && $data['d_mobil'] != "" ? '<strong>Telefón: </strong>'.$data['d_mobil']: '');
		$html .= '</div><div class="clear"></div></div>';
	}else{
		$html .= (isset($data['company']) && $data['company'] != "" ? '<strong>'.$data['company'].'</strong><br />' : '');
		$html .= $data['givenname'].' '.$data['surname'].'<br />'; 
		$html .= (isset($data['street']) && $data['street'] != "" ? $data['street'].'<br />': '');
		$html .= $data['zip'].' '.$data['city'].'<br />';               
		$html .= '</div><div class="'.($_GET['act'] != "pdf" ? 'pr' : '').' l">';	
		$html .= (isset($data['ico']) && $data['ico'] != "" ? '<strong>IČO: </strong>'.$data['ico'].'<br />' : '');        
    	$html .= (isset($data['dic']) && $data['dic'] != "" ? '<strong>DIČ: </strong>'.$data['dic'].'<br />' : ''); 	
		$html .= (isset($data['mail']) && $data['mail'] != "" ? '<strong>E-mail: </strong>'.$data['mail'].'<br />' : '');
		$html .= (isset($data['mobil']) && $data['mobil'] != "" ? '<strong>Telefón: </strong>'.$data['mobil']: '');
		$html .= '</div><div class="clear"></div></div>';
	}
	
	
			
	return $html;
}


function printItems($id, $curr , $dph, $dID, $dPrice ){
	global  $conn, $config;
        
        return printOrderItems($conn, $id, $curr , $dph, $dID, $dPrice);
	
	$config['s_order_price'] = $dPrice;
	$data = getOrderItems($id, NULL);
	
	$html = "";

	for($i = 0; $i < count($data); $i++ ){
		$data[$i] = array_map("clean", $data[$i]);
		$config['s_order_price'] += $data[$i]['price'] * $data[$i]['count'];
		$html .= '<tr><td class="c">'.($i+1).' </td>'.
				 '<td>'.($data[$i]['shop_manufacturer_name'] != "Nepriradený"  ? $data[$i]['shop_manufacturer_name']." / "  : '' ).$data[$i]['title_sk'].'</td>'.
				 '<td class="c">'.(intval($data[$i]['id_shop_variant']) != 0 ?  getVariantName($data[$i]['id_shop_variant'])  : '-' ).'</td>'.
				 '<td class="c">'.$data[$i]['count'].'</td>'.
				 '<td class="rg">'.$data[$i]['price'].' '.$curr.'</td>'.
				 '<td class="rg">'.number_format($data[$i]['price'] * $data[$i]['count'], 2).' '.$curr.'</td>'.
				 '<td class="rg">'.number_format(($data[$i]['price'] * ( $dph / 100 + 1)) * $data[$i]['count'], 2 ).' '.$curr.'</td></tr>';
	}
	if($dID > 1){
		$deliveryInfo = getDeliveryInfo( $dID );		
		$html .= '<tr><td class="c">'.($i+1).'</a></td>'.
				 '<td>'.getDeliveryName( $dID ).'</td>'.
				 '<td class="c">-</td><td class="c">1</td>'.
				 '<td class="rg">'.$dPrice.' '.$curr.'</td>'.
				 '<td class="rg">'.($deliveryInfo['dph'] == 0 ? $dPrice : number_format($dPrice * ( $dph / 100 + 1), 2)).' '.$curr.'</td>'.
				 '<td class="rg">'.($deliveryInfo['dph'] == 0? $dPrice : number_format($dPrice * ( $dph / 100 + 1), 2)).' '.$curr.'</td></tr>';
	}
	$config['s_order_price'] +=	 $dPrice;	
	return $html; 
}



function html($data){
	$orderInfo = getOrderInfo($_GET['oid']);
	$html = printHeads($data);
	$html .= '<table id="m"><tr><th>P.č</th><th>Kód</th><th>Výrobca / názov tovaru</th><th>Varianta</th><th>ks</th><th>Cena/ks bez DPH</th><th>Cena/ks s DPH</th><th>Cena spolu s DPH</th></tr>';
	$html .= printItems($_GET['oid'], $orderInfo["shop_currency_name"], $orderInfo["dph"], $orderInfo["id_shop_delivery"], $orderInfo["price_delivery"] ).'</table>';
	$html .= '<div id="pagi" class="sum">'.printSUMofOrder($data["dph"], $data["sale"], $orderInfo["id_shop_delivery"], $orderInfo["price_delivery"] ).'</div>';
	return $html .= ' </div></body></html>';	
}




$auth = new Authenticate($conn);
if(!$auth->isLogined()){  
	exit();
} 

$data = getFullOrderByID( $_GET['oid'] );
if(isset($data['id_user']) && $data['id_user'] != ""){
	$data = array_merge($data, getOrderUserById($data['id_user']) );
}


		
 if(isset($_GET['act']) && $_GET['act'] == "pdf"){
  	  	include "./mpdf/mpdf.php";
		// mPDF($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=9, $orientation='P') {
		$mpdf=new mPDF("utf-8", "A4", 0, "",5,5,6,6,3,3,"P"); 
		$mpdf->SetAuthor("E-shop");
		$mpdf->SetTitle("Objednávka: ".$_GET['oid']);
		$mpdf->SetDisplayMode('fullpage');
		
		
		$stylesheet = file_get_contents('../css/print.css');
		$mpdf->WriteHTML($stylesheet,1);	
		
		$mpdf->WriteHTML(html($data));

		$mpdf->Output("objednavka_".$_GET['oid'], "I");
	
		exit;
 }else{
		echo html($data);
}
 
?>
