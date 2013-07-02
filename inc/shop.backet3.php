<?php
	if(isset($_SESSION['dp'])){
		$d = explode("-", $_SESSION['dp']);
	}

	
	
	$form_token = uniqid();
 
        // commit token to session
   $_SESSION['token'] = $form_token;
?>

<div id="detail">
    <div class="border top"></div>
    <div class="bucket">
        <div id="bucketBox">
<h1 class="shop">Dodacie údaje 3/3</h1>
<?php
	if( $cart->getTotalQuantity()  == 0){
		echo '<p class="alert">Váš nákupný košík je prázny.</p>';
	}elseif(count($d) != 3){
		echo '<p class="alert">Nie je zvolenú spôsob dopravy a platby.</p>';
	}else{
?>


<div class="bucket-nav">
	<a href="/shop/kosik" class="k1" title="Zobraziť obsah nákupného košíka"><p>Košík</p></a>
    <span class="arrow"></span>
    <a href="/shop/kosik2" class="k2" title="Voľba dopravy a pľatby">Doprava a platba</a>
    <span class="arrow"></span>
     <a href="/shop/kosik2" class="k3" >Dodacie údaje</a>
     <div class="clear"></div>
</div>	
	 
     
     <div id="fin-price" > 
     	<h2 class="h2">Výsledná cena</h2>
        <table>
        	<tr>
            	<td class="w200">Celková sumna tovaru s DPH: </td>
            	<td class="w100"><?php echo $cart->getTotalPriceWithCurrencyAndDPH() ?></td>
            </tr>
			<tr><td class="w200">Cena dopravy tovaru:</td><td class="w100"><?php echo $cart->getDeliveryPriceDph(); ?></td></tr>
            <tr class="big"><td class="w200">Celkom k úhrade:</td><td class="w100"><?php echo $cart->getTotalOrderPrice(); ?></td></tr>
        </table>
        <div class="clear"></div>
     </div>
     
     
       <h2 class="h2">Objednávateľ</h2>
         <form name="order" > 
        <p class="input-nav"> 
            <input class="radio" type="radio" checked="checked" value="0" name="is_fo"><span>Nakupujem <strong>súkromne</strong></span>
            <input class="radio" type="radio" value="1" name="is_fo"><span>Nakupujem <strong>na firmu</strong></span>
        </p> 

       
        <?php 
            if(!isset($_SESSION['dp'])){
                echo '<p class="alert">Váš nákupný košík je prázny.</p>';
            }else{
        ?>
        <table class="kontakt">
        	<tr>
            	<td>Váš e-mail<strong>*</strong>:</td>
                <td><input type="text" name="mail" class="required email" maxlength="45" /></td>
                <td>Telefón<strong>*</strong>:</td>
                <td><span>+421</span><input type="text" name="mobil" maxlength="10" class="tel required numeric mobil" /></td>
            </tr>
            <tr>
            	<td>Meno<strong>*</strong>:</td>
                <td><input type="text" name="givenname" maxlength="45" class="required"/></td>
                <td>Priezvisko<strong>*</strong>:</td>
                <td><input type="text" name="surname" maxlength="45" class="required" /></td>
            </tr>
            <tr>
            	<td>Ulica, č.p.<strong>*</strong>:</td>
                <td><input type="text" name="street" maxlength="100" class="required" /></td>
                <td>Mesto<strong>*</strong>:</td>
                <td><input type="text" name="city" maxlength="45" class="required" /></td>
                <td>PSČ<strong>*</strong>:</td>
                <td><input type="text" name="zip" maxlength="5" class="required numeric fiveplus"/></td>
            </tr>
             <tr class="hidden" id="hi">
            	<td>Názov firmy:</td>
                <td><input type="text" name="company" maxlength="45" class="company" /></td>
                <td>IČO:</td>
                <td><input type="text" name="ico" maxlength="8" class="company numeric fiveplus" /></td>
                <td>DIČ:</td>
                <td><input type="text" name="dic" maxlength="10" class="company numeric mobil" /></td>
            </tr>
        
        </table>
        
        
        <p class="input-nav"> 
            <input class="radio" type="radio" checked="checked" value="0" name="diff_addr"><span>Tovar odoslať na <strong>vyššie uvedenú adresu</strong></span>
            <input class="radio" type="radio" value="1" name="diff_addr"><span>Tovar odoslať na <strong>inú adresu</strong></span>
        </p> 
     	
         <table class="kontakt hidden" id="dv">
            <tr>
            	<td>Meno<strong>*</strong>:</td>
                <td><input type="text" name="d_givenname" maxlength="45" class="del" /></td>
                <td>Priezvisko<strong>*</strong>:</td>
                <td><input type="text" name="d_surname" maxlength="45"  class="del" /></td>
                <td>Názov firmy:</td>
                <td><input type="text" name="d_company" maxlength="45" /></td>
            </tr>
            <tr>
            	<td>Ulica, č.p.<strong>*</strong>:</td>
                <td><input type="text" name="d_street" maxlength="100" class="del"  /></td>
                <td>Mesto<strong>*</strong>:</td>
                <td><input type="text" name="d_city" maxlength="45"  class="del" /></td>
                <td>PSČ<strong>*</strong>:</td>
                <td><input type="text" name="d_zip" maxlength="5" class="del numeric fiveplus" /></td>
            </tr>
            <tr>
                <td>Telefón:</td>
                <td><span>+421</span><input type="text" name="d_mobil" maxlength="10" class="tel" /></td>
            </tr>
           
        
        </table>
        <div class="clear"></div>
        <div class="nextStep">
            <input type="hidden" value="5" name="act" />
            <input type="submit" value="Odoslať" class="kbtn"  />
            <img src="/img/ajax-loader2.gif"  id="loader" alt=""/> 
         </div>
        <div class="clear"></div>
     </form> 
       </div>
<?php 
    } // dp
}

?>
    </div>
</div>
