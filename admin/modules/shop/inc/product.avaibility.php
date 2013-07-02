<?php 
if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
if(!isset($_GET['cid'])){ $cid = 0; } else { $cid = intval($_GET['cid']); } 
?>

<div class="breadcrumb">Nachádzate sa:
	<a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?m=shop">Internetový obchod</a> &raquo;
    <a href="./index.php?m=shop&amp;c=product">Správa tovaru</a> &raquo;
    <a href="./index.php?m=shop&amp;c=product&amp;sp=avaibility">Správa dostupnosti tovaru</a>
</div>

<strong class="h1">Správa dostupnosti tovaru</strong>

<div class="left">
		
        <?php  include dirname(__FILE__)."/product.nav.php"; ?>	
        
</div>
<div class="right">	
    <div class="cbox">
        <strong class="h img article">Správa dostupnosti tovaru</strong>
        <form class="inlineEditing">
            <table class="tc inline">
              <thead>
                  <tr>
                    <th class="il text-shop_product_avaibility_name required">Názov dostupnosti</th>
                    <th class="il textarea-label">Popis</th>
                    <th>Upraviť</th>
                    <th>Zmazať</th>
                  </tr>
              </thead>
              <tbody class="shop_product_avaibility">
              	<?php echo printAvaibility($conn) ; ?>
              </tbody>
            </table>
            	<input type="hidden" name="act" value="3" />
                <input type="hidden" value="shop_product_avaibility" name="table" />
			</form>
            
            <div class="addBox">
            	<em>Pridanie novej dostupnosti tovaru</em>
            	<form class="ajaxSubmit block">
                	<span><strong>*</strong>Názov: </span><input type="text" name="shop_product_avaibility_name" maxlength="150"  class="w200 required"  />
                    <span>Web: </span><textarea name="label" cols="20" rows="5"  class="w200" ></textarea>
                    
                    <input type="hidden" value="shop_product_avaibility" name="table" />
                    <input type="hidden" value="7" name="act" />
                    <input type="submit" class="ibtn cust" value="Pridať" />
                    <div class="clear"></div>
                </form>
            </div>
    
    	<div class="clear"></div>
    </div>
    
    
        
</div>
<div class="clear"></div>



