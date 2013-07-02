<?php
	function payment($pid){
		global $conn, $meta;
		$html = '';
		$data = $conn->select("SELECT * FROM `shop_payment` WHERE `active`=1");
		for($i = 0; $i < count($data); $i++){
			$html .='<div><input type="checkbox" '.($pid == $data[$i]['id_shop_payment'] ? 'checked="checked"' : '').
					' name="p'.$data[$i]['id_shop_payment'].'" /><strong>'.
					$data[$i]['payment_name'].'</strong><p>'.$data[$i]['label'].'</p></div>';
		}
		return $html;
	}
	
        function deliveryPrice($isDph, $price){
            global $meta;
            return ($isDph == 1 ? 
                    number_format(round($price * ($meta['s_dph'] / 100 +1), 1),2,","," ") : 
                    $price);
        }
        
	function delivery($did){
		global $conn, $meta;
		$html = '';
		$data = $conn->select("SELECT * FROM `shop_delivery` WHERE `active`=1");
		for($i = 0; $i < count($data); $i++){		
			$html .= '<div id="d'.$data[$i]['id_shop_delivery'].'"><input type="checkbox" '.
						($did == $data[$i]['id_shop_delivery'] ? 'checked="checked"' : '').
					' name="d'.$data[$i]['id_shop_delivery'].'" /><strong>'.$data[$i]['delivery_name'].
					' - ( '.deliveryPrice($data[$i]['dph'], $data[$i]['price']).' '.$meta['s_currency'].')'.
					(strlen($data[$i]['map']) > 10 ? '<a class="map" href="#map'.$data[$i]['id_shop_delivery'].'">zobraziť mapu</a>' : '' )
					.'</strong><p>'.$data[$i]['label'].'</p></div>';
		}
		return $html;
	}

	if(isset($_SESSION['dp'])){
		$d = explode("-", $_SESSION['dp']);
	}

?>
<div id="detail">
    <div class="border top"></div>
    <div class="bucket">
        <h1 class="shop">Doprava a platba 2/3</h1>
        <?php
                if( $cart->getTotalQuantity()  == 0){
                        echo '<p class="alert">Váš nákupný košík je prázny.</p>';
                }else{
        ?>
        <script>
                $(function() { 

                        if($('input').filter(":checked").length == 2)
                                $('input').not(':checked').prop("disabled", true); 

                });
        </script>

        <!-- BUCKET NAV -->
        <div class="bucket-nav">
             <a href="/shop/kosik" class="k1" title="Zobraziť obsah nákupného košíka"><p>Košík</p></a>
            <span class="arrow"></span>
            <a href="/shop/kosik2" class="k2" title="Zobraziť obsah nákupného košíka">Doprava a platba</a>
            <span class="arrow"></span>
            <a href="/shop/kosik3" class="k3 dp" >Dodacie údaje</a>
        </div>

        <!-- DELIVERY STTINGS -->
         <div class="left bx">
                <h2>Zvoľte dopravu</h2>
            <?php echo delivery( ( isset($d[0]) ? $d[0] : NULL) ); ?>
         </div>
         <div class="right bx">
                <h2>Zvoľte platbu</h2>

            <?php echo payment(( isset($d[1]) ? $d[1] : NULL)); ?>
         </div>
        <div class="clear"></div>
        
        
        
        <div class="nextStep">
            <a href="/shop/kosik3" title="Pokračovať k zadaniu dodacích údajov" class="kbtn dp">Pokračovať</a>
            <img src="/img/ajax-loader2.gif"  id="loader" alt=""/>
            <div class="clear"></div>
        </div>
        
        <div id="map-dialog"><img src="/img/ajax-loader2.gif" alt="" class="map-loader" /></div>
        <?php } ?>
    </div>
</div>
