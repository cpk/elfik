        <!-- DETAIL PAGE -->
        <?php $product = getProduct("full", $meta['id_shop_product']);?>
        <div id="detail">
            <div class="border top"></div>
            
            
            <!-- DETAIL -->
            <div  id="detail-content">
                <div class="left">
                    
                    <!-- AVATAR -->
                    <div id="avatar-box">
                        <?php 
                            echo printShopAvatar($product[0]['avatar1'], 210, 185, $product[0]['title_sk']).
                            ($product[0]['id_shop_product_status'] == 4 ? '<span class="item-status sr-1"></span>' : '').
                            ($product[0]['top'] == 1 ? '<span class="item-status sl-1"></span>' : '');
                        ?>
                    </div>
                    
                    
                    <!-- PRICE -->
                    <div id="price-box">
                        <p class="price"><?php echo printPrice($product[0]); ?></p>
                        <?php if($product[0]['store_in'] > 0 || $product[0]['id_shop_product_avaibility'] == 3)
                            echo '<a class="addToBasket" href="#'.$meta['id_shop_product'].'" title="Vložiť do košíka">vložiť do košíka</a>';
                         ?>
                    </div>
                    
                    <?php 
                        if(($product[0]['id_shop_product_status'] == 2 || $product[0]['id_shop_product_status'] == 3) && $product[0]['price_sale'] != 0){
                            echo '<p class="sale">
                                    CENA s DPH:<br />
                                    <em class="yellow">'.printPrice($product[0]).'</em><br />
                                    Pôvodná cena:<br />
                                    <em class="cross">'.calculatePriceWithDph($product[0]['price']).' &euro;</em><br />
                                    Zľava:  <em class="yellow">'.percentageSale($product[0]).'</em>
                                </p>';
                        }
                    ?>
                    
                    <!-- availability -->
                    <span class="avail">
                        <p>Dostupnosť</p>
                        <?php echo $product[0]['label'].'<br />'.
                         getStoreStatus($product[0]['store_in'], $product[0]['id_shop_product_avaibility']); ?>
                    </span>
                    
                    
                    <!-- BACK BUTTON -->
                   
                    <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" type="Pokračovať v nákupe" class="back">&laquo; pokračovať v nákupe</a>
                    
                    
                   <?php echo pritShopGallery($meta['id_shop_product'], 95, 72, $product[0]['title_sk']); ?>
                    
                </div>
                <div class="right">
                    <div id="bc">
                        <a href="/shop/">ALKATRAZ</a> 
                        <?php echo shopBreadcrumb($meta['id_shop_product']) ; ?>
                    </div>
                    
                    <h1><?php echo $product[0]['title_sk']?></h1>
                    <?php if(strlen($product[0]['subtitle_sk']) > 1) {
                        echo '<h2>'.$product[0]['subtitle_sk'].'</h2>';
                    }
                        echo $product[0]['content_sk'];
                    ?>
                    
                </div>
                
                <div class="clear"></div>
            </div>
            
            
            <div class="border bottom"></div>
            
        </div>


