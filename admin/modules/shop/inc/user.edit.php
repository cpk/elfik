<?php if(!$auth->isLogined()){ die("Neautorizovaný prístup."); } 
	if(!isset($_GET['uid'])){ $uid = 0; } else { $uid = intval($_GET['uid']); }
?>

<div class="breadcrumb">Nachádzate sa:
	<a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?m=shop">Internetový obchod</a> &raquo;
    <a href="./index.php?m=shop&amp;c=user&amp;sp=view">Správa zákazníkov</a> &raquo;
    <a href="./index.php?m=shop&amp;c=user&amp;sp=edit">Editácia zákazníka</a>
</div>


<strong class="h1">Správa zákazníkov</strong>

<div class="left">
		<?php include dirname(__FILE__)."/user.nav.php" ?>
</div>

<div class="right">       
        <?php
			if($uid != 0){
        	$data = $conn->select("SELECT * FROM `user` u INNER JOIN `shop_customer` c ON u.`id_user`=c.`id_user` WHERE u.`id_user`=? LIMIT 1", array($uid));
			if($data == null){
				echo '<p class="error">Užívateľ s ID: '.$uid. ' neexistuje.</p>';
			}else{
			$data[0] = array_map("clean", $data[0]);

		?>
        
        <div class="cbox">
        	<strong class="h img profile">Úprava užívateľa: <?php echo $data[0]['login']; ?></strong>
            	
        		<span class="tinfo odd"> 
                    <strong>Registrovaný od: </strong> <?php echo strftime("%d.%m.%Y / %H:%M", $data[0]['reg_time']); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                    <strong>Posledná zmena v profile: </strong> <?php if(strlen($data[0]['edit']) != 0)echo strftime("%d.%m.%Y / %H:%M", $data[0]['edit']); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                </span>

            <form name="user"  class="ajaxSubmit">
            	
                
                <div class="i">
                	<label>Typ: </label><select class="w200" name="id_user_type"><?php echo getOptions( $conn, "user_type", "name",   $data[0]['id_user_type'], ($_SESSION['type'] != 5 ? 5 : null)); ?></select>
                </div>
                
                 <div class="i odd">
                	<label>Aktivita: </label><select class="w200" name="active"><?php 
					echo ($data[0]["active"] == 0 ? '<option value="0">Neumožniť prihlásenie</option><option value="1">Umožniť prihlásenie</option>' : '<option value="1">Umožniť prihlásenie</option><option value="0">Neumožniť prihlásenie</option>');?>
                    </select>
                </div>
                <div class="ih">Fakturačné údaje</div>
                <div class="i">
                	<label><em>*</em>Meno:</label><input  maxlength="45" type="text" class="w200 required" name="givenname" value="<?php echo $data[0]['givenname']; ?>" />
                    <span><em>*</em>Priezvisko:</span><input  maxlength="45" type="text" class="w200 required" name="surname" value="<?php echo $data[0]['surname']; ?>" />
                </div>
                
                <div class="i odd">
                	<label><em>*</em>E-mail:</label><input  maxlength="45" type="text" class="w300 required email unique" name="email" value="<?php echo $data[0]['email']; ?>" />
                </div> 	
                
               <div class="i">
                	<label><em>*</em>Telefón:</label><span class="price">+421</span><input  maxlength="10" type="text" class="w200 required fiveplus" name="mobil" value="<?php echo $data[0]['mobil']; ?>" />
                </div>
                
                <div class="i odd">
                	<label><em>*</em>Ulica:</label><input  maxlength="100" type="text" class="w300 required" name="street" value="<?php echo $data[0]['street']; ?>" />
                </div> 
                
                <div class="i">
                	<label><em>*</em>Mesto:</label><input  maxlength="45" type="text" class="w200 required" name="city" value="<?php echo $data[0]['city']; ?>" />
                    <span><em>*</em>PSČ: </span><input  maxlength="5" type="text" class="w45 c required fiveplus" name="zip" value="<?php echo $data[0]['zip']; ?>" />
                </div>
                
                
                
                
                <div class="ih">Firemné údaje</div>
                <p class="info">Nechajte prázdne ak je zákazník FO.</p>
                <div class="i odd">
                	<label>Názov firmy:</label><input  maxlength="45" type="text" class="w300" name="company" value="<?php echo $data[0]['company']; ?>" />
                </div>
                 <div class="i">
                	<label>IČO:</label><input  maxlength="8" type="text" class="w100" name="ico" value="<?php echo $data[0]['ico']; ?>" />
                    <span>DIČ: </span><input  maxlength="10" type="text" class="w100" name="dic" value="<?php echo $data[0]['dic']; ?>" />
                </div>
                
                
                
                <div class="ih">Dodacie údaje</div>
                <p class="info">Nechajte prázdne ak sa dodacia adresa zhoduje s fakturačnou.</p>
                <div class="i odd">
                	<label>Meno:</label><input  maxlength="45" type="text" class="w200" name="d_givenname" value="<?php echo $data[0]['d_givenname']; ?>" />
                    <span>Priezvisko:</span><input  maxlength="45" type="text" class="w200" name="d_surname" value="<?php echo $data[0]['d_surname']; ?>" />
                </div>
                 <div class="i">
                	<label>Názov firmy:</label><input  maxlength="45" type="text" class="w300" name="d_company" value="<?php echo $data[0]['d_company']; ?>" />
                </div>
         
               <div class="i odd">
                	<label>Telefón:</label><span class="price">+421</span><input  maxlength="10" type="text" class="w200" name="d_mobil" value="<?php echo $data[0]['d_mobil']; ?>" />
                </div>
                
                <div class="i">
                	<label>Ulica:</label><input  maxlength="100" type="text" class="w300" name="d_street" value="<?php echo $data[0]['d_street']; ?>" />
                </div> 
                
                <div class="i odd">
                	<label>Mesto:</label><input  maxlength="45" type="text" class="w200" name="d_city" value="<?php echo $data[0]['d_city']; ?>" />
                    <span>PSČ: </span><input  maxlength="5" type="text" class="w45 c" name="d_zip" value="<?php echo $data[0]['d_zip']; ?>" />
                </div>
                
                
                
                
                <div class="i">
                	<input type="hidden" value="9" name="act" />
                	<input type="hidden" value="<?php echo $uid; ?>" name="id" />
                	<input type="submit"  class="ibtn2" name="button" value="Uložiť" />
                    <div class="clear"></div>
                </div>
                
            </form>
        </div>
        
        
        <div class="cbox">
        	<strong class="h img profile">Zmena prihlasovacieho hesla: <?php echo $data[0]['login']; ?></strong>

            <form name="pass"  class="ajax">
            	<p class="info">  Heslo musí mať minimálne 5 znakov.</p>
            	
                <div class="i odd">
                	<label>Súčastné heslo:</label><input  maxlength="35" type="password" class="w200 required fiveplus" name="oldpass" />
                </div>
                
                 <div class="i">
                	<label>Nové heslo:</label><input  maxlength="35" type="password" class="w200 required fiveplus" name="newpass1" />
                </div>
                
                 <div class="i odd">
                	<label>Nové heslo (kontrola):</label><input  maxlength="35" type="password" class="w200 required fiveplus" name="newpass2" />
                </div>

                <div class="i">
                	<input type="hidden" value="16" name="act" />
                	<input type="hidden" value="<?php echo $uid; ?>" name="id" />
                	<input type="submit"  class="ibtn2" name="button" value="Uložiť" />
                    <div class="clear"></div>
                </div>
            </form>
        </div>
        <?php
				}
        	}
		?>

</div>
<div class="clear"></div>


