<?php 
if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
if(!isset($_GET['sid'])){ $sid = 0; } else { $sid = intval($_GET['sid']); } 


    function getCalendarData(){
        global $conn;
        $json = array();

        $d = $conn->select("SELECT `id_shop_order`, `givenname`, `surname`, `create` FROM `shop_order`");

        for($i = 0; $i < count($d); $i++ ){
            $json[] = array( 
                'id' => $d[$i]['id_shop_order'] , 
                'title' => $d[$i]['id_shop_order'].", ".$d[$i]['givenname']." ".$d[$i]['surname'] , 
                'url' =>  "/admin/index.php?m=shop&c=order&sp=edit&oid=".$d[$i]['id_shop_order'], 
                'start' => strftime("%Y-%m-%d", $d[$i]['create'])
            );

        }
        return json_encode( $json );
    }

?>
<script>
	$(function() {
				$('.date').datepicker({
				dayNamesMin: ['Ne', 'Po', 'Út', 'St', 'Št', 'Pi', 'So'], 
				monthNames: ['Január','Február','Marec','Apríl','Máj','Jún','Júl','August','September','Október','November','December'], 
				maxDate: 0,
				autoSize: false,
				dateFormat: 'dd.mm.yy',
				firstDay: 1
				});
	});
</script>
<div class="breadcrumb">Nachádzate sa:
	<a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?m=shop">Internetový obchod</a> &raquo;
    <a href="./index.php?m=shop&amp;c=order">Správa objednávok</a> &raquo;
    <a href="./index.php?m=shop&amp;c=order&amp;sp=view2">Kalendár objednávok</a>
</div>

<style>
#calendar {background:#fff;}
.cust{ background:#fff;margin: 20px 0px 0px 0px;}
</style>

<script>
$(document).ready(function() {
    var data = <?php echo  getCalendarData(); ?>;
    console.log(data);
    $('#calendar').fullCalendar({
        monthNames : ['Január', 'Február', 'Marec', 'Apríl', 'Máj', 'Jún', 'Júl', 'August', 'September', 'Október', 'November', 'December'],
        monthNamesShort : ['Jan', 'Feb', 'Mar', 'Apr', 'Máj', 'Jún', 'Júl', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'],
        dayNamesShort : ['Ned', 'Pon', 'Úto', 'Str', 'Štv', 'Pia', 'Sob'],
        firstDay : 1,
        buttonText :    
            {
                today:    'Dnešok',
                month:    'mesiac',
                week:     'týždeň',
                day:      'deň'
            },
        events: data,
        color: 'yellow',   // an option!
        textColor: 'black' // an option!
    });
});
</script>
<div class="cbox cust">
<strong class="h img orders">Kalenár objednávok</strong>

<div id='calendar'></div>
<div class="clear"></div>
</div>
  
  




