<?php 
if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
if(!isset($_GET['sid'])){ $sid = 0; } else { $sid = intval($_GET['sid']); } 
$texts = getMailText( );
?>

<div class="breadcrumb">Nachádzate sa:
	<a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?m=shop">Internetový obchod</a> &raquo;
    <a href="./index.php?m=shop&amp;c=setting">Nastavenia</a> &raquo;
    <a href="./index.php?m=shop&amp;c=setting&amp;sp=text">Nastavenie textov v emailoch</a>
</div>

<strong class="h1">Nastavenie textov v emailoch</strong>

<div class="left">
    <?php include dirname(__FILE__)."/setting.nav.php";  ?>
</div>
<script>
		$(function() {
			$('.ckform').submit(function (){
				var data = {
					s_1 : CKEDITOR.instances.s_1.getData(),
					s_2 : CKEDITOR.instances.s_2.getData(),
					s_3 : CKEDITOR.instances.s_3.getData(),
					s_4 : CKEDITOR.instances.s_4.getData(),
                                        s_5 : CKEDITOR.instances.s_5.getData(),
					act : 3
				};
				 $.post( './modules/shop/inc/ajax.post.php', data, function(json) {  
					showStatus(json);
				}, "json");
				return false;
			});
		});	
</script>
<div class="right">	
    <div class="cbox">
        <strong class="h img sys">Nastavenie textov v emailoch</strong>
        	<form class="ckform">
            	<p class="info">V tejto sekcii sa nastavujú text e-mailov, ktoré su automaticky odosielané po vytvorení objednávky, zmazaní atď.</p>
            	 		<script type="text/javascript">
                          	$(function() {
                                        $('.ckedit').each(function(){
                                        CKEDITOR.replace( $(this).attr('id'),{
                                        language : 'sk',width: 500,height: 100,
                                        toolbar : [								
                                                ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList'],
                                                ['Undo','Redo'],
                                                ['Link','Unlink','Anchor'],
                                                ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
                                                ['-','Format']]}  
                                        );
                                        });
										CKEDITOR.config.entities = false;
                                        CKEDITOR.instances.s_1.setData($('textarea[name=s_1]').val());
                                        CKEDITOR.instances.s_2.setData($('textarea[name=s_2]').val());
                                        CKEDITOR.instances.s_3.setData($('textarea[name=s_3]').val());
                                        CKEDITOR.instances.s_4.setData($('textarea[name=s_4]').val());
                                        CKEDITOR.instances.s_5.setData($('textarea[name=s_5]').val());
                                });
                          </script>
                
                <div class="i"> 
                	<div class="lbl"><div class="tt" title="shops_1.txt"></div>Text po odoslaní košíka</div>
                    <textarea name="s_1" class="hidden"><?php echo $texts['s_1']; ?></textarea>
                    <div class="ckedit" id="s_1"></div> <div class="clear"></div>
                </div>
                
                 <div class="i odd"> 
                	<div class="lbl"><div class="tt" title="shops_2.txt"></div>Text po prijatí objednávky</div>
                    <textarea name="s_2" class="hidden"><?php echo $texts['s_2']; ?></textarea>
                    <div class="ckedit" id="s_2"></div><div class="clear"></div>
                </div>
                
                <div class="i "> 
                	<div class="lbl"><div class="tt" title="shops_3.txt"></div>Text po odoslani objednávky</div>
                    <textarea name="s_3" class="hidden"><?php echo $texts['s_3']; ?></textarea>
                    <div class="ckedit" id="s_3"></div><div class="clear"></div>
                </div>
                          
                <div class="i "> 
                    <div class="lbl"><div class="tt" title="shops_4.txt"></div>Text po dokončení objednávky</div>
                    <textarea name="s_4" class="hidden"><?php echo $texts['s_4']; ?></textarea>
                    <div class="ckedit" id="s_4"></div><div class="clear"></div>
                </div>
                
                <div class="i odd"> 
                	<div class="lbl"><div class="tt" title="shops_5.txt"></div>Text po zrušení objednávky</div>
                    <textarea name="s_5" class="hidden"><?php echo $texts['s_5']; ?></textarea>
                    <div class="ckedit" id="s_5"></div><div class="clear"></div>
                </div>
                
                <div class="i"> 
                	<input type="hidden" name="act"  value="18" />
                	<input type="submit" class="ibtn2"  value="Uložiť" />
                </div>
            </form>     
        
    	<div class="clear"></div>
    </div>
  
  
  
</div>
<div class="clear"></div>


