<?php 
if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
if(!isset($_GET['sid'])){ $sid = 0; } else { $sid = intval($_GET['sid']); } 
?>
<script>
	$(function() {
				$('.date').datepicker({
				dayNamesMin: ['Ne', 'Po', 'Út', 'St', 'Št', 'Pi', 'So'], 
				monthNames: ['Január','Február','Marec','Apríl','Máj','Jún','Júl','August','September','Október','November','December'], 
				maxDate: 0,
				autoSize: false,
				dateFormat: 'dd.mm.yy',
				firstDay: 1
				});
	});
</script>
<div class="breadcrumb">Nachádzate sa:
	<a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?m=shop">Internetový obchod</a> &raquo;
    <a href="./index.php?m=shop&amp;c=order">Správa objednávok</a> &raquo;
    <a href="./index.php?m=shop&amp;c=order&amp;sp=view">Zoznam objednávok</a>
</div>

<strong class="h1">Zoznam objednávok</strong>

<div class="left">
        <?php  include dirname(__FILE__)."/order.statuses.php"; ?>	
        
</div>
<div class="right">	


	
    <div class="cbox">
        <strong class="h img orders">Objednávky</strong>
             <form class="ajaxSubmit" id="filter">
            	<div class="fl">
                	<div class="bx">
                        <span>Zoradiť podľa:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>	
                        <select class="w150"  name="order">
                           <?php 
						   	if(!isset($_GET['order'])) $_GET['order'] = 1; 
						   	$opts = array( '<option value="1">Najnovších</option>', '<option value="2">Najstarších</option>', '<option value="3">Ceny vzostupne &uarr;</option>', '<option value="4">Ceny zostupne &darr;</option>', 
                            				'<option value="5">Mena A-Z</option>', '<option value="6">Priezviska A-Z</option>' );
								echo $opts[$_GET['order']-1];
								unset($opts[$_GET['order']-1]);
								echo implode("", $opts);
						   ?>
                        </select>
                    </div>
                        <div class="bx">
                        <span>Číslo objednávky:&nbsp;&nbsp;</span>	
                        <input type="text" class="w150" name="oid" value="<?php echo (isset($_GET['oid']) ? $_GET['oid'] : ''); ?>" />
                    </div>
                </div>
                <div class="fl">
                    <div class="bx">
                        <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dátumu od:</span>	
                        <input type="text" class="date w70" name="dateFrom" value="<?php echo (isset($_GET['dateFrom']) ? $_GET['dateFrom'] : ''); ?>"/>
                        <span>do:</span>	
                        <input type="text" class="date w70" name="dateTo" value="<?php echo (isset($_GET['dateTo']) ? $_GET['dateTo'] : ''); ?>" />
                    </div>
                    <div class="bx">
                        <span>Ceny (<?php echo $config['s_currency']; ?>) od:</span>	
                        <input type="text" class="w70" name="priceFrom"  value="<?php echo (isset($_GET['priceFrom']) ? $_GET['priceFrom'] : ''); ?>"/>
                        <span>do:</span>	
                        <input type="text" class="w70" name="priceTo"  value="<?php echo (isset($_GET['priceTo']) ? $_GET['priceTo'] : ''); ?>"/>
                    </div>
                </div>
                <input type="submit" class="ibtn" value="Filtrovať"  />
                <input type="hidden" value="12" name="act"  />
                <input type="hidden" name="sid" value="<?php echo $sid; ?>"  />           
                <div class="clear"></div>
             </form>
            <table class="tc">
              <thead>
                  <tr>
                    <th>Číslo obj.</th>
                    <th>Hodnota</th>
                    <th>Objednávateľ</th>
                    <th>Stav obejdnávky</th>
                    <th>Prijatá</th>
                    <th>Zmazať</th>
                  </tr>
              </thead>
              <tbody class="shop_order">
              	<?php echo printOrders($conn, $s) ; ?>
              </tbody>
            </table>
            
            
        <div id="pagi">
			<?php 
            $nav = new Navigator($config['count'], $s , './index.php?'.preg_replace("/&s=[0-9]/", "", $_SERVER['QUERY_STRING']) , $config["adminPagi"]);
            $nav->setSeparator("&amp;s=");
            echo $nav->smartNavigator();
            ?>
        </div>
        
        
    	<div class="clear"></div>
    </div>
  
  
  
</div>
<div class="clear"></div>



