<?php
	session_start();
	session_regenerate_id(true);
        //$_SESSION['cart'] = array();
	if(!isset($_GET['s']))	{ $_GET['s'] = 1; }else{  $_GET['s'] = intval( $_GET['s'] ); }
	
	require_once "admin/config.php";
	require_once "admin/inc/fnc.main.php";
	require_once "admin/page/fnc.page.php";
	require_once "admin/page/fnc.shop.php";
		
	function __autoload($class) {
		require_once 'admin/inc/class.'.$class.'.php';
	}
	
	
	try{
		$lang = "sk";
		$conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);
		$meta = MAIN();
	}catch(MysqlException $e){
		echo $e->getMessage();
	} 
	if(intval($meta['c_status']) == 0){
		die($meta['c_offline_msg']);
	}

	$meta['s_currency'] = str_replace("EUR", "&euro;", $meta['s_currency']);
	
	
        $cart = new Cart($meta['s_currency'], $meta['s_dph']);
	$cart->calculate();
		
	$nav = printMenu(0, "", -1, false);
	//print_r($meta);
        
        function pritParentCatName(){
            global $meta;
            if(isset($_GET['cid']) &&
               $_GET['cid'] != 0 && 
               $_GET['cid'] != "index" &&
               $_GET['cid'] != "home" )
                return " - ". $meta["parentCategName"];
        }
       
?>
<!DOCTYPE HTML>
<html>
<head>
    <title><?php echo $meta["title_${lang}"]. pritParentCatName()." - ".$meta['c_name']; ?></title>
    <meta charset="utf-8" />
    <meta name="robots" content="<?php  echo $meta['c_robots']; ?>"/>
    <meta name="description" content="<?php  echo substr($meta["header_${lang}"], 0 , 200); ?>"/>
    <!-- styles & js -->
    <link rel="stylesheet" href="/css/main.css" />
    <link rel="shortcut icon" href="/img/favicon.png" />	
    <link rel="stylesheet" href="/css/ui-lightness/jquery-ui-1.8.16.custom.css" /> 
    <link rel="stylesheet" href="/css/jquery.lightbox-0.5.css" />
    <link rel="stylesheet" href="/css/slider.css" />
     <!--[if IE]>
		  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
      <![endif]-->
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script>!window.jQuery && document.write('<script src="/admin/js/jquery.min.js"><\/script>')</script>
    <script src="/js/jquery-ui-1.8.16.custom.min.js"></script>
    <script src="/js/jquery.lightbox-0.5.min.js"></script>
    <script src="/js/jquery.slider.js"></script>
    <script src="/js/scripts.js"></script>
	<script type="text/javascript">
  var _gaq = _gaq || []; _gaq.push(['_setAccount', 'UA-27772875-1']); _gaq.push(['_trackPageview']);
  (function() {   var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);  })();
</script>
</head>
<body>  
        <?php  //echo  '<div id="debug">';  print_r($meta);  echo '</div>'; ?>
	<div id="status"><!-- ajax cb --></div>
        
        <!-- top box   -->
        <div id="top-box">
           
            <ul class="vop">
                <li><?php echo linkToPage(6); ?></li>
                <li><?php echo linkToPage(13); ?>&ensp;&ensp;&ensp;</li>
            </ul>
            
            <a id="logo" href="/shop" title="<?php echo $meta["s_title"];?>">
                <img src="/img/logo.jpg" alt="<?php echo $meta['s_company'];?>" />
            </a>
            <a href="/" id="fb" title="Facebook stránka">
                <img src="/img/fb.png" alt="<?php echo $meta['s_company'];?>" />
            </a>
            
            <a id="price" href="/shop/kosik" title="Zobraziť nákupný košík"><?php echo $cart->getTotalPriceWithCurrencyAndDPH()?></a>
            
        </div>
        
        
        <!-- TOP BOX / HEADER   -->
        <nav>
            <ul class="toplevel"><?php echo nav(0); ?></ul>
        </nav>
        
        
        

                <?php

            try{    
                if($_GET['p'] == "shop"){
                        if($_GET['cn'] == "kosik"){
                                include 'inc/shop.backet.php';
                        }elseif($_GET['cn'] == "kosik2"){
                                include 'inc/shop.backet2.php';
                        }elseif($_GET['cn'] == "kosik3"){
                                include 'inc/shop.backet3.php';
                        }elseif(isset($_GET['pid'])) {
                                include 'inc/shop.detail.php';
                        }else{
                                include 'inc/shop.php';
                        }

                }else{
                        include 'inc/article.php';
                }
            }catch(MysqlException $e){
                echo '<p class="error">Vyskytol sa problém s databázou, operáciu skúste zopakovať.</p>';
            } 

           
            ?>
                
        
         <!-- FOOTER   -->
        <footer>
            <div class="footer-box">
                <strong>E-SHOP</strong>
                <ul class="uppercase">
                    <?php echo footerNav(0); ?>
                </ul>
            </div>
            
            <div class="footer-box">
                <strong class="uppercase"><?php echo getArticleTitle(1); ?></strong>
                <ul>
                    <?php echo printMenu(1, '', -1, false); ?>
                </ul>
            </div>
            
            <div class="footer-box">
                <strong class="uppercase"><?php echo getArticleTitle(2); ?></strong>
               <ul>
                    <?php echo printMenu(2, '', -1, false); ?>
                </ul>
            </div>
            
            <div class="footer-box">
                <strong class="uppercase">Kontakt</strong>
                <ul  class="contact">
                    <li><?php echo $meta['s_name']; ?></li>
                    <li>tel: +421 <?php echo $meta['s_mobil']; ?></li>
                    <li>E-mail: <a class="email" href="mailto:<?php echo $meta['s_fa_mail']; ?>"><?php echo $meta['s_fa_mail']; ?></a></li>
                    <li><?php echo $meta['s_street'].', '.$meta['s_city'].' '.$meta['s_zip']; ?></li>
                </ul>    
            </div>
            <div class="clear"></div>
            <div id="footer">&copy; <?php echo date("Y")." ".$meta['s_company'];?> &nbsp;&nbsp;|&nbsp;&nbsp;design by: <a href="http://www.skifi.sk" target="_blank">www.skifi.sk</a></div>
        </footer>
        
        

</body>
</html>
