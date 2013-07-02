<?php 
if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
if(!isset($_GET['did'])){ $did = 0; } else { $did = intval($_GET['did']); } 
?>

<div class="breadcrumb">Nachádzate sa:
	<a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?m=shop">Internetový obchod</a> &raquo;
    <a href="./index.php?m=shop&amp;c=order">Správa objednávok</a> &raquo;
    <a href="./index.php?m=shop&amp;c=order&amp;sp=delivery">Správa spôsobov doručovania</a>
</div>

<strong class="h1">Správa doručovania tovaru</strong>

<div class="left">
	<a href="./index.php?m=shop&amp;c=order&amp;sp=delivery&amp;did=0" class="btn2 cust" title="Pridať nový typ doručenia"><strong>+</strong> Pridať nový typ doručenia</a>
		
        <?php  include dirname(__FILE__)."/order.nav.php"; ?>	
        
</div>
<div class="right">	
    <div class="cbox">
        <strong class="h img article">Správa spôsobov doručovania</strong>
            <table class="tc">
              <thead>
                  <tr>
                    <th>Názov spôsobu doručenia</th>
                    <th>Popis</th>
                    <th>Cena</th>
                    <th>Publikovať</th>
                    <th>Zmazať</th>
                  </tr>
              </thead>
              <tbody class="shop_delivery">
              	<?php echo printDelivery($conn) ; ?>
              </tbody>
            </table>
    	<div class="clear"></div>
    </div>
    
    <?php if($did == 0){ ?>
     <div class="cbox">
        <strong class="h">Pridať nový typ doručenia</strong>  
            <form name="shop_delivery"  class="ajaxSubmit"> 
                <div class="i ">
                	<label><em>*</em>Názov doručenia:</label><input  maxlength="100" type="text" class="w300 required" name="delivery_name"/>
                </div> 	
                <div class="i odd">
                	<label>Popis:</label><textarea name="label" class="w520 h70"></textarea>
                </div>
                <div class="i">
                	<label><div class="tt"  title="shopdeliveryprice.txt"></div>Cena doručenia:</label><input  maxlength="11" type="text" class="w220 c" name="price" />
					<span>DPH:</span>
					<select class="w70" name="dph">
                    <option value="1"><?php echo ($config['s_dph']); ?>%</option><option value="0">0%</option>
                    </select>
                </div>
                <div class="i odd">
                    <label>Aktivita: </label><select class="w200" name="active">
                    <option value="0">Nepublikovať</option><option value="1">Publikovať</option>
                    </select>
                </div>
                <div class="i ">
                	<label><div class="tt"  title="shopdeliverymap.txt"></div>HTML kód mapy:</label><textarea name="map" class="w520 h70"></textarea>
                </div> 
                <div class="i odd">
                    <input type="hidden" value="shop_delivery" name="table" />
                    <input type="hidden" value="11" name="act" />
                    <input type="submit" class="ibtn2 cust" value="Pridať" />
                </div>
            </form>
        <div class="clear"></div>
    </div>
	<?php }else{
		$data = $conn->select("SELECT * FROM `shop_delivery` WHERE `id_shop_delivery`=? LIMIT 1", array( $did ) );
			if(count($data) == 0){
				echo "<p class=\"alert\">Položka $did nie je v databáze evidovaná</p>";		
			}else{
	?>    
    	<div class="cbox">
        <strong class="h img profile">Úprava spôsobu doručenia</strong>  
            <form name="shop_delivery"  class="ajaxSubmit"> 
                <div class="i ">
                	<label><em>*</em>Názov doručenia:</label><input  maxlength="100" type="text" class="w300 required" name="delivery_name" value="<?php echo $data[0]['delivery_name'];  ?>" />
                </div> 	
                <div class="i odd">
                	<label>Popis:</label><textarea name="label" class="w520 h70"><?php echo $data[0]['label'];  ?></textarea>
                </div>
                <div class="i">
                	<label><div class="tt"  title="shopdeliveryprice.txt"></div>Cena doručenia:</label><input  maxlength="11" type="text" class="w220 c" name="price"  value="<?php echo $data[0]['price'];  ?>"  /> 
                    <span>DPH:</span>
					<select class="w70" name="dph">
                    <?php echo ($data[0]["dph"] == 1 ?  '<option value="1">'.($config['s_dph']).'%</option><option value="0">0%</option>' : '<option value="0">0%</option><option value="1">'.($config['s_dph']).'%</option>'); ?>
                    </select>
                </div>
                <div class="i odd">
                    <label>Aktivita: </label><select class="w200" name="active">
                    <?php  echo ($data[0]["active"] == 0 ? '<option value="0">Nepublikovať</option><option value="1">Publikovať</option>' : '<option value="1">Publikovať</option><option value="0">Nepublikovať</option>');?>
                    </select>
                </div>
                <div class="i ">
                	<label><div class="tt"  title="shopdeliverymap.txt"></div>HTML kód mapy:</label><textarea name="map" class="w520 h70"><?php echo $data[0]['map'];  ?></textarea>
                </div> 
                
                <div class="i odd">
                	<input type="hidden" value="<?php echo $did; ?>" name="id" />
                    <input type="hidden" value="shop_delivery" name="table" />
                    <input type="hidden" value="3" name="act" />
                    <input type="submit" class="ibtn2 cust" value="Uložiť" />
                </div>
            </form>
        <div class="clear"></div>
    </div>
    
    <?php 
		} 
	} 
	?>
</div>
<div class="clear"></div>



