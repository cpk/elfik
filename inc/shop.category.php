<script type="text/javascript">
    $(function(){ 
        $('#items-parent').height( $('#items').height()); 
        $("#categ-nav a").hover(
        function () {
          $(this).find('.hover').stop().fadeOut(100);
        }, 
        function () {
          $(this).find('.hover').fadeIn();
        }
      );

    });
</script>
<div id="categ-nav">
    <ul class="level2">
        <?php echo printShopCateg(1, 76, 76); ?>
    </ul><div class="clear"></div>
    
     <ul class="level3">
        <?php 
            if($meta['sub_id'] == 1 ){
                echo printShopCateg($meta['id_shop_category'], 53, 53);
            }else if($meta['sub_id'] > 1){
                echo printShopCateg($meta['sub_id'], 53, 53);
            }
        
        ?>
     </ul><div class="clear"></div>
    
    <h1><?php echo (isset($meta['parentCategName']) ?  $meta['parentCategName'].' - ' : '').
                    ($meta['sub_id'] > 1 ? getCateg("name", $meta['sub_id'] ).' - ' : '').
                    $meta['title_sk']; ?></h1>
</div>


<div id="category">
    <div class="border top"></div>
    <div id="items-parent"><div id="items"><?php echo printPageProducts(); ?></div></div>
    <div class="border bottom"></div>
</div>