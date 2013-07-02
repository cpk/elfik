<section>
<?php 
	try{
		include  dirname(__FILE__)."/setting.fnc.php";
		if(!isset($_GET['sp'])){
				include "setting.general.php";
		}else{
			switch ($_GET['sp']){
				case "general" :
						include dirname(__FILE__)."/setting.general.php";
					break;
				case "fa" :
						include dirname(__FILE__)."/setting.fa.php";
					break;
				case "text" :
						include dirname(__FILE__)."/setting.text.php";
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