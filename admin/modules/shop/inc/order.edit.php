<?php 
if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
$oid =  intval($_GET['oid']);  
?>
<div class="breadcrumb">Nachádzate sa:
    <a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?m=shop">Internetový obchod</a> &raquo;
    <a href="./index.php?m=shop&amp;c=order&amp;sp=view">Správa objednávok</a> &raquo;
    <a href="./index.php?m=shop&amp;c=order&amp;sp=edit&amp;oid=<?php echo $oid; ?>">Editácia objednávky ID: <?php echo $oid; ?></a>
</div>

<strong class="h1">Objednávka: <?php echo $oid; ?></strong>
<?php

$data = getFullOrderByID( $oid );
if(isset($data['id_user']) && $data['id_user'] != ""){
	$data = array_merge($data, getOrderUserById($data['id_user']) );
}

if($data == null){
	echo '<p class="error">Objednávka s ID: '.$oid. ' sa v databáze nenachádza.</p>';
}else{
	$data = cleanArticle($data);
}
?>
<script>
	$(function() {		
		$('#shop_product-title_sk').change(function(){
			var o = $(this), l = $('#add .load'), v = $('#variant');
			if(o.val().length === 0) {
                            v.html('<option value="0">Výber varianty tovaru</option>');
                            v.prop("disabled", true);
                            return false;
			}
			l.removeClass("hidden");
			$.getJSON(getUrl, {act : 15, val : o.val()}, function(json) { 
                            if(json.err === 0){
                                    v.html(json.html);
                                    v.prop("disabled", false);
                            }
                            l.addClass("hidden");
			});
			return false;
		});
		
		$('#ownprice').click(function(){
                    if(this.checked){
                        $('#hide').removeClass('hidden');
                    }else{
                        $('#hide').addClass('hidden');
                    }
		});
		
		$('#add input[type=text]').click(function(){
			var o = $(this);if(o.val() === 'ID / Názov tovaru' || 
                            o.val() === 'ks') o.val('');return false;
		});
		
		$(".tc").delegate(".tc a.edit", "click", function(){
                    $('body').data('iid', $(this).attr("href").replace('#id',''));
                    $('body').data('count', $(this).parent().parent("tr").find('.ks').text());
                    $( "#dialog-form" ).dialog( "open" );
		});
		$("#editInfo").click(function(){ 
			$('body').data('oid', $(this).attr("href").replace('#id','')); 
			$( "#user-form" ).dialog( "open" );
		});
                $(".imail a").click(function(){ 
			$('body').data('oid', $(this).attr("href").replace('#id',''));
                        $('body').data('statusId', $('select[name=id_shop_order_status] option:selected').val());
			$( "#imail-form" ).dialog( "open" );
		});
		// user-form --------------------------------------------------------------------------	
                $( "#imail-form" ).dialog({
                    autoOpen: false,
                    height: 500,
                    width: 550,
                    modal: true,
                    buttons: {"Zavireť" : function() {$( this ).dialog( "close" ); }},
                    open: function(){
                        $.getJSON(getUrl,{ act : 23, statusId : $('body').data('statusId'), id : $('body').data('oid')}, function(json) { 
                            if(json.err === 0){$("#imail-form").html(json.html);
                            }else{  showStatus(json); }
                        }); 
                    }
                });
		$( "#user-form" ).dialog({
                    autoOpen: false,
                    height: 650,
                    width: 550,
                    modal: true,
                    buttons: {
                        "Uložiť zmeny": function() {
                            var form = $('#user-form form'),
                                data = renameArr(form.serializeArray());
                            if(! validate( form )){ return false; }
                            data['id'] = $('body').data('oid');
                            $.getJSON(getUrl, data, function(json) { 
                                if(json.err === 0){
                                    $('#data').html(json.html);
                                    $('#user-form').dialog("close");
                                }
                                showStatus(json);
                            }); 
                        },
                        "Zavireť" : function() {
                            $( this ).dialog( "close" );
                        }
                    },
                    open: function(){
                        $.getJSON(getUrl,{ act : 19, id : $('body').data('oid')}, function(json) { 
                            if(json.err === 0){
                                $("#user-form form").html(json.html);
                            }else{
                                showStatus(json);
                            }
                        }); 
                    }
		});
		// ------------------------------------------------------------------------------
		$( "#dialog-form" ).dialog({
			autoOpen: false,
			height: 280,
			width: 550,
			modal: true,
			buttons: {
                            "Uložiť zmeny": function() {
                                var form = $('#dialog-form form'),
                                        data = renameArr(form.serializeArray());
                                if(! validate( form )) return false;
                                data['ids'] = $('body').data('iid').split('-');
                                $.getJSON(getUrl, data, function(json) { 
                                    if(json.err === 0){
                                        $('tbody.shop_item').html(json.html);
                                        $('#pagi').html(json.pagi);
                                        createClasses();
                                        $('#dialog-form').dialog("close");
                                    }
                                    showStatus(json);
                                });
                            },
                            "Zavireť" : function() {
                                    $( this ).dialog( "close" );
                            }
			},
			open: function(){
                            $("#ownprice").prop("checked", false);
                            $("#price").val('');
                            if(!$('#hide').hasClass('hidden')){
                                    $('#hide').addClass('hidden');
                            }
                            var data = {
                                    act : 13,
                                    ids : $('body').data('iid').split('-')
                            };
                            $('#count').val($('body').data('count'));
                            $.getJSON(getUrl, data, function(json) { 
                                    if(json.err === 0){
                                            $('#id_shop_variant').html(json.html);
                                    }else{
                                            showStatus(json);
                                    }
                            }); 
			}
		});
	});
	</script>
