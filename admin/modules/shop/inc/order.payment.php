<?php 
if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
if(!isset($_GET['pyid'])){ $pyid = 0; } else { $pyid = intval($_GET['pyid']); } 
?>

<div class="breadcrumb">Nachádzate sa:
	<a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?m=shop">Internetový obchod</a> &raquo;
    <a href="./index.php?m=shop&amp;c=order">Správa objednávok</a> &raquo;
    <a href="./index.php?m=shop&amp;c=order&amp;sp=payment">Správa spôsobov platby</a>
</div>

<strong class="h1">Správa spôsobov platby objednávok</strong>

<div class="left">
	<a href="./index.php?m=shop&amp;c=order&amp;sp=payment&amp;did=0" class="btn2 cust" title="Pridať nový typ doručenia"><strong>+</strong> Pridať nový typ platby</a>
		
        <?php  include dirname(__FILE__)."/order.nav.php"; ?>	
        
</div>
<div class="right">	
    <div class="cbox">
        <strong class="h img article">Správa spôsobov platby objednávok</strong>
            <table class="tc">
              <thead>
                  <tr>
                    <th>Názov spôsobu platby</th>
                    <th>Popis</th>
                    <th>Publikovať</th>
                    <th>Zmazať</th>
                  </tr>
              </thead>
              <tbody class="shop_payment">
              	<?php echo printPayments($conn) ; ?>
              </tbody>
            </table>
    	<div class="clear"></div>
    </div>
    
    <?php if($pyid == 0){ ?>
     <div class="cbox">
        <strong class="h">Pridať nový typ platby</strong>  
            <form name="shop_payment"  class="ajaxSubmit"> 
                <div class="i odd">
                	<label><em>*</em>Názov platby:</label><input  maxlength="45" type="text" class="w300 required" name="payment_name"/>
                </div> 	
                <div class="i">
                	<label>Popis:</label><textarea name="label" class="w520 h70"></textarea>
                </div>
                <div class="i odd">
                    <label>Publikované: </label><select class="w200" name="active">
                    <option value="0">Nepublikovať</option><option value="1">Publikovať</option>
                    </select>
                </div>
                <div class="i">
                    <input type="hidden" value="shop_payment" name="table" />
                    <input type="hidden" value="111" name="act" />
                    <input type="submit" class="ibtn2 cust" value="Pridať" />
                </div>
            </form>
        <div class="clear"></div>
    </div>
	<?php }else{
		$data = $conn->select("SELECT * FROM `shop_payment` WHERE `id_shop_payment`=? LIMIT 1", array( $pyid ) );
			if(count($data) == 0){
				echo "<p class=\"alert\">Položka $pyid nie je v databáze evidovaná</p>";		
			}else{
	?>    
    	<div class="cbox">
        <strong class="h img profile">Úprava spôsov platby</strong>  
            <form name="shop_payment"  class="ajaxSubmit"> 
                <div class="i odd">
                	<label><em>*</em>Názov doručenia:</label><input  maxlength="45" type="text" class="w300 required" name="payment_name" value="<?php echo $data[0]['payment_name'];  ?>" />
                </div> 	
                <div class="i ">
                	<label>Popis:</label><textarea name="label" class="w520 h70"><?php echo $data[0]['label'];  ?></textarea>
                </div>
                <div class="i odd">
                    <label>Publikované: </label><select class="w200" name="active">
                    <?php  echo ($data[0]["active"] == 0 ? '<option value="0">Nepublikovať</option><option value="1">Publikovať</option>' : '<option value="1">Publikovať</option><option value="0">Nepublikovať</option>');?>
                    </select>
                </div>
                <div class="i ">
                	<input type="hidden" value="<?php echo $pyid; ?>" name="id" />
                    <input type="hidden" value="shop_payment" name="table" />
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



