
<?php 
       
        if(isset($_GET['cid']) && $_GET['cid'] == "home"){
            include 'shop.home.php';
        }else{
            include 'shop.category.php';
        }	
?>
