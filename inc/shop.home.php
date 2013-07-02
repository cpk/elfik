        <!-- SLIDER   -->
        <div id="slider-container">
            <div id="slider">
            <ul><?php echo printSliders(14, 999, 354, "e-shop hry"); ?> </ul>
           </div>
        </div>
        
        
        <!-- HOME PAGE -->
        <div id="home-content">
            <div id="items">
                 <?php echo printPageProducts(true); ?>
            </div>
            
            <div id="new-items">
                <ul>
                   <?php echo  getNews(15);  ?>
                </ul>
            </div>
            <div class="clear"></div>
        </div>