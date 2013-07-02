<section>
<?php 
	try{
		include  dirname(__FILE__)."/order.fnc.php";
		if(!isset($_GET['sp'])){
				include "order.view.php";
		}else{
			switch ($_GET['sp']){
				case "new" :
						include dirname(__FILE__)."/order.new.php";
					break;
				case "view" :
						include dirname(__FILE__)."/order.view.php";
					break;
				case "view2" :
						include dirname(__FILE__)."/order.view2.php";
					break;	
				case "edit" :
						include dirname(__FILE__)."/order.edit.php";
					break;
				case "delivery" :
						include dirname(__FILE__)."/order.delivery.php";
					break;
				case "payment" :
						include dirname(__FILE__)."/order.payment.php";
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