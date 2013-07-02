<?php 
if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
if(!isset($_GET['cid'])){ $cid = 0; } else { $cid = intval($_GET['cid']); } 
?>

<div class="breadcrumb">Nachádzate sa:
	<a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?m=shop">Internetový obchod</a> &raquo;
    <a href="./index.php?m=shop&amp;c=product">Správa tovaru</a>
    <a href="./index.php?m=shop&amp;c=product&amp;sp=manufacturer">Správa Výrobcov</a>
</div>

<strong class="h1">Správa výrobcov tovaru</strong>

<div class="left">
		
        <?php  include dirname(__FILE__)."/product.nav.php"; ?>	
        
</div>
<div class="right">
	<form class="shopSearch">
    	<input type="text" name="q" id="shop_manufacturer-shop_manufacturer_name" />
        <input type="hidden" name="url" value="<?php echo $_SERVER['QUERY_STRING']; ?>" />
        <input type="hidden" name="table" value="shop_manufacturer" />
        <input type="submit" class="ibtn"  value="Hľadať" />
    </form>
	
    <div class="cbox">
        <strong class="h img article">Zoznam výrobcov</strong>
    	<div class="breadcrumb"></div>
        <form class="inlineEditing">
            <table class="tc inline">
              <thead>
                  <tr>
                    <th>ID</th>
                    <th class="il text-shop_manufacturer_name required">Názov výrobcu</th>
                    <th class="il text-web">Web stránka výrobcu</th>
                    <th>Upraviť</th>
                    <th>Zmazať</th>
                  </tr>
              </thead>
              <tbody class="shop_manufacturer">
              	<?php echo printManufacturers($conn, (isset($_GET['q']) ? $_GET['q'] : NULL), $s) ; ?>
              </tbody>
            </table>
            	<input type="hidden" name="act" value="3" />
                <input type="hidden" value="shop_manufacturer" name="table" />
			</form>
             <div id="pagi">
				<?php 
                $nav = new Navigator($config['count'], $s , './index.php?'.preg_replace("/&s=[0-9]/", "", $_SERVER['QUERY_STRING']) , $config["adminPagi"]);
                $nav->setSeparator("&amp;s=");
                echo $nav->smartNavigator();
                ?>
        	</div>
            <div class="clear"></div>
            <div class="addBox margin70">
            	<em>Pridanie nového výrobcu</em>
            	<form class="ajaxSubmit">
                	<span><strong>*</strong>Názov: </span><input type="text" name="shop_manufacturer_name" maxlength="50"  class="w200 required"  />
                    <span>Web: </span><input type="text" name="web"  maxlength="50"  class="w200" />
                    
                    <input type="hidden" value="shop_manufacturer" name="table" />
                    <input type="hidden" value="5" name="act" />
                    <input type="hidden" name="url" value="<?php echo $_SERVER['QUERY_STRING']; ?>" />
                    <input type="submit" class="ibtn cust" value="Pridať" />
                    <div class="clear"></div>
                </form>
            </div>
    
    	<div class="clear"></div>
    </div>
    
    
        
</div>
<div class="clear"></div>



