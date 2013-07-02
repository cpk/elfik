<?php 
if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
if(!isset($_GET['cid'])){ $cid = 0; } else { $cid = intval($_GET['cid']); } 
?>

<div class="breadcrumb">Nachádzate sa:
	<a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?m=shop">Internetový obchod</a> &raquo;
    <a href="./index.php?m=shop&amp;c=product">Správa tovaru</a> &raquo;
    <a href="./index.php?m=shop&amp;c=product&amp;sp=variant">Správa plniek</a>
</div>

<strong class="h1">Správa plniek</strong>

<div class="left">
		
        <?php  include dirname(__FILE__)."/product.nav.php"; ?>	
        
</div>
<div class="right">	
    <div class="cbox">
        <strong class="h img article">Správa plniek</strong>
        <form class="inlineEditing">
            <table class="tc inline">
              <thead>
                  <tr>
                    <th class="il text-name required">Názov plnky</th>
                    <th>Upraviť</th>
                    <th>Zmazať</th>
                  </tr>
              </thead>
              <tbody class="shop_product_variant">
              	<?php echo printVariant($conn) ; ?>
              </tbody>
            </table>
            	<input type="hidden" name="act" value="3" />
                <input type="hidden" value="shop_product_variant" name="table" />
			</form>
            
            <div class="addBox">
            	<em>Pridanie novej plnky</em>
            	<form class="ajaxSubmit block">
                	<span><strong>*</strong>Názov: </span><input type="text" name="name" maxlength="55"  class="w200 required"  />
                    <input type="hidden" value="shop_product_variant" name="table" />
                    <input type="hidden" value="21" name="act" />
                    <input type="submit" class="ibtn cust" value="Pridať" />
                    <div class="clear"></div>
                </form>
            </div>
    
    	<div class="clear"></div>
    </div>
    
    
        
</div>
<div class="clear"></div>



