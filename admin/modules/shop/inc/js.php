<?php
switch($_GET['c']){
	case "product":
		if($_GET['sp'] == "view"){ ?>
                <script src="./js/jquery.cookie.js"></script>
                <script src="./js/jquery.treeview.js"></script>	
		<?php }elseif($_GET['sp'] == "edit"){ ?>
				<script src="./ckeditor/ckeditor.js"></script>
                <script src="./ckfinder/ckfinder.js"></script>
		<?php }
	break;
	case "category": 
	?>
        <script src="./js/jquery.cookie.js"></script>
        <script src="./js/jquery.treeview.js"></script>	
        <script src="./js/jquery.tablednd_0_5.js"></script>
	<?php		
	break;
	case "order": 
	?>
		<link rel="stylesheet" href="./modules/shop/css/fullcalendar.css" />
        <script src="./modules/shop/js/fullcalendar.min.js"></script>
	<?php		
	break;
	case "setting": 
	?>
          <script src="./ckeditor/ckeditor.js"></script>
	<?php		
	break;
}
?>
<link rel="stylesheet" href="./modules/shop/css/main.css" />
<script src="./js/scripts.js"></script>
<script src="./modules/shop/js/scripts.js"></script>

