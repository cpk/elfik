<?php 
if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
if(!isset($_GET['sid'])){ $sid = 0; } else { $sid = intval($_GET['sid']); } 



function getCurrencies( $conn,  $first = "EUR"){
	$html = "";
	$array =  $conn->select("SELECT * FROM `shop_currency`");	
	$c = count($array); 
	
	for($j=0; $j < $c;$j++){
		if($array[$j]["shop_currency_name"] == $first){
			$html .= "<option value=\"".$array[$j]["shop_currency_name"]."\">".$array[$j]["shop_currency_name"]."</option>\n";
			break;
		}
	}	
	for($j=0; $j < $c;$j++) {   
		if($array[$j]["shop_currency_name"] == $first){ continue; }
			 $html .= "<option value=\"".$array[$j]["shop_currency_name"]."\">".$array[$j]["shop_currency_name"]."</option>\n";
	}   
	return $html;
}

?>
<div class="breadcrumb">Nachádzate sa:
	<a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?m=shop">Internetový obchod</a> &raquo;
    <a href="./index.php?m=shop&amp;c=setting">Nastavenia</a> &raquo;
    <a href="./index.php?m=shop&amp;c=setting&amp;sp=fa">Fakturačné nastavenia</a>
</div>

<strong class="h1">Fakturačné nastavenia obchodu</strong>

<div class="left">
        <?php  include dirname(__FILE__)."/setting.nav.php"; ?>	
        
</div>
<div class="right">	


	
    <div class="cbox">
        <strong class="h img sys">Fakturačné nastavenia</strong>
        	<form class="ajaxSubmit cust" >
            	<p class="info">Informácie nastavované v tejto sekcii sa zobrazujú na obejdnávkach.</p>
            	<div class="i">
                    <label>Názov firmy: </label>
                	<input type="text" name="s_company" maxlength="100" class="w300" value="<?php echo $config["s_company"]; ?>" />
                </div>
                <div class="i odd">
                    <label>Meno a priezvisko: </label>
                	<input type="text" name="s_name" maxlength="100" class="w300" value="<?php echo $config["s_name"]; ?>" />
                </div>
                
                 <div class="i">
                    <label><em>*</em>Ulica: </label>
                	<input type="text" name="s_street" maxlength="100" class="w300 required" value="<?php echo $config["s_street"]; ?>" />
                </div>
                
                 <div class="i odd">
                    <label><em>*</em>PSČ: </label>
                	<input type="text" name="s_zip" maxlength="5" class="w55 c required fiveplus"  value="<?php echo $config["s_zip"]; ?>" />
                    <span><em>*</em>Mesto: </span>
                    <input type="text" name="s_city" maxlength="45" class="w200 required"  value="<?php echo $config["s_city"]; ?>" />
                </div>
                
                <div class="i">
                    <label>IČO: </label>
                	<input type="text" name="s_ico" maxlength="8" class="w100 c"  value="<?php echo $config["s_ico"]; ?>" />
                </div>
                
                 <div class="i odd">
                    <label>DIČ: </label>
                	<span class="price">SK</span><input type="text" name="s_dic" maxlength="10" class="w200"  value="<?php echo $config["s_dic"]; ?>" />
                </div>
                
                <div class="ih">Kontaktné údaje</div>
                
                 <div class="i">
                    <label>E-mail: </label>
                	<input type="text" name="s_fa_mail" maxlength="45" class="w300 required email"  value="<?php echo $config["s_fa_mail"]; ?>" />
                </div>
                 <div class="i odd">
                    <label>Telefón: </label>
                	<span class="price">+421</span><input type="text" name="s_mobil" maxlength="10" class="w200"  value="<?php echo $config["s_mobil"]; ?>" />
                </div>
                 <div class="i">
                    <label>Fax: </label>
                	<input type="text" name="s_fax" maxlength="10" class="w300"  value="<?php echo $config["s_fax"]; ?>" />
                </div>
                 <div class="i odd">
                    <label>1. Bankové spojenie:</label>
                	<input type="text" name="s_bank1" maxlength="45" class="w300"  value="<?php echo $config["s_bank1"]; ?>" />
                </div>
                 <div class="i odd">
                    <label>2. Bankové spojenie:</label>
                	<input type="text" name="s_bank2" maxlength="45" class="w300"  value="<?php echo $config["s_bank2"]; ?>" />
                </div>
                 <div class="i odd">
                   <label>3. Bankové spojenie:</label>
                	<input type="text" name="s_bank3" maxlength="45" class="w300"  value="<?php echo $config["s_bank3"]; ?>" />
                </div>
                
                
                <div class="i"> 
                	<input type="hidden" name="act"  value="18" />
                	<input type="submit" class="ibtn2"  value="Uložiť" />
                </div>
            </form>     
        
    	<div class="clear"></div>
    </div>
  
  
  
</div>
<div class="clear"></div>