<div class="center">
	<div class="obox onav">
    	<span>Akcie: </span>
        <a href="./modules/shop/inc/print.php?type=1&amp;oid=<?php echo $oid; ?>&amp;act=print"  class="print" target="_blank" title="Veriza pre tlač">Vytlačiť</a>
        <a href="./modules/shop/inc/print.php?type=1&amp;oid=<?php echo $oid; ?>&amp;act=pdf"  class="pdf" target="_blank" title="Siťiahnuť PDF">Export do PDF</a>
         <a href="./modules/shop/inc/print.php?type=2&amp;oid=<?php echo $oid; ?>&amp;act=csv"  class="csv" target="_blank" title="Siťiahnuť CSV">Export do CSV</a>
        <div class="clear"></div>
    </div>
	<strong class="h">Informácie</strong>
    <div class="cbox fa">
  			<div id="data">
			<?php echo printOrderInfo($data);?>
			</div>	
            <a class="btn2" href="#id<?php echo $oid; ?>" id="editInfo" title="Upraviť inrofmácie o objednávateľovi?">Upraviť</a>
            <div class="clear"></div>	
    </div>
    
   	<strong class="h">Upraviť objednávku</strong>
    <div class="cbox">
    	<form class="ajaxSubmit">
  		<div class="i odd">
        	<label>Zmena stavu objednávky: </label>
           	<select class="mw300" name="id_shop_order_status">
				<?php echo getOptions( $conn, "shop_order_status", "order_status_name",  $data['id_shop_order_status']); 	?>
           </select>
        </div>
        <div class="i ">
        	<label>Zmena spôsobu platby: </label>
           	<select class="mw300" name="id_shop_payment">
				<?php echo getOptions( $conn, "shop_payment", "payment_name",  $data['id_shop_payment']); 	?>
           </select>
        </div>
       	 <div class="i odd">
        	<label>Zmena spôsobu odberu: </label>
           	<select class="mw300" name="id_shop_delivery">
				<?php echo getDeliveryOpts(  $data['id_shop_delivery']); 	?>
           </select>
        </div>
         <div class="i">
      		<input type="submit" class="ibtn2 fr" value="Uložiť" />
            <input type="hidden" name="table" value="shop_order" />
            <input type="hidden" name="act" value="3" />
            <input type="hidden" name="id" value="<?php echo $oid; ?>" />
			<span class="imail">
                <input type="checkbox" name="sendMail" />odoslať informačný e-mail o zmene stavu obejdnávky zákazníkovi?
                (<a href="#id<?php echo $oid; ?>">zobraziť obsah emailu</a>)
            </span>
			
        </div>
        </form>
		<div class="clear"></div>
    </div>
    
    
    <strong class="h">Pripomienky k objednávke</strong>    	
    <div class="cbox">
        <p class="padding"><?php echo $data['note']; ?></p>
        <div class="clear"></div>
    </div>
    
    
    <strong class="h">Položky objednávky</strong>
    <div class="cbox">
    	<table class="tc items">
          <thead>
              <tr>
                <th>P.č.</th>
                <th>Kód</th>
                <th>Výrobca / Názov tovaru</th>
                <th>Varianta</th>
                <th>Ks</th>
                <th>Cena/ks</th>
                <th>Cena/ks s DPH</th>
                <th>Cena spolu s DPH</th>
                <th>Upraviť</th>
                <th>Zmazať</th>
              </tr>
          </thead>
          <tbody class="shop_item">
            <?php echo printOrderItems($conn, $oid, $data["shop_currency_name"], $data["dph"], $data['id_shop_delivery'], $data['price_delivery'] ); ?>
         </tbody>
    </table>
    <div id="pagi" class="sum">
    		<?php echo printSUMofOrder($data["dph"], $data["sale"], $data['id_shop_delivery'], $data['price_delivery']); ?>
    </div>
    
    <div id="add" class="obox">
    	<form class="ajaxSubmit">
        	<span>Pridať položku do objednávky:</span>
        	<input type="text" name="q" id="shop_product-title_sk"  value="ID / Názov tovaru" class="w250 required"/>
            <div class="load hidden"></div>
            <select id="variant" name="id_shop_variant" class="w250" disabled="disabled">
            	<option value="0">Výber varianty tovaru</option>
            </select>
            <input type="text" class="w45 c required numeric" name="count"  value="ks"/>
            <input type="submit" value="+ Pridať" class="ibtn cust" />
            <input type="hidden" name="act" value="16" />
            <input type="hidden" name="id_shop_order" value="<?php echo $oid; ?>" />
        </form>
    </div>
    
    
    <div id="sale" class="obox">
    	<form class="ajaxSubmit">
        	<div class="tt" title="ordersale.txt"></div><p>Aplikovať zľavu na objednávku (%):</p>
        	<input type="text" name="sale" value="<?php echo $data["sale"]; ?>"  class="c w45 required numeric"/>
            <input type="submit" value="Uložiť" class="ibtn cust" />
            <input type="hidden" name="act" value="3" />
            <input type="hidden" name="table" value="shop_order" />
            <input type="hidden" name="id" value="<?php echo $oid; ?>" />
        </form>
    </div>
    <div class="clear"></div>
    </div>
    
