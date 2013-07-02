<section>
<?php 
	try{
		include  dirname(__FILE__)."/product.fnc.php";
		if(!isset($_GET['sp'])){
				include "product.view.php";
		}else{
			switch ($_GET['sp']){
				case "new" :
						include dirname(__FILE__)."/product.new.php";
					break;
				case "edit" :
						include dirname(__FILE__)."/product.edit.php";
					break;
				case "view" :
						include dirname(__FILE__)."/product.view.php";
					break;
				case "manufacturer" :
						include dirname(__FILE__)."/product.manufacturer.php";
					break;
				case "status" :
						include dirname(__FILE__)."/product.status.php";
					break;
				case "avaibility" :
						include dirname(__FILE__)."/product.avaibility.php";
					break;	
				case "variant" :
						include dirname(__FILE__)."/product.variant.php";
					break;	
				default :
					echo "<strong class=\"error\">404 - Požadovaná stránka sa nenašla</strong>";
			
			}
		}
	}catch(MysqlException $ex){
		echo "<strong class=\"error\">Vyskytol sa problém s databázou, operáciu skúste zopakovať</strong>";
	}
	
?>
</section>