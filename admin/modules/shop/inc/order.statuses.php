<?php 
function printOrderNav($conn, $sid){
	$html = "";
	$data = $conn->select("SELECT  * FROM `shop_order_status` ORDER BY `id_shop_order_status`", array());
		
	for($i = 0; $i < count($data); $i++){
		$html .= '<li '.($sid == $data[$i]['id_shop_order_status'] ? 'class="bold" ' : '').'><a class="s'.$data[$i]['id_shop_order_status'].'" href="./index.php?m=shop&amp;c=order&amp;sp=view&amp;sid='.$data[$i]['id_shop_order_status'].'" >'.$data[$i]['order_status_name'].'</a></li>';
	}
	return $html;
}

?>

<strong class="h">Zobraziť obejdnávky typu:</strong>
<ul class="orderNav">
    <li <?php echo ($sid == 0 ? 'class="bold" ' : ''); ?>><a href="./index.php?m=shop&amp;c=order&amp;sp=view&amp;sid=0">Všetky obejdnávky</a></li>
	<?php
    	echo printOrderNav($conn, $sid);
	?>
</ul>