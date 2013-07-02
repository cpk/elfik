<?php
function nav($id){
	global  $config;
	$html = "";
	$arr = getCateg( "categ", 0);		  
	//   <li><a href="#" class="top m1"><strong class="blind">Home</strong></a>
        for($i = 0; $i < count($arr); $i++){
                $html .= '<li class="top m'.$arr[$i]['id_shop_category'].'"><a class="top m'.$arr[$i]['id_shop_category'].
                         '" href="/'.$config['shop_prefix'].'/'.$arr[$i]['link_sk'].'"'.  
                         ' title="'.$arr[$i]['category_name'].'"><strong class="blind">'
                        .$arr[$i]['category_name'].'</strong></a>'.subNav($arr[$i]['id_shop_category'], $arr[$i]['link_sk']).'</li>';	
        }
	
	return $html;
}

function footerNav($id){
	global  $config;
	$html = "";
	$arr = getCateg( "categ", 0);		  
        for($i = 0; $i < count($arr); $i++){
                $html .= '<li><a href="/'.$config['shop_prefix'].'/'.$arr[$i]['link_sk'].'"'.  
                         ' title="'.$arr[$i]['category_name'].'">'.$arr[$i]['category_name'].'</a></li>';	
        }
	return $html;
}


function subNav($subId, $parentLink){

        global $meta, $config;
	$html = "";
	$arr = getCateg("categ", $subId);
        if(count($arr) != 0){
            for($i = 0; $i < count($arr); $i++){
                    $html .= '<li><a href="/'.$config['shop_prefix']."/".$parentLink."/".$arr[$i]["id_shop_category"]."/".$arr[$i]["link_sk"].'"'. 
                            (isset($meta["id_shop_category"]) && $meta["id_shop_category"] == $arr[$i]['id_shop_category'] ? ' class="curr" ' : '')
                            .'>'.$arr[$i]['category_name'].'</a>'.subSubNav($arr[$i]['id_shop_category'], $arr[$i]['link_sk']).'</li>';	
            }
            return '<ul class="sub_menu">'.$html.'</ul>';
        }
	return $html;
}

function subSubNav($subId, $parentLink){

        global $meta, $config;
	$html = "";
	$arr = getCateg("categ", $subId);
        if(count($arr) != 0){
            for($i = 0; $i < count($arr); $i++){
                    $html .= '<li><a href="/'.$config['shop_prefix']."/".$parentLink."/".$arr[$i]["id_shop_category"]."/".$arr[$i]["link_sk"].'"'. 
                            (isset($meta["id_shop_category"]) && $meta["id_shop_category"] == $arr[$i]['id_shop_category'] ? ' class="curr" ' : '')
                            .'>'.$arr[$i]['category_name'].'</a></li>';	
            }
            return '<ul>'.$html.'</ul>';
        }
	return $html;
}

function updateProductHit($idProduct){
    global $conn;
    $conn->update("UPDATE `shop_product` SET `hits`=`hits`+1 WHERE `id_shop_product`=?", array( $idProduct ));
}

function shopBreadcrumb($idProduct){

    $data = getProduct("link", $idProduct);
    $html = ' &raquo; <a href="/shop/'.$idProduct.'/'.SEOlink($data[0]['title_sk']).'">'.$data[0]['title_sk'].'</a>' ; 
    $html = shopFullLinker( $data[0]['id_shop_category'] ). $html; ; 
    
    return $html;
}


function shopFullLinker($cid, $html = ""){
	global $config;
	
	$cat = getCateg("link", $cid);	
	
	if($cat[0]["sub_id"] == 0){
	   return  ' &raquo;  <a href="/'.$config['shop_prefix']."/".$cat[0]["link_sk"].'" title="eshop '.
                   $cat[0]["link_sk"].'">'.$cat[0]["category_name"].'</a>'. $html; 
          
	}else{
	   $html =  ' &raquo;  <a href="/'.$config['shop_prefix']."/".parentCat($cat[0]["sub_id"]).
                   "/".$cat[0]["id_shop_category"]."/".$cat[0]["link_sk"].'" title="eshop '.
                   $cat[0]["link_sk"].'">'.$cat[0]["category_name"].'</a>'.$html; 
           return shopFullLinker($cat[0]["sub_id"], $html);
	}
	
}


