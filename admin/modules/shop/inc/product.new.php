<?php 
if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
if(!isset($_GET['cid'])){ $cid = 0; } else { $cid = intval($_GET['cid']); } 
?>
<div class="breadcrumb">
	Nachádzate sa:
    <a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?m=shop">Internetový obchod</a> &raquo;
    <a href="./index.php?m=shop&amp;c=product">Správa tovaru</a> &raquo;
    <a href="./index.php?m=shop&amp;c=product&amp;sp=new">Pridanie nového tovaru</a>
</div>
<strong class="h1">Pridanie nového tovaru</strong>
<br />
<br />
<br />


<strong class="h">Formulár pre pridanie nového tovaru</strong>

<form class="box cust" id="pnew" name="shop_product" method="get" action="./modules/shop/inc/product.insert.php">
    <label><em>*</em>Názov tovaru: &nbsp;</label></label>
    <input type="text" name="title_sk" class="w400 required unique" />
    <input class="ibtn2" type="submit" value="Uložiť a pokračovať &rsaquo;" />
    <input type="hidden" name="id" value="-1" />
</form>

