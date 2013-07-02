<?php 
if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
if(!isset($_GET['cid'])){ $cid = 0; } else { 
	$cid = intval($_GET['cid']); 
	$name = getCategoryById($conn, $cid);
} 
?>

<div class="breadcrumb">Nachádzate sa:
	<a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?m=shop">Internetový obchod</a> &raquo;
    <a href="./index.php?m=shop&amp;c=category&amp;sp=view">Správa kategórií</a>`

</div>
<strong class="h1">Správa kategórií</strong>

<div class="left">
		<strong class="h">Kategórie</strong>
        <a href="./index.php?m=shop&amp;c=category&amp;sp=view&amp;cid=0" class="maincateg">Hlavné kategórie</a>
        <ul id="tree">
        <?php  echo categTree($conn, 0, $cid, "category"); ?>	
        </ul>
</div>
<script>
	$(function() {$('#tree').treeview({animated: "fast",collapsed: true,unique: false,persist: "cookie"}); tableDNDinit(); });
</script>
<div class="right">	
    <div class="cbox">
        <strong class="h img article">Zoznam <?php echo ( $cid == 0 ? 'hlavných kategórií' : '<strong>podkategórií</strong> kategórie <strong>'.$name[0]['category_name'].'</strong>'); ?></strong>
        <div class="breadcrumb">
        Zobrazené: <?php 
			if($cid == 0){
				echo '<a href="./index.php?m=shop&amp;c=product&amp;sp=view&amp;cid=0" >Hlavné kategórie</a>';
			}else{
				echo categoryBC($conn, $cid ,"&raquo;"); 
			}
		?>
        </div>
            <table class="tc" id="dnd">
              <thead>
                  <tr class="nodrop nodrag">
                    <th>&nbsp;Názov kategórie</th>
                    <th>&nbsp;Popis</th>
                    <th scope="col">&nbsp;Publikovať</th>
                    <th scope="col">&nbsp;Poradie</th>
                    <th scope="col">&nbsp;Zmazať</th>
                  </tr>
              </thead>
              <tbody class="shop_category">
                <?php echo printCategories($conn, $cid) ; ?>
             </tbody>
             </table>

            
            <div class="addBox margin30">
            	<em>Pridanie novej <?php echo ($cid == 0 ? ' kategórie do <strong>hlavnej kategórie</strong>' : 'podkategórie do kategórie <strong>'.$name[0]['category_name'].'</strong>' )?></em>
            	<form class="ajaxSubmit block">
                	<div class="i">
                        <span class="w100"><strong>*</strong>Názov:</span>
                        <input type="text" name="category_name" maxlength="100"  class="w400 required"  />
                        <input type="submit" class="ibtn cust" value="Pridať" />
                    </div>
                     <div class="i">
                  	<span class="w100">Popis:</span><textarea  name="label"  class="w400 h70"></textarea>
                    </div>
                    <input type="hidden" value="<?php echo $cid; ?>" name="id" />
                    <input type="hidden" value="shop_category" name="table" />
                    <input type="hidden" value="8" name="act" />
                    
                    <div class="clear"></div>
                </form>
            </div>
        <div class="clear"></div>
     </div>
        
        
        
	<?php 	
		if($cid != 0){
			$data = $conn->select("SELECT * FROM `shop_category` WHERE `id_shop_category`=? LIMIT 1", array( $cid ) );
			if(count($data) == 0){
				echo "<p class=\"alert\">Položka $pyid nie je v databáze evidovaná</p>";		
			}
	?>
    	<div class="cbox">
        <strong class="h">Editácia kategórie  <strong><?php echo $name[0]['category_name']; ?></strong></strong>
    		 <form  class="ajaxSubmit"> 
                <div class="i odd">
                	<label><em>*</em>Názov kategórie:</label><input  maxlength="45" type="text" class="w300 required" name="category_name" value="<?php echo $data[0]['category_name'];  ?>" />
                </div> 	
                <div class="i ">
                	<label>Popis:</label><textarea name="label" class="w520 h70"><?php echo $data[0]['label'];  ?></textarea>
                </div>
                <div class="i odd">
                    <label>Publikované: </label><select class="w200" name="active">
                    <?php  echo ($data[0]["active"] == 0 ? '<option value="0">Nepublikovať</option><option value="1">Publikovať</option>' : '<option value="1">Publikovať</option><option value="0">Nepublikovať</option>');?>
                    </select>
                </div>
                <div class="i ">
                	<input type="hidden" value="<?php echo $cid; ?>" name="id" />
                    <input type="hidden" value="<?php echo $data[0]['sub_id']; ?>" name="sub_id" />
                    <input type="hidden" value="shop_category" name="table" />
                    <input type="hidden" value="3" name="act" />
                    <input type="submit" class="ibtn2 cust" value="Uložiť" />
                </div>
            </form>
       <div class="clear"></div>
     </div> 
        
            <div class="cbox">
                 <strong class="h ">Nahrať avatár</strong></strong>
    	       <form name="avatars" id="avatars" action="./inc/ajax.post.php" method="post" enctype="multipart/form-data">
                   <div class="wrp">
                        <div class="ibox cust">
                            <strong>Avatar 1</strong>
                            	
                            <div id="avatar1">
                                    <?php 
                                       if($name[0]['avatar1'] != "") { 
                                       		 echo '<a href="../../data/avatars/'.$name[0]['avatar1'].'" title="Zobraziť obrázok" class="show hidden"></a>'.
                               			'<a href="#id'.$cid.'" title="shop_product#avatar1#'.$name[0]['avatar1'].'" class="del hidden"></a>'. 
						'<img src="./inc/img.php?url=../../data/avatars/'.$name[0]['avatar1'].'&amp;w=100&amp;h=100&amp;type=crop"  class="img" alt="" />';
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
                                       if($name[0]['avatar2'] != "") { 
                                       		 echo '<a href="../../data/avatars/'.$name[0]['avatar2'].'" title="Zobraziť obrázok" class="show hidden"></a>'.
                               			'<a href="#id'.$cid.'" title="shop_product#avatar2#'.$name[0]['avatar2'].'" class="del hidden"></a>'. 
						'<img src="./inc/img.php?url=../../data/avatars/'.$name[0]['avatar2'].'&amp;w=100&amp;h=100&amp;type=crop"  class="img" alt="" />';
                                        }else{
                                                echo '<img src="./img/noavatar.png" alt="Nie je nahratý obrázok." />';
                                        }
                                    ?> 
                            </div>
                                <input name="avatar2" class="f" type="file" maxlength="45" />
                          </div>
                        <div class="clear"></div>
                    </div>
                <input type="hidden" value="<?php echo $cid; ?>" name="id" />
                <input type="hidden" value="shop_category" name="table" />
                <input type="hidden" value="10" name="act" />
                <input type="submit" class="ibtn2" value="Nahrať" /><img src="./img/ajax-loader.gif"  class="loader" alt="Nahrávam..." />
             </form>
        <div class="clear"></div>
     </div>
    <?php } ?>
        
</div>
<div class="clear"></div>