function printSlideImages($idArticle){
    $gallery = "";
    if(is_dir(dirname(__FILE__)."/../../data/gallery/".$idArticle."/")){
        $file 	= new File();			 
        $files 	= $file->scanFolder(dirname(__FILE__)."/../../data/gallery/".$idArticle."/");
        $count 	= count($files);
        if($count != 0){
            foreach($files as $fileName){
                $gallery .=  '<img src="/i/817-441-crop/gallery/'.$idArticle.'/'.$fileName.'" alt="eshop s oblečením"/>';
            }
        }
     return $gallery;
    }
}


function getProduct($type = "basic", $id = null){
	global $conn;
	
	switch($type){
		case "link" :
			return $conn->select("SELECT `id_shop_product_status`, `id_shop_category`, `title_sk`, `header_sk` FROM  `shop_product` WHERE `id_shop_product`=? AND `active`=1 LIMIT 1", array( $id ));
		case "basic" :
			return $conn->select("SELECT `id_shop_product_status`, `id_shop_category`, `title_sk`, `header_sk`, `price`, `avatar1` FROM `shop_product` WHERE `id_shop_product`=? AND active=1", array( $id ));
		case "categ" :
			return $conn->select("SELECT  `id_shop_product`,`id_shop_product_status`, `id_shop_category`, `title_sk`, `price`, `price_sale`, `avatar1` 
								  FROM `shop_product` 
								  WHERE `id_shop_category`=? AND active=1".($home ? " AND `home`=1" : ""), array( $id ));
		case "full" :
		default: 
			$p = $conn->select("SELECT * FROM  `shop_product` p,`shop_product_avaibility` a 
                                            WHERE p.`id_shop_product`=? AND p.`active`=1 AND a.id_shop_product_avaibility=p.id_shop_product_avaibility 
                                            LIMIT 1", array( $id ));
			if(!$p) break; 
			$p[0]['shop_manufacturer_name'] = getManufacturerById( $p[0]['id_shop_manufacturer'] );
			return $p;		
	}
}

// ----------------------------------------------------------

function getCateg($type = "categ", $id = NULL){
	global $conn;
	
	switch($type){
		default:
		case "full" :
			return $conn->select("SELECT * FROM  `shop_category` WHERE `id_shop_category`=? AND `active`=1 LIMIT 1", array( $id ));	
		case "categ" :
			return $conn->select("SELECT `id_shop_category`,`category_name`, `link_sk`, `avatar1`, `avatar2` FROM `shop_category` WHERE `sub_id`=? AND active=1 ORDER BY `order` ASC", array( $id ));		
		case "link" :
			return $conn->select("SELECT `id_shop_category`,`category_name`, `link_sk`, `sub_id`, `label` FROM  `shop_category` WHERE `id_shop_category`=? AND `active`=1 LIMIT 1", array( $id ));	
                case "name" :
                    $name =  $conn->select("SELECT `category_name` FROM  `shop_category` WHERE `id_shop_category`=? LIMIT 1", array( $id ));	
                    return (count($name) == 1 ? $name[0]['category_name'] : '');

                    
        }
}

// ----------------------------------------------------------


function shopLinker($cid){
	global $config;
	
	$cat = getCateg($type = "link", $cid);	
	
	if($cat[0]["sub_id"] == 0){
	   return  "/".$config['shop_prefix']."/".$cat[0]["link_sk"]; 
	}else{
	   return  "/".$config['shop_prefix']."/".parentCat($cat[0]["sub_id"])."/".$cat[0]["id_shop_category"]."/".$cat[0]["link_sk"]; 
	}
	
}

// ----------------------------------------------------------

function parentCat($subID){
	$cat = getCateg($type = "link", $subID);
	if($cat[0]["sub_id"] == 0){
		return ($cat[0]["link_sk"]);
	}else{
		return(parentCat($cat[0]['sub_id'])); 
	}
}

// ----------------------------------------------------------

function getManufacturerById($id){
	if(!isset($id) || $id == 1 || $id == 0) return;
	global $conn;
	$r = $conn->select("SELECT `shop_manufacturer_name` FROM `shop_manufacturer` WHERE `id_shop_manufacturer`=? LIMIT 1", array( $id ));
	return $r[0]['shop_manufacturer_name'];
}


function calculatePriceWithDph($price){
	global $meta;
	return number_format( $price * ( $meta['s_dph'] / 100 + 1), 2, ","," ");
}

// ----------------------------------------------------------

function printPrice($data){
	global $meta;
	if(($data['id_shop_product_status'] == 2 || $data['id_shop_product_status'] == 3) && $data['price_sale'] != 0){
		return calculatePriceWithDph($data['price_sale']).' '.$meta['s_currency'];
	}else{
		return calculatePriceWithDph($data['price']).' '.$meta['s_currency'];
	}
}

function percentageSale($data){
    if($data['price_sale'] == 0 || $data['price'] == 0) return '-';
    return str_replace("-",'', floor(($data['price_sale'] - $data['price']) /  $data['price'] * 100))."%";
}

// ----------------------------------------------------------

function printVariants($pid){
	global $conn;
	$html = "";
	$data = $conn->select("SELECT `id_shop_variant`, `shop_variant_name` FROM `shop_variant` WHERE `id_shop_product`=? AND `active`=1", array( $pid ));
	for($i=0; $i < count($data); $i++) {   
		$html .= "<option value=\"".$data[$i]["id_shop_variant"]."\">".htmlspecialchars($data[$i]["shop_variant_name"])."</option>\n";
	}
	return $html; 
}

// ----------------------------------------------------------

function printStatus($id){
	if($id == 2){
		return '<img class="s" src="/img/s/2.png" alt="Akcia!" />'; // akcia
	}elseif($id == 3){
		return '<img class="s" src="/img/s/3.png" alt="Výpredaj!" />'; // vypredaj
	}elseif($id == 4){
		return '<img class="s" src="/img/s/1.png" alt="Novinka!" />'; // novinka
	}
}

// ----------------------------------------------------------

function getNews($limit = 20){
    global $conn, $meta;
    $data = $conn->select(" SELECT `id_shop_product`, `title_sk` 
                            FROM `shop_product` 
                            WHERE id_shop_product_status=4 
                            ORDER BY id_shop_product DESC 
                            LIMIT $limit");
    $html = "";
    for($i = 0; $i < count($data); $i++){
        $href = '/shop/'.$data[$i]['id_shop_product'].'/'.SEOlink($data[$i]['title_sk']);
        $html .= '<li><a href="'.$href.'" title="'.$data[$i]['title_sk'].'">'.  crop($data[$i]['title_sk'], 28).'</a></li>'; 
    }
    return $html;
}

function printPageProducts($home = false, $limit = null){
	global $conn, $meta;
        $html = "";
	if(!$home){
		$ids = getChildersID($meta["id_shop_category"]);
		$where = "WHERE p.`active`=1 AND (p.`id_shop_category`=".implode(" OR p.`id_shop_category`=", $ids).")";
		
		$data = $conn->select("SELECT count(*) FROM `shop_product` p ".$where);
		$count = $data[0]["count(*)"];
		$offset = ($_GET['s'] == 1 ? 0 :  ($_GET['s'] * $meta["s_shopPagi"]) - $meta["s_shopPagi"]);
		$data = $conn->select("SELECT p.`id_shop_product`,p.`id_shop_product_status`,  a.`shop_product_avaibility_name`,p.`top`,
                                             p.`id_shop_category`, p.`title_sk`, p.`price`, p.`price_sale`, p.`avatar1`,p.`id_shop_product_status` 
                                        FROM `shop_product` p, `shop_product_avaibility` a  $where  AND a.`id_shop_product_avaibility`=p.`id_shop_product_avaibility` 
                                        LIMIT $offset, ".$meta["s_shopPagi"]);
                $nav = new Navigator($count, $_GET['s'], shopLinker($meta["id_shop_category"]) , $meta["s_shopPagi"]);
                $nav->setLabelNext("&raquo;");
                $nav->setLabelPrev("&laquo;");
                $html .= $nav->simpleNumNavigator();
                
	}else{
		$data = $conn->select("SELECT p.`id_shop_product`, p.`id_shop_product_status`, p.`id_shop_category`, a.`shop_product_avaibility_name`,  
                                              p.`title_sk`, p.`price`, p.`price_sale`, p.`avatar1`,p.`id_shop_product_status` ,p.`top`
                                        FROM `shop_product` p, `shop_product_avaibility` a
                                        WHERE p.`home`=1 AND p.active=1 AND a.`id_shop_product_avaibility`=p.`id_shop_product_avaibility` 
                                        ORDER BY RAND()
                                        LIMIT 8");
	}

	return $html.generateProduct($data);
}

function printSpecialProduct($type){
        global $conn, $meta;	
        $data = $conn->select("SELECT count(*) FROM `shop_product` WHERE `active`=1 AND `id_shop_product_status`=?", array( $type ));
        $count = $data[0]["count(*)"];
        if($count == 0 && $type == 2){
            return '<p class="alert">Momentálne sa v ponuke nenachádza žiadny akciový tovar.</a>';
        }
        if($count == 0 && $type == 4){
            return ;
        }
        $offset = ($_GET['s'] == 1 ? 0 :  ($_GET['s'] * $meta["s_shopPagi"]) - $meta["s_shopPagi"]);
        $data = $conn->select("SELECT `id_shop_product`,`id_shop_product_status`, `id_shop_category`, `title_sk`, `price`, `price_sale`, `avatar1` 
                                FROM `shop_product` 
                                WHERE `active`=1 AND `id_shop_product_status`=? 
                                LIMIT $offset, ".$meta["s_shopPagi"], array( $type ));

        $nav = new Navigator($count, $_GET['s'], shopLinker($meta["id_shop_category"]) , $meta["s_shopPagi"]);
        $nav->setLabelNext("&raquo;");
        $nav->setLabelPrev("&laquo;");
        $html = $nav->smartNavigator();
                
        return $html.generateProduct($data);
}


function printSimilarProduct($limit = 4){
        global $conn, $meta;
        $ids = getChildersID($meta["id_shop_category"]);
        $where = "WHERE `active`=1 AND (`id_shop_category`=".implode(" OR `id_shop_category`=", $ids).")";
        $data = $conn->select("SELECT `id_shop_product`,`id_shop_product_status`, `id_shop_category`, `title_sk`, `price`, `price_sale`, `avatar1` 
			       FROM `shop_product` $where AND `id_shop_product`!=".$meta["id_shop_product"]." ORDER BY RAND() LIMIT $limit");

        return generateProduct($data);
}

// ----------------------------------------------------------
function generateProduct($data){
    $html = "";
    for($i = 0; $i < count($data); $i++){
        $href = '/shop/'.$data[$i]['id_shop_product'].'/'.SEOlink($data[$i]['title_sk']);
        $html .= '<div class="item">'.
                 '<a href="'.$href.'" class="item-img">'.
                 printShopAvatar($data[$i]['avatar1'], 160, 142, $data[$i]['title_sk']).
                 '</a><a href="'.$href.'" class="item-title" title="Zobraziť">'.$data[$i]['title_sk'].'</a>'.
                 '<div class="item-info">
                        <span class="item-avail">'.$data[$i]['shop_product_avaibility_name'].'</span>
                        <span class="item-price">'.printPrice($data[$i]).'</span>
                    </div>'; 
       if($data[$i]['id_shop_product_status'] == 4) $html .= '<span class="item-status sr-1"></span>';
        if($data[$i]['top'] == 1) $html .= '<span class="item-status sl-1"></span>';
        $html .= '</div>';
    }
    return $html;
}

function printShopAvatar($name, $width, $height, $alt, $class = false){
    if(strlen($name) > 5){
        return '<img '.($class ? ' class="'.$class.'" ' : '').' src="/i/'.$width.'-'.$height.'-crop/avatars/'.$name.'" alt="'.$alt.'" />';
    }
    return '<img '.($class ? ' class="'.$class.'" ' : '').' src="/i/'.$width.'-'.$height.'-crop/avatars/default_avatar.jpg" alt="'.$alt.'" />';
}
// ----------------------------------------------------------

function getChildersID($id){
	$ids = array( $id );
	if(hasChild($id)){
		$ids = iterate($id, $ids);
	}
	return $ids;	
}

function iterate($id, $ids){
	global $conn;
	$data =  $conn->select("SELECT `id_shop_category`, `sub_id` FROM `shop_category` WHERE `sub_id`=? AND `active`=1", array( $id) );
	for($i =0; $i < count( $data ); $i++){
		$ids[] = $data[$i]['id_shop_category'];
		if(hasChild($data[$i]['id_shop_category'])){
			$ids = iterate( $data[$i]['id_shop_category'], $ids);
 		}
	}
	return $ids;	
}

function hasChild($id){
	global $conn;
	return (count($conn->select("SELECT `id_shop_category` FROM `shop_category` WHERE `sub_id`=".$id. " AND `active`=1 LIMIT 1")) == 1 ? true : false);
}

// ----------------------------------------------------------
// BACKET fncs

	function getDeliveryPriceDPH($id, $dph){
		global $conn; 
		$data = $conn->select("SELECT `price`, `dph` FROM `shop_delivery` WHERE `id_shop_delivery`=? LIMIT 1", array( $id ));
		return ($data[0]['dph'] == 1 ? $data[0]['price'] * ($dph / 100 +1) : $data[0]['price']);
	}
        
        function getDeliveryPrice($id){
		global $conn; 
		$data = $conn->select("SELECT `price`, `dph` FROM `shop_delivery` WHERE `id_shop_delivery`=? LIMIT 1", array( $id ));
		return $data[0]['price'];
	}

	function getBacketItem($pid, $vid){
		global $conn;
		
		if($vid == 0){
			$data = $conn->select("SELECT `title_sk`, `avatar1` FROM `shop_product` WHERE `id_shop_product`=? LIMIT 1", array( $pid ));
		}else{
			$data = $conn->select("SELECT p.`title_sk`, p.`avatar1`, v.`shop_variant_name` FROM `shop_product` p, `shop_variant` v WHERE 
			p.`id_shop_product`=? AND v.`id_shop_variant`= ? LIMIT 1", array( $pid , $vid ));
		}
		return $data[0];
	}
	
	
	function printBacket($dph, $curr){
		$html = "";
		foreach($_SESSION['cart'] as $item => $val){
			$i = explode("-", $item);
			$v = explode("-", $val);
			$data = getBacketItem($i[0], $i[1]);
			$html .= 
			'<tr>
				<td>'.printShopAvatar($data['avatar1'], 30, 30, $data['avatar1']).'</td>
				<td>'.$data['title_sk'].(isset($data['shop_variant_name']) ? ', '.$data['shop_variant_name'] : '').'</td>
				<td class="c">'.number_format($v[1] * ($dph / 100 + 1), 2).' '.$curr.'</td>
				<td><input type="text" maxlength="4" value="'.$v[0].'"/><a href=#'.$item.' class="edit" title="Upraviť počet kusov?"></a></td>
				<td class="c"><a href=#'.$item.' class="del" title="Odstrániť položku z košíka?"></a></td>
			</tr>';
		}
		return $html;
	}
	
	
	function sum($cart){
		return '<table><tr><td class="w200">Celková sumna: </td><td class="w100">'.$cart->getTotalPriceWithCurrency().'</td></tr>'.
				'<tr><td class="w200">DPH '.$cart->getPercentageDph().'%:</td><td class="w100">'.$cart->getDph().'</td></tr>'.
				'<tr><td class="w200">Celková suma s DPH:</td><td class="w100">'.$cart->getTotalPriceWithCurrencyAndDPH().'</td></tr></table><div class="clear"></div>';
	}

// ----------------------------------------------------------

function getMailText($tID){
	global $conn;
	$r = $conn->select("SELECT `val` FROM `shop_config_text` WHERE `key`='$tID' LIMIT 1");
	return $r[0]["val"];
}

function printMailItems($dph, $curr, $totalPrice){
	$html = '<br><h3>Položky objednávky</h3><table border="1" cellspacing="0" cellpadding="0"><tr><td align="left"><b>Názov tovaru</b></td><td align="right"><b>Cena s DPH</b></td><td><b>Počet</b></td><td align="right"><b>Cena spolu</b></td></tr>';
	
	foreach($_SESSION['cart'] as $item => $val){
			$i = explode("-", $item);
			$v = explode("-", $val);
			$data = getBacketItem($i[0], $i[1]);
			$html .= 
			'<tr>'.
				'<td>'.$data['title_sk'].(isset($data['shop_variant_name']) ? ', '.$data['shop_variant_name'] : '').'</td>'.
				'<td align="right">'.number_format($v[1] * ($dph / 100 + 1), 2).' '.$curr.'</td>'.
				'<td align="center">'.$v[0].'</td>'.
				'<td align="right">'.number_format( $v[1] * ($dph / 100 + 1) * $v[0], 2).' '.$curr.'</td>'.
			'</tr>';
		}
		$d = explode("-", $_SESSION['dp']);
		if($d[0] > 1){	
		$html .= '<tr>'.
				 '<td>'.getDeliveryName( $d[0] ).'</td>'.
				 '<td align="right">'.($d[0] == 2 ? $d[2] : number_format($d[2] * ( $dph / 100 + 1), 2)).' '.$curr.'</td>'.
				 '<td align="center">1</td>'.
				 '<td align="right">'.($d[0] == 2 ? $d[2] : number_format($d[2] * ( $dph / 100 + 1), 2)).' '.$curr.'</td></tr>';	 
		}
		
		$html .= '<tr><td><b>Celkom k úhrade:</b></td><td align="right"><b>'.number_format(getTotalOrderPrice($dph, $totalPrice, $d[0],  $d[2]), 2).' '.$curr.'</b></td></tr>';
		return $html.'<table>';
	
}

function pritShopGallery($id, $width, $height, $altTitle = ""){
    $gallery = "";
    if(is_dir(dirname(__FILE__)."/../../data/shop/".$id."/")){
        $file 	= new File();			 
        $files 	= $file->scanFolder(dirname(__FILE__)."/../../data/shop/".$id."/");
        $count 	= count($files);
        if($count != 0){
            foreach($files as $fileName){
                $gallery .=  '<a href="/i/700-700-auto/shop/'.$id.'/'.$fileName.'" rel="lightbox">'.
                             '<img src="/i/'.$width.'-'.$height.'-crop/shop/'.$id.'/'.
                             $fileName.'" alt="'.$altTitle.'"/></a>';
            }
            $gallery = '<div id="gallery"><strong class="gallery">Galéria</strong>'.$gallery.'<div class="clear"></div></div>';
        }
     return $gallery;
    }
}

function getTotalOrderPrice($dph, $itemsPrice, $dID, $dPrice){
        if($dID == 2)
                return $itemsPrice * ($dph / 100 + 1) + $dPrice;
        return ($itemsPrice + $dPrice) * ($dph / 100 + 1);
}


function getDeliveryName( $id ){
	global $conn;
	$r =  $conn->select("SELECT `delivery_name` FROM `shop_delivery` WHERE `id_shop_delivery`=? LIMIT 1", array( $id ) );	
	return $r[0]['delivery_name'];
}

function getPaymentName( $id ){
	global $conn;
	$r =  $conn->select("SELECT `payment_name` FROM `shop_payment` WHERE `id_shop_payment`=? LIMIT 1", array( $id ) );	
	return $r[0]['payment_name'];
}


function sendOrderInfoMail($config, $orderId){
   global $conn; 
	
    $mc = new MailContent($conn, $orderId);
    $mc->generateMailContent();
	
    if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){
        $mail = new PHPMailer();
		$mail->AddAddress( trim( $mc->getCustomerMail()) );
		$mail->SetFrom("sender@chicfashion.sk", $config["c_name"]);
		$mail->AddReplyTo(trim($config["s_fa_mail"]), $config["c_name"]);
        $mail->WordWrap = 120; 
        $mail->IsHTML(true);
        $mail->Subject = $mc->getSubject();
        $mail->Body    = $mc->getBody();
        $mail->Send();
    }
    
}


function printShopCateg($id, $width, $height){
    global $conn, $meta;
    $html ="";
    $arr = getCateg("categ", $id);
    for($i = 0; $i < count($arr); $i++){
        $html .= '<li><a '.($meta['id_shop_category'] == $arr[$i]['id_shop_category'] || 
                            $meta['sub_id'] == $arr[$i]['id_shop_category'] ? ' class="curr"' :'').
                ' href="'.shopLinker($arr[$i]['id_shop_category']).'" title="'.$arr[$i]['category_name'].'"><span>
                '.printShopAvatar($arr[$i]['avatar1'], $width, $height, $arr[$i]['category_name'])
                 .printShopAvatar($arr[$i]['avatar2'], $width, $height, $arr[$i]['category_name'], "hover")
                .$arr[$i]['category_name'].'</span></a></li>';	
    }
    return $html;
}


function getStoreStatus($count, $status){
        if($status == 3){
            return 'Možné predobjednávky';
        }else if($count <= 0){
            return 'Skladom 0 ks';
        }else if($count > 0 && $count <= 3){
            return 'Skladom '.$count.' ks';
        }elseif($count > 3){
            return 'Skladom 3+ ks';
        }
    
    
}
