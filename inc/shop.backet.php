<div id="detail">
<div class="border top"></div>
<div class="bucket">
<h1 class="shop">Nákupný košík 1/3</h1>
<?php
	if( $cart->getTotalQuantity()  == 0){
		echo '<p class="alert">Váš nákupný košík je prázny.</p>';
	}else{
?>

<!-- BUCKET NAV -->
<div class="bucket-nav">
	<a href="/shop/kosik" class="k1" title="Zobraziť obsah nákupného košíka"><p>Košík</p></a>
    <span class="arrow"></span>
    <a href="/shop/kosik2" class="k2" title="Zobraziť obsah nákupného košíka">Doprava a platba</a>
    <span class="arrow"></span>
    <span class="item k3">Dodacie údaje</span>
    <div class="clear"></div>
</div>



<!-- SHOP ITEMS -->
<table id="k">
	<thead>
    	<tr>
        	<th class="c"></th>	
            <th class="l">Názov tovaru</th>
        	<th>Cena</th>
        	<th class="pcks">Počet ks.</th>
        	<th class="dlte">Odstrániť</th>
        <tr>
    </thead>
	<tbody>
    	<?php  echo  printBacket($meta['s_dph'], $meta['s_currency']);?>
    </tbody>
    
</table>



<div id="sum">
	<?php  echo  sum($cart);?>
</div>

<a href="/shop/kosik2" title="Pokračovať k volbe dopravy a platby" class="kbtn">Pokračovať</a>

<?php } ?>
<div id="imgbox"></div>

    </div>
</div>