</div>
<div class="clear"></div>


<div id="dialog-form" title="Úprava položky objednávky">
	<form>
	<fieldset  class="cbox">
		<div class="i">
            <label>Varianta:</label>
            <select name="id_shop_variant" id="id_shop_variant" class="select ui-widget-content ui-corner-all w300" ></select>
        </div>
        <div class="i odd">
            <label>Počet ks:</label>
            <input type="text" name="count" id="count" value="" class="text ui-widget-content ui-corner-all required numeric" />
         </div>
        <div class="i">
            <label>Nastaviť vlastnú cenu:</label>
            <input type="checkbox" name="ownprice" id="ownprice"  class="checkbox ui-widget-content ui-corner-all" />
         </div>
         <div id="hide" class="i hidden odd">
            <label>Cena:</label>
            <input type="text" name="price" id="price"  class="text ui-widget-content ui-corner-all" />
            <input type="hidden" name="act" value="14" />
            <input type="hidden" name="id" value="<?php echo $oid; ?>" />
         </div>
	</fieldset>
	</form>
</div>
<div id="user-form" title="Úprava kontaktných údajov"><form><fieldset><p class="ld"><img src="./img/ajax-loader.gif" alt="" /></p></fieldset></form></div>
<div id="imail-form" title="Obsah e-mailu, ktorý sa odošle zákazníkovi"><p class="ld"><img src="./img/ajax-loader.gif" alt="" /></p></div>

