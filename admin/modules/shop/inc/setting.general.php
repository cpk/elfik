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
    <a href="./index.php?m=shop&amp;c=setting&amp;sp=general">Základné nastavenia</a>
</div>

<strong class="h1">Základné nastavenia obchodu</strong>

<div class="left">
        <?php  include dirname(__FILE__)."/setting.nav.php"; ?>	
        
</div>
<div class="right">	


	
    <div class="cbox">
        <strong class="h img sys">Základné nastavenia</strong>
        	<form class="ajaxSubmit cust" >
            	<div class="i">
                    <div class="lbl"><div class="tt" title="shoptitle.txt"></div>Názov obchodu: </div>
                	<input type="text" name="s_title" maxlength="100" class="w300" value="<?php echo $config["s_title"]; ?>" />
                </div>
                <div class="i odd">
                    <div class="lbl"><div class="tt" title="shopdescr.txt"></div>Popis obchodu: </div>
                	<textarea  name="s_descr"  class="w300 h70"><?php echo $config["s_descr"]; ?></textarea>
                </div>
                
                 <div class="i">
                    <div class="lbl"><div class="tt" title="shopadminpagi.txt"></div><em>*</em>Stránkovanie admin: </div>
                	<input type="text" name="s_adminPagi" maxlength="3" class="w45 c required numeric" value="<?php echo $config["s_adminPagi"]; ?>" />
                </div>
                
                 <div class="i odd">
                    <div class="lbl"><div class="tt" title="shopwebpagi.txt"></div><em>*</em>Stránkovanie obchod: </div>
                	<input type="text" name="s_shopPagi" maxlength="3" class="w45 c required numeric"  value="<?php echo $config["s_shopPagi"]; ?>" />
                </div>
                
                <div class="i">
                    <div class="lbl"><div class="tt" title="shopcurrency.txt"></div>Mena: </div>
                	<select name="s_currency" class="w100">
     					<?php echo getCurrencies( $conn, $config["s_currency"]); ?>
                    </select>
                </div>
                
                <div class="i odd">
                    <div class="lbl"><div class="tt" title="shopdph.txt"></div><em>*</em>DPH: </div>
                	<input type="text" name="s_dph" maxlength="3" class="w45 c required numeric"  value="<?php echo $config["s_dph"]; ?>" /><span> % </span>
                </div>
                <div class="i"> 
                	<input type="hidden" name="act"  value="17" />
                	<input type="submit" class="ibtn2"  value="Uložiť" />
                </div>
            </form>     
        
    	<div class="clear"></div>
    </div>
  
  
  
</div>
<div class="clear"></div>



