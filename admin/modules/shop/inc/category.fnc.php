<?php 

// PRODUCT FNCs

// PRINT & SEARCH PRODUCT ----------------------------------------------------------------------

function printCategories($conn, $id, $newID = NULL){
	global $config;

	if($newID == NULL){
		$data = $conn->select("SELECT `id_shop_category`, `sub_id`, `category_name`, `active`, `order`, `label` FROM  `shop_category` WHERE `sub_id`=? ORDER BY `order`" , array( $id ));
	}else{
		$data = $conn->select("SELECT `id_shop_category`, `sub_id`, `category_name`, `active`, `order`, `label` FROM  `shop_category` WHERE `id_shop_category`=? LIMIT 1" , array( $newID ));
	}
	
	if(count($data) == 0){
		return "<p class=\"alert\">Kategória neobsahuje žiadne podkategórie.</p>";
	}
	
	$html = "";

	for($i = 0; $i < count($data); $i++ ){
		$data[$i] = array_map("clean", $data[$i]);
		$html .= '<tr id="id'.$data[$i]['id_shop_category'].'"   class="o'.$data[$i]['order'].($newID != NULL ? ' mark' : '').'">'.
				 '<td class="w200"><a title="Upraviť kategóriu?" class="edit" href="./index.php?m=shop&c=category&sp=view&cid='.$data[$i]['id_shop_category'].'">'.$data[$i]['category_name'].'</a></td>'.
				 '<td >'.crop($data[$i]['label'], 50).'</td>'.
				 '<td class="c w45"><a href="#id'.$data[$i]['id_shop_category'].'" title="Zmeniť aktivnosť ?" class="'.($data[$i]['active'] == 1 ? "a1" : "a0" ).'" ></a></td>'.
				 '<td class="c order w45"></td>'.
				 '<td class="w45"><a class="del" title="Odstrániť stránku ?" href="#id'.$data[$i]['id_shop_category'].'" ></a></td></tr>';
	}
	return $html; 
}


function isUniqueCateg($conn, $cid, $title, $subID = NULL){
	if($subID == NULL){
	$r = $conn->select("SELECT count(*) FROM  `shop_category` WHERE `sub_id`=? AND `link_sk`=?", array( $cid , SEOlink($title) )); 
	}else{
	$r = $conn->select("SELECT count(*) FROM  `shop_category` WHERE `sub_id`=? AND `link_sk`=? AND `id_shop_category`<>?", 
						array( $subID , SEOlink($title), $cid )); 
	}
	return ($r[0]["count(*)"] == 0 ? true : false);
}


?>