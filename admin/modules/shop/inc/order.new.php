<?php 
if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
if(!isset($_GET['pyid'])){ $pyid = 0; } else { $pyid = intval($_GET['pyid']); } 
?>

<div class="breadcrumb">Nachádzate sa:
	<a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?m=shop">Internetový obchod</a> &raquo;
    <a href="./index.php?m=shop&amp;c=order">Správa objednávok</a> &raquo;
    <a href="./index.php?m=shop&amp;c=order&amp;sp=new">Pridanie novej objednávky</a>
</div>

<strong class="h1">Pridanie novej objednávky</strong>

<div class="left">
        <?php  include dirname(__FILE__)."/order.nav.php"; ?>	    
</div>

<div class="right">	
    <div class="cbox">
        <strong class="h img article">Pridanie objednávky</strong>
            <form  method="post"  action="./modules/shop/inc/order.insert.php">
            <?php
            	if(isset($_SESSION['status'])){
					echo '<p class="error">'.$_SESSION['status'].'</p>';
					unset($_SESSION['status']);
				}
			?>	
                 <div class="ih">Nastavenie stavov</div>
                
               	<div class="i">
                    <label>Stav objednávky: </label>
                    <select class="mw300" name="id_shop_order_status">
                        <?php echo getOptions( $conn, "shop_order_status", "order_status_name", (isset($_POST['id_shop_order_status']) ? $_POST['id_shop_order_status'] : NULL)); 	?>
                   </select>
                </div>
                <div class="i odd">
                    <label>Spôsob platby: </label>
                    <select class="mw300" name="id_shop_payment">
                        <?php echo getOptions( $conn, "shop_payment", "payment_name", (isset($_POST['id_shop_payment']) ? $_POST['id_shop_payment'] : NULL)); 	?>
                   </select>
                </div>
                 <div class="i">
                    <label>Spôsob odberu: </label>
                    <select class="mw300" name="id_shop_delivery">
                       <?php echo getDeliveryOpts(  $data['id_shop_delivery']); ?>
                   </select>
                </div>
               
                
                <div class="ih">Fakturačné údaje</div>
                <div class="i">
                	<label><em>*</em>Meno:</label><input  maxlength="45" type="text" class="w200 required" name="givenname"  <?php echo (isset($_POST['givenname']) ? ' value="'.$_POST['givenname'].'"' : ''); ?> />
                    <span><em>*</em>Priezvisko:</span><input  maxlength="45" type="text" class="w200 required" name="surname" <?php echo (isset($_POST['surname']) ? ' value="'.$_POST['surname'].'"' : ''); ?> />
                </div>
                
                <div class="i odd">
                	<label><em>*</em>E-mail:</label><input  maxlength="45" type="text" class="w300 required email" name="mail"  <?php echo (isset($_POST['mail']) ? ' value="'.$_POST['mail'].'"' : ''); ?>  />
                </div> 	
                
               <div class="i">
                	<label><em>*</em>Telefón:</label><span class="price">+421</span><input  maxlength="10" type="text" class="w200 required fiveplus" name="mobil"  
					<?php echo (isset($_POST['mobil']) ? ' value="'.$_POST['mobil'].'"' : ''); ?>  />
                </div>
                
                <div class="i odd">
                	<label><em>*</em>Ulica:</label><input  maxlength="100" type="text" class="w300 required" name="street" 
                    <?php echo (isset($_POST['street']) ? ' value="'.$_POST['street'].'"' : ''); ?> />
                </div> 
                
                <div class="i">
                	<label><em>*</em>Mesto:</label><input  maxlength="45" type="text" class="w200 required" name="city"  <?php echo (isset($_POST['city']) ? ' value="'.$_POST['city'].'"' : ''); ?>  />
                    <span><em>*</em>PSČ: </span><input  maxlength="5" type="text" class="w45 c required fiveplus" name="zip"  <?php echo (isset($_POST['zip']) ? ' value="'.$_POST['zip'].'"' : ''); ?>  />
                </div>
                
                
                
                
                <div class="ih">Firemné údaje</div>
                <p class="info">Nechajte prázdne ak je zákazník FO.</p>
                <div class="i odd">
                	<label>Názov firmy:</label><input  maxlength="45" type="text" class="w300" name="company"  <?php echo (isset($_POST['company']) ? ' value="'.$_POST['company'].'"' : ''); ?>  />
                </div>
                 <div class="i">
                	<label>IČO:</label><input  maxlength="8" type="text" class="w100" name="ico" <?php echo (isset($_POST['ico']) ? ' value="'.$_POST['ico'].'"' : ''); ?>  />
                    <span>DIČ: </span><input  maxlength="10" type="text" class="w100" name="dic" <?php echo (isset($_POST['dic']) ? ' value="'.$_POST['dic'].'"' : ''); ?>  />
                </div>
                
                
                
                <div class="ih">Dodacie údaje</div>
                <p class="info">Nechajte prázdne ak sa dodacia adresa zhoduje s fakturačnou.</p>
                <div class="i odd">
                	<label>Meno:</label><input  maxlength="45" type="text" class="w200" name="d_givenname"  <?php echo (isset($_POST['d_givenname']) ? ' value="'.$_POST['d_givenname'].'"' : ''); ?> />
                    <span>Priezvisko:</span><input  maxlength="45" type="text" class="w200" name="d_surname"  <?php echo (isset($_POST['d_surname']) ? ' value="'.$_POST['d_surname'].'"' : ''); ?> />
                </div>
         		 <div class="i">
                	<label>Názov firmy:</label><input  maxlength="45" type="text" class="w300" name="d_company" <?php echo (isset($_POST['d_company']) ? ' value="'.$_POST['d_company'].'"' : ''); ?> />
                </div>
               <div class="i odd">
                	<label>Telefón:</label><span class="price">+421</span><input  maxlength="10" type="text" class="w200" name="d_mobil" <?php echo (isset($_POST['d_mobil']) ? ' value="'.$_POST['d_mobil'].'"' : ''); ?>  />
                </div>
                
                <div class="i">
                	<label>Ulica:</label><input  maxlength="100" type="text" class="w300" name="d_street"  <?php echo (isset($_POST['d_street']) ? ' value="'.$_POST['d_street'].'"' : ''); ?>/>
                </div> 
                
                <div class="i odd">
                	<label>Mesto:</label><input  maxlength="45" type="text" class="w200" name="d_city"  <?php echo (isset($_POST['d_city']) ? ' value="'.$_POST['d_city'].'"' : ''); ?> />
                    <span>PSČ: </span><input  maxlength="5" type="text" class="w45 c" name="d_zip" <?php echo (isset($_POST['d_zip']) ? ' value="'.$_POST['d_zip'].'"' : ''); ?>  />
                </div>
                
                
                
                
                <div class="i">
                	<input type="submit"  class="ibtn2 w150" value="Uložiť a pokračovať " />
                    <div class="clear"></div>
                </div>
                
            </form>
    	<div class="clear"></div>
    </div>
   
   
</div>
<div class="clear"></div>



