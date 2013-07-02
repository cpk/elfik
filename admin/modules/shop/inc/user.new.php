<?php if(!$auth->isLogined()){ die("Neautorizovaný prístup."); } 
	if(!isset($_GET['uid'])){ $uid = 0; } else { $uid = intval($_GET['uid']); }
?>

<div class="breadcrumb">Nachádzate sa:
	<a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?m=shop">Internetový obchod</a> &raquo;
    <a href="./index.php?m=shop&amp;c=user&amp;sp=view">Správa zákazníkov</a> &raquo;
    <a href="./index.php?m=shop&amp;c=user&amp;sp=new">Pridanie zákazníka</a>
</div>


<strong class="h1">Pridanie zákazníka</strong>

<div class="left">
		<?php include dirname(__FILE__)."/user.nav.php" ?>
</div>

<div class="right">       

        <div class="cbox">
        	<strong class="h img profile">Pridanie nového zákazníka</strong>
          

            <form name="user"  class="ajaxSubmit">
            	
                
                <div class="i">
                	<label>Typ: </label><select class="w200" name="id_user_type"><?php echo getOptions( $conn, "user_type", "name"); ?></select>
                </div>
                
                 <div class="i odd">
                	<label>Aktivita: </label><select class="w200" name="active">
                        <option value="0">Neumožniť prihlásenie</option>
                        <option value="1">Umožniť prihlásenie</option>
                    </select>
                </div>
                
                <div class="i">
                	<label><em>*</em>Login:</label><input  maxlength="35" type="text" class="w300 required" name="login"  />
                </div>
                
                <div class="i odd">
                	<label><em>*</em>Heslo:</label><input  maxlength="35" type="password" class="w300 required fiveplus" name="pass1" />
                </div>
                
                 <div class="i odd">
                	<label><em>*</em>Kontrola hesla:</label><input  maxlength="35" type="password" class="w300 required fiveplus" name="pass2" />
                </div>
                
                <div class="ih">Fakturačné údaje</div>
                <div class="i">
                	<label><em>*</em>Meno:</label><input  maxlength="45" type="text" class="w200 required" name="givenname"  />
                    <span><em>*</em>Priezvisko:</span><input  maxlength="45" type="text" class="w200 required" name="surname"/>
                </div>
                
                <div class="i odd">
                	<label><em>*</em>E-mail:</label><input  maxlength="45" type="text" class="w300 required email unique" name="email" />
                </div> 	
                
               <div class="i">
                	<label><em>*</em>Telefón:</label><span class="price">+421</span><input  maxlength="10" type="text" class="w200 required fiveplus" name="mobil" />
                </div>
                
                <div class="i odd">
                	<label><em>*</em>Ulica:</label><input  maxlength="100" type="text" class="w300 required" name="street" />
                </div> 
                
                <div class="i">
                	<label><em>*</em>Mesto:</label><input  maxlength="45" type="text" class="w200 required" name="city" />
                    <span><em>*</em>PSČ: </span><input  maxlength="5" type="text" class="w45 c required fiveplus" name="zip" />
                </div>
                
                
                
                
                <div class="ih">Firemné údaje</div>
                <p class="info">Nechajte prázdne ak je zákazník FO.</p>
                <div class="i odd">
                	<label>Názov firmy:</label><input  maxlength="45" type="text" class="w300" name="company" />
                </div>
                 <div class="i">
                	<label>IČO:</label><input  maxlength="8" type="text" class="w100" name="ico" />
                    <span>DIČ: </span><input  maxlength="10" type="text" class="w100" name="dic" />
                </div>
                
                
                
                <div class="ih">Dodacie údaje</div>
                <p class="info">Nechajte prázdne ak sa dodacia adresa zhoduje s fakturačnou.</p>
                <div class="i odd">
                	<label>Meno:</label><input  maxlength="45" type="text" class="w200" name="d_givenname"  />
                    <span>Priezvisko:</span><input  maxlength="45" type="text" class="w200" name="d_surname" />
                </div>
         		 <div class="i">
                	<label>Názov firmy:</label><input  maxlength="45" type="text" class="w300" name="d_company" />
                </div>
               <div class="i odd">
                	<label>Telefón:</label><span class="price">+421</span><input  maxlength="10" type="text" class="w200" name="d_mobil"  />
                </div>
                
                <div class="i">
                	<label>Ulica:</label><input  maxlength="100" type="text" class="w300" name="d_street" />
                </div> 
                
                <div class="i odd">
                	<label>Mesto:</label><input  maxlength="45" type="text" class="w200" name="d_city"  />
                    <span>PSČ: </span><input  maxlength="5" type="text" class="w45 c" name="d_zip"  />
                </div>
                
                
                
                
                <div class="i">
                	<input type="hidden" value="10" name="act" />
                	<input type="submit"  class="ibtn2" name="button" value="Uložiť" />
                    <div class="clear"></div>
                </div>
                
            </form>
        </div>
       

</div>
<div class="clear"></div>


