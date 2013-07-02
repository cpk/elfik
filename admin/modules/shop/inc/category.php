<section>
<?php 
	try{
		include  dirname(__FILE__)."/category.fnc.php";
		if(!isset($_GET['sp'])){
				include "category.view.php";
		}else{
			switch ($_GET['sp']){
				case "view" :
						include dirname(__FILE__)."/category.view.php";
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