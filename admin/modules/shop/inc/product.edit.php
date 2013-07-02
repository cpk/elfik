<?php 
if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
$pid =  intval($_GET['pid']);  
?>
<div class="breadcrumb">Nachádzate sa:
    <a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?m=shop">Internetový obchod</a> &raquo;
    <a href="./index.php?m=shop&amp;c=product&amp;sp=view&amp;f=1">Správa tovaru</a> &raquo;
    <a href="./index.php?m=shop&amp;c=product&amp;sp=edit&amp;pid=<?php echo $pid; ?>">Editácia tovaru</a>
</div>
<script>
	$(function() {
		
	});
</script>
<strong class="h1">Editácia tovaru</strong>
<?php

$data = $conn->select("SELECT * FROM shop_product WHERE `id_shop_product`=? LIMIT 1", array($pid));
if($data == null){
	echo '<p class="error">Produkt s ID: '.$pid. ' sa v databáze nenachádza.</p>';
}else{
	$data[0] = cleanArticle($data[0]);
}
?>
<div class="center">
	 <?php echo printNextPrev($pid, $data[0]['id_shop_category']); ?>
	 <strong class="h img aedit">Editácia tovaru ID: <strong><?php echo $pid; ?></strong></strong>
    <div class="cbox">
    	<span class="tinfo"> 
                	<?php $user = getUserById($conn, (int)$data[0]["author"], "login");   ?>
                    <strong>Vytvorená: </strong> <?php echo strftime("%d.%m.%Y/%H:%M", $data[0]['create']).'/'.$user["login"]; ?> &nbsp;&nbsp;&nbsp;&nbsp;
                    <?php 
						if(isset($data[0]["editor"]) && $data[0]["editor"] != ""){
							$user = getUserById($conn, (int)$data[0]["editor"], "login");
							echo '<strong>Upravená: </strong>'.strftime("%d.%m.%Y/%H:%M", $data[0]['edit']).'/'.$user["login"].'&nbsp;&nbsp;&nbsp;&nbsp;';
						}
					   ?>
                    <strong>Pč. zobrazení:</strong> <?php echo $data[0]["hits"]; ?>x &nbsp;&nbsp;&nbsp;&nbsp;
                    <strong>Predané:</strong> <?php echo $data[0]["sold_count"]; ?>x &nbsp;&nbsp;&nbsp;&nbsp;
                    <strong>Externý sklad:</strong> <?php echo $data[0]["store_ex"]; ?> ks &nbsp;&nbsp;&nbsp;&nbsp;
         </span>
         <form name="shop_product" id="psave">
         	 
             <div class="i">
                	<label><em>*</em>Názov tovaru:</label><input  maxlength="100" type="text" class="w520 required unique" name="title_sk" value="<?php echo $data[0]['title_sk']; ?>" />
             </div>
             
              <div class="i odd">
                	<label>Výrobca (značka): </label><select class="w200" name="id_shop_manufacturer">
							<?php echo getOptions( $conn, "shop_manufacturer", "shop_manufacturer_name",  $data[0]['id_shop_manufacturer']); ?>
                    </select>
              </div>
              
              <div class="i">
                	<label>Kategória tovaru: </label><select class="mw300 tree" name="id_shop_category">
                    	<optgroup class="top">
						<?php 
						if($data[0]['id_shop_category'] == 0){ 
								echo '<option value="0">Nekategorizovaný produkt</option>'; 
						}else{
								$categName = getCategoryById($conn, $data[0]['id_shop_category']);
								echo '<option value="'.$data[0]['id_shop_category'].'">'.$categName[0]['category_name'].'</option><option value="0">Nekategorizovaný produkt</option>'; 
						} ?>
                        </optgroup>
                        <optgroup><?php echo  getCategoryOpts($conn, 0); ?></optgroup>
                    </select>
              </div>
             
             <div class="i odd">
                	<div class="lbl"><div class="tt" title="shopproductstatus.txt"></div>Status tovaru:</div><select class="mw300" name="id_shop_product_status"><?php echo getOptions( $conn, "shop_product_status", "shop_product_status_name",  $data[0]['id_shop_product_status']); ?></select>
             </div>
             
			 <div class="i">
                	<label>Dostupnosť tovaru:</label><select class="mw300" name="id_shop_product_avaibility"><?php echo getOptions( $conn, "shop_product_avaibility", "shop_product_avaibility_name",  $data[0]['id_shop_product_avaibility']); ?></select>
             </div>
             
			<div class="i odd">
                	<label>Publikovať:</label><input type="checkbox" class="checkbox" name="active" <?php echo ($data[0]['active'] == 1 ? 'checked="checked"' : "" ); ?> />
             		<span style="padding:5px 0px 10px 40px">Zobraziť na úvodnej stránke:</span><input type="checkbox" class="checkbox" name="home" 
					<?php echo ($data[0]['home'] == 1 ? 'checked="checked"' : "" ); ?> />
                        <span style="padding:5px 0px 10px 40px">Nastaviť ako TOP tovar:</span><input type="checkbox" class="checkbox" name="top" 
					<?php echo ($data[0]['top'] == 1 ? 'checked="checked"' : "" ); ?> />
             </div>
           
            <div class="i">
                	<div class="lbl"><div class="tt" title="shopprice.txt"></div>Cena tovaru:</div>
                        <input  maxlength="11" type="text" class="w220 c pricePom" name="price" value="<?php echo $data[0]['price']; ?>" />
                        <span class="price"><?php echo $config["s_currency"]; ?></span>
                        <span> &nbsp; &nbsp; &nbsp;Cena s DPH</span>
                        <input type="text" name="skip" class="w220 c" />
                    
             </div>
             
              <div class="i odd">
                	<div class="lbl"><div class="tt" title="shoppricesale.txt"></div>Akciová cena tovaru:</div>
                        <input  maxlength="11" type="text" class="w220 c pricePom" name="price_sale" value="<?php echo $data[0]['price_sale']; ?>" />
                        <span class="price"><?php echo $config["s_currency"]; ?></span>
                        <span> &nbsp; &nbsp; &nbsp;Cena s DPH</span>
                        <input type="text" name="skip" class="w220 c" />
             </div>
             
              <div class="i">
                	<label>Štandardná cena tovaru:</label><input  maxlength="11" type="text" class="w220 c" name="price_standard" value="<?php echo $data[0]['price_standard']; ?>" /> <span class="price"><?php echo $config["s_currency"]; ?></span>
             </div>
             
              <div class="i odd">
                	<label>Záruka :</label><input  maxlength="3" type="text" class="w45 c" name="guarantee" value="<?php echo $data[0]['guarantee']; ?>" /><span>mes</span>
              </div>
              
              <div class="i">
                	<div class="lbl"><div class="tt" title="shopstore.txt"></div>Hmotnosť :</div><input  maxlength="5" type="text" class="w50 c" name="store_in" value="<?php echo $data[0]['store_in']; ?>" /><span>g</span>
              </div>
              
              <div class="i odd">
                	<label>Kód tovaru:</label><input  maxlength="13" type="text" class="w200" name="ean" value="<?php echo $data[0]['ean']; ?>" />
              </div>
             
              <div class="i">
                	<label>Skladom:</label><input  maxlength="13" type="text" class="w100" name="store_in" value="<?php echo $data[0]['store_in']; ?>" /><span>ks</span>
              </div>
             
             
              <div class="i odd">
                	<label>Podnadpis tovaru:</label><input  maxlength="150" type="text" class="w750" name="subtitle_sk" value="<?php echo $data[0]['subtitle_sk']; ?>" />
              </div>
              
              <div class="i ">
                	<label>Úvodný text:</label><textarea class="w750 h100" cols="10" rows="10" name="header_sk"><?php echo $data[0]['header_sk']; ?></textarea>
              </div>
              
              <div class="i odd">
              		<label>Detail tovaru:</label>
                    <textarea name="content_sk" id="editor1"  class="w750" rows="10" cols="83"></textarea>
                    <div id="content"></div>
                     <div id="data" class="hidden"><?php echo $data[0]["content_sk"]; ?></div>
                        <div id="ContentEditor"></div>
                          <script type="text/javascript">
                          	var editor = CKEDITOR.replace( 'editor1', 
							{
								language : 'sk',
								width: 770,
								toolbar : [		
                  [ 'Source' ],						
									['Bold', 'Italic', '-', 'NumberedList', 'BulletedList'],
									['Undo','Redo'],
									['Link','Unlink','Anchor'],
									['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
									['-','Format','Table'],
									]	
							}
							);	
							editor.setData(document.getElementById("data").innerHTML);
							CKFinder.setupCKEditor( editor, './ckfinder/' ) ;
                          </script>
              	<div class="clear"></div>
              </div>
              
               <div class="i ">
                	<input type="hidden" value="<?php echo $pid; ?>" name="id" />
                	<input type="submit"  class="ibtn2" name="button" value="Uložiť" />
                    <input type="hidden" value="shop_product" name="table" />
                    <div class="clear"></div>
                </div>
          </form>
    </div>
    
    
     
    
    
    <!--  VARIANTS   -->
    <strong class="h">Variatny tovaru</strong>
    <div class="cbox">
    		<form class="inlineEditing">
            <table class="tc variant inline" border="1" cellspacing="0" cellpadding="0">
              <thead>
                  <tr>
                    <th class="il text-shop_variant_name required">Názov</th>
                    <th class="il text-price">Cena bez DPH</th>
                    <th class="il text-weight">Hmotnosť</th>
                    <th>Publikovať</th>
                    <th>Upraviť</th>
                    <th>Zmazať</th>
                  </tr>
              </thead>
              <tbody class="shop_variant">
              	<?php echo printVariants($conn, $pid); ?>
              </tbody>
            </table>
            	<input type="hidden" name="act" value="3" />
                <input type="hidden" value="<?php echo $pid; ?>" name="id_shop_product" />
                <input type="hidden" value="shop_variant" name="table" />
			</form>
            
            <div class="addBox">
            	<em>Pridanie novej varianty</em>
            	<form class="ajaxSubmit">
                	<span><strong>*</strong>Názov: </span><input type="text" name="shop_variant_name" maxlength="100"  class="w250 required"  />
                    <span>Cena bez DPH: </span><input type="text" name="price"  class="w55 c" maxlength="9" />
                    <span>Cena s DPH: </span><input type="text" name="skip"  class="w55 c" maxlength="9" />
                    <span>Hmotnosť: </span> <input type="text" name="weight"  class="w55 c" maxlength="10" />
                    
                    <input type="hidden" value="<?php echo $pid; ?>" name="id_shop_product" />
                    <input type="hidden" value="shop_variant" name="table" />
                    <input type="hidden" value="2" name="act" />
                    <input type="submit" class="ibtn cust" value="Pridať" />
                </form>
            </div>
    
    	<div class="clear"></div>
    </div>
    
    
    <!--  Atributs  -->
	<!-- 
    <strong class="h">Atribúty tovaru</strong>
    <div class="cbox">
    		<form class="inlineEditing">
            <table class="tc variant inline" border="1" cellspacing="0" cellpadding="0">
              <thead>
                  <tr>
                    <th class="il text-key required">Kľúč</th>
                    <th class="il text-val">Hodnota</th>
                    <th>Upraviť</th>
                    <th>Zmazať</th>
                  </tr>
              </thead>
              <tbody class="shop_attr">
              	<?php // echo printAttrs($conn, $pid); ?>
              </tbody>
            </table>
            	<input type="hidden" name="act" value="3" />
                <input type="hidden" value="<?php echo $pid; ?>" name="id_shop_product" />
                <input type="hidden" value="shop_attr" name="table" />
			</form>
            
            <div class="addBox">
            	<em>Pridanie nového atribútu</em>
            	<form class="ajaxSubmit">
                	<span><strong>*</strong>Kľúč: </span><input type="text" name="key" maxlength="40"  class="w200 required"  />
                    <span>Hodnota: </span><input type="text" name="val"  maxlength="100"  class="w400" />
                    
                    <input type="hidden" value="<?php echo $pid; ?>" name="id_shop_product" />
                    <input type="hidden" value="shop_attr" name="table" />
                    <input type="hidden" value="4" name="act" />
                    <input type="submit" class="ibtn cust" value="Pridať" />
                </form>
            </div>
    
    	<div class="clear"></div>
    </div>
    -->
    
    <!--  Colors   -->
    <!-- <strong class="h">Farba tovaru</strong>
    <div class="cbox">
    		<form class="ajaxSubmit">
               <?php // echo printColors($pid); ?>
                <input type="hidden" value="<?php echo $pid; ?>" name="id_shop_product" />
                <input type="hidden" value="22" name="act" />
                <input type="submit" class="ibtn cust" value="Uložiť" />
            </form>
    	<div class="clear"></div>
    </div>
    -->
	
    <!--  AVATARY -->
    
    <strong class="h img av">Avatáry k tovaru</strong>
    <div class="cbox">
    	       <form name="avatars" id="avatars" action="./inc/ajax.post.php" method="post" enctype="multipart/form-data">
                   <div class="wrp">
                        
                        <div class="ibox cust">
                            <strong>Avatar 1</strong>
                            	
                            <div id="avatar1">
                                    <?php 
                                       if($data[0]['avatar1'] != "") { 
                                       		 echo '<a href="../../data/avatars/'.$data[0]['avatar1'].'" title="Zobraziť obrázok" class="show hidden"></a>'.
                               					  '<a href="#id'.$pid.'" title="shop_product#avatar1#'.$data[0]['avatar1'].'" class="del hidden"></a>'. 
											 	  '<img src="./inc/img.php?url=../../data/avatars/'.$data[0]['avatar1'].'&amp;w=100&amp;h=100&amp;type=crop"  class="img" alt="" />';
                                        }else{
											echo '<img src="./img/noavatar.png" alt="Nie je nahratý obrázok." />';
										}
                                    ?> 
                                  
                            </div>
                                <input name="avatar1" class="f" type="file" maxlength="45" />
                          </div>
                        
                         <div class="ibox cust"> 
                            <strong>Avatar 2</strong>
                             <div id="avatar2">
                                    <?php 
                                        if($data[0]['avatar2'] != "") { 
                                        	echo '<a href="../../data/avatars/'.$data[0]['avatar2'].'" title="Zobraziť obrázok" class="show hidden"></a>'.
                               					  '<a href="#id'.$pid.'" title="shop_product#avatar2#'.$data[0]['avatar2'].'" class="del hidden"></a>'. 
											 	  '<img src="./inc/img.php?url=../../data/avatars/'.$data[0]['avatar2'].'&amp;w=100&amp;h=100&amp;type=crop" class="img" alt="" />';
                                        }else{
											echo '<img src="./img/noavatar.png" alt="Nie je nahratý obrázok." />';
										}
                                        
                                    ?> 
                              </div>
                              <input name="avatar2" class="f" type="file" maxlength="45" />
                        </div>
                        
                         <div class="ibox cust"> 
                            <strong>Avatar 3</strong>
                             <div id="avatar3">
                                    <?php 
                                        if($data[0]['avatar3'] != "") { 
                                        	echo '<a href="../../data/avatars/'.$data[0]['avatar3'].'" title="Zobraziť obrázok" class="show hidden"></a>'.
                               					  '<a href="#id'.$pid.'" title="shop_product#avatar3#'.$data[0]['avatar3'].'" class="del hidden"></a>'. 
											 	  '<img src="./inc/img.php?url=../../data/avatars/'.$data[0]['avatar3'].'&amp;w=100&amp;h=100&amp;type=crop" class="img" alt="" />';
                                        }else{
											echo '<img src="./img/noavatar.png" alt="Nie je nahratý obrázok." />';
										}
                                        
                                    ?> 
                              </div>
                              <input name="avatar3" class="f" type="file" maxlength="45" />
                        </div>
                        <div class="clear"></div>
                    </div>
                    
            	
                
                <input type="hidden" value="<?php echo $pid; ?>" name="id" />
                <input type="hidden" value="shop_product" name="table" />
                <input type="hidden" value="10" name="act" />
                
                <input type="submit" class="ibtn2" value="Nahrať" /><img src="./img/ajax-loader.gif"  class="loader" alt="Nahrávam..." />
             </form>
         <div class="clear"></div>
    </div>
    
    <!--  GALLERY  -----------------------  -->

     <strong class="h img ga">Galéria</strong>
     <div class="cbox">
                <form  id="uploader" name="gallery" action="./inc/ajax.post.php"  method="post" enctype="multipart/form-data">
                    <input name="img1" class="f" type="file" maxlength="45" />
                    <input name="img2" class="f" type="file" maxlength="45" />
                    <input name="img3" class="f" type="file" maxlength="45" />
                    <input name="img4" class="f" type="file" maxlength="45" />
                    <input name="img5" class="f" type="file" maxlength="45" />
                    <input type="hidden" value="<?php echo $pid; ?>" name="id" />
                    <input type="hidden" value="shop" name="dirName" />
                    <input type="hidden" value="13" name="act" />
                    <input type="submit" class="ibtn2" value="Nahrať" /><img src="./img/ajax-loader.gif"  class="loader" alt="Nahrávam..." />
                </form>
                
                <div id="gallery"  class="clear">
                	<?php  echo gallery($config, $pid, "shop");  ?>             
                </div>
                <div class="clear"></div>
           </div>

    
    
    
</div>
<div class="clear"></div>



