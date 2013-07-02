<?php 
if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
if(!isset($_GET['cid'])){ $cid = 0; } else { $cid = intval($_GET['cid']); } 
?>

<div class="breadcrumb">Nachádzate sa:
	<a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?m=shop">Internetový obchod</a> &raquo;
    <a href="./index.php?m=shop&amp;c=product&amp;sp=view&amp;f=1">Správa tovaru</a>
</div>

<strong class="h1">Správa tovaru</strong>

<div class="left">
		<a href="./index.php?m=shop&amp;c=product&amp;sp=new" class="btn2 cust" title="Pridať nový produkt"><strong>+</strong> Pridať nový tovar</a>
		<strong class="h">Kategórie</strong>
        <a class="uncategory" href="./index.php?m=shop&amp;c=product&amp;sp=view&amp;cid=0&amp;f=0">Nekategorizovavý tovar</a>
        <a class="all <?php echo (isset($_GET['f']) ? 'b':'');?>" href="./index.php?m=shop&amp;c=product&amp;sp=view&amp;cid=0&amp;f=1">Zobraziť všetko</a>
        <ul id="tree">
        
        <?php  echo categTree($conn, 0, $cid); ?>	
        </ul>
</div>
<script>
	$(function() {$('#tree').treeview({animated: "fast",collapsed: true,unique: false,persist: "cookie"}); 
		
		$(".tca1").live({
		  mouseover: function() {
			var o = $(this),
				a = o.attr("href").replace("#","");
				if(a.length === 0){
					o.find('img').show();
				}else{
					o.load( './modules/shop/inc/img.php?a='+ a,  function() {
						 var i = o.find('img'), p = o.position();
						 i.css({'top' :  -1*i.outerHeight()-20 }).show()
						 o.attr("href", "#");
					});
				}
		  },
		  mouseout: function() {
			$('tbody').find('img.tcav').hide();
		  }
		});
	});
</script>
<div class="right">
	<form class="shopSearch">
    	<input type="text" name="q" id="shop_product-title_sk" class="w200" />
        <input type="hidden" name="url" value="<?php echo $_SERVER['QUERY_STRING']; ?>" />
        <input type="hidden" name="table" value="shop_product" />
        <input type="submit" class="ibtn"  value="Hladať" />
    </form>
	
    <div class="cbox">
        <strong class="h img article">Zoznam tovarov</strong>
        <div class="breadcrumb">
        Zobrazené: <?php echo categoryBC($conn, $cid ,"&raquo;"); ?>
        </div>          
        
        
        <form class="ajaxSubmit" id="filter">
            	<div class="fl">
                	<div class="bx">
                        <span>Zoradiť podľa:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>	
                        <select class="w150"  name="order">
                           <?php 
						   	if(!$_GET['order']) $_GET['order'] = 1; 
						   	$opts = array( '<option value="1">Najnovšieho</option>', '<option value="2">Najstaršieho</option>', '<option value="3">Ceny vzostupne &uarr;</option>', '<option value="4">Ceny zostupne &darr;</option>', '<option value="5">Názvu A-Z</option>', '<option value="6">Názvu Z-A</option>' );
								echo $opts[$_GET['order']-1];
								unset($opts[$_GET['order']-1]);
								echo implode("", $opts);
						   ?>
                        </select>
                    </div>
                        <div class="bx">
                        <span>Kód / ID tovaru:&nbsp;&nbsp;</span>	
                        <input type="text" class="w150" name="code" value="<?php echo (isset($_GET['code']) ? $_GET['code'] : ''); ?>" />
                    </div>
                </div>
                <div class="fl">
                	 <div class="bx cust">
                        <span>Zľavnený:</span><select name="sale" class="w50"><?php echo opts(0); ?></select>	
                        <span>Publikovaný:</span><select name="active" class="w50"><?php echo opts(0); ?></select>
                        <span>Na úvodnej str.:</span><select name="home" class="w50"><?php echo opts(0); ?></select>
                    </div>
                    <div class="bx">
                        <span>Ceny (<?php echo $config['s_currency']; ?>) od:</span>	
                        <input type="text" class="w70" name="priceFrom"  value="<?php echo (isset($_GET['priceFrom']) ? $_GET['priceFrom'] : ''); ?>"/>
                        <span>do:</span>	
                        <input type="text" class="w70" name="priceTo"  value="<?php echo (isset($_GET['priceTo']) ? $_GET['priceTo'] : ''); ?>"/>
                    </div>
                    
                </div>
                <input type="submit" class="ibtn abs" value="Filtrovať"  />
                <input type="hidden" value="122" name="act"  />
                <input type="hidden" name="cid" value="<?php echo $cid; ?>"  />  
                <input type="hidden" name="f" value="<?php echo $_GET['f']; ?>"  />           
                <div class="clear"></div>
             </form>
        
        
        <table class="tc" id="dnd">
          <thead>
              <tr class="nodrop nodrag">
              	<th scope="col">&nbsp;</th>
                <th scope="col">&nbsp;ID</th>
                <th scope="col">&nbsp;Názov</th>
                <th scope="col">&nbsp;Publikovať</th>
                <th scope="col">&nbsp;Zobrazená</th>
                <th scope="col">&nbsp;Zmenená</th>
                <th scope="col">&nbsp;Zmazať</th>
              </tr>
          </thead>
          <tbody class="shop_product">
            <?php echo printProducts($conn, $cid, (isset($_GET['q']) ? $_GET['q'] : NULL), $s) ; ?>
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



