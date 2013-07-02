<?php
	include "./modules/shop/inc/fnc.shop.php";
	
	$config = array_merge($config, getConfig($conn, "`config`", $type = "shop"));
	$config["adminPagi"] = $config["s_adminPagi"];
?>
	<nav>
    	<ul>
        	<li class="t"><a class="home"  href="./">Ovládací panel</a></li>
            <li class="t"><a class="order" href="./index.php?m=shop&amp;c=order&amp;sp=view">Správa Objednávok</a>
            	<ul>
                    <li><a href="./index.php?m=shop&amp;c=order&amp;sp=view">Zobraziť obejdnávky</a></li>
                    <li><a href="./index.php?m=shop&amp;c=order&amp;sp=view2">Kalendár obejdnávok</a></li>
                    <li><a href="./index.php?m=shop&amp;c=order&amp;sp=new">Pridať objednávku</a></li>
                    <li><a href="./index.php?m=shop&amp;c=order&amp;sp=delivery">Správa doručovania</a></li>
                    <li><a href="./index.php?m=shop&amp;c=order&amp;sp=payment">Správa spôsobov platby</a></li>
                </ul>
            </li>
            <li class="t"><a class="product" href="./index.php?m=shop&amp;c=product&amp;sp=view&amp;f=1">Správa tovaru</a>
            	<ul>
                    <li><a href="./index.php?m=shop&amp;c=product&amp;sp=view&amp;f=1">Zobraziť tovar</a></li>
                    <li><a href="./index.php?m=shop&amp;c=product&amp;sp=new">Pridať nový tovar</a></li>
                    <li><a href="./index.php?m=shop&amp;c=product&amp;sp=manufacturer">Správa výrobcov</a></li>
                    <li><a href="./index.php?m=shop&amp;c=product&amp;sp=status">Správa statusov tovaru</a></li>
                    <li><a href="./index.php?m=shop&amp;c=product&amp;sp=avaibility">Správa dostupnosti tovaru</a></li>
                </ul>
            </li>
            <li class="t"><a class="article" href="./index.php?m=shop&amp;c=category&amp;sp=view">Správa kategórií</a></li>
            <li class="t"><a class="users" href="./index.php?m=shop&amp;c=user&amp;sp=view">Správa zákazníkov</a>
            	<ul>
                    <li><a href="./index.php?m=shop&amp;c=user&amp;sp=view">Zobraziť zákazníkov</a></li>
                    <li><a href="./index.php?m=shop&amp;c=user&amp;sp=new">Pridať zákazníka</a></li>
            	</ul>
            </li>
          	<li class="t"><a class="set" href="./index.php?m=shop&amp;c=setting&amp;sp=general">Nastavenia</a>
                 <ul>
                    <li><a href="./index.php?m=shop&amp;c=setting&amp;sp=general">Základné nastavenia</a></li>
                  	<li><a href="./index.php?m=shop&amp;c=setting&amp;sp=fa">Fakturačné nastavenia</a></li>
                    <li><a href="./index.php?m=shop&amp;c=setting&amp;sp=text">Nastavenie textov</a></li>
                 </ul>
              </li>
        </ul>
    </nav>

<?php 
	$page = "order";
	if(isset($_GET['c']) && in_array( $_GET['c'] , array("order", "product", "category", "customer", "user", "setting"))){
		$page = $_GET['c'];
	}
	include "./modules/shop/inc/".$page.".php";
?>
