<?php if(!$auth->isLogined()){ die("Neautorizovaný prístup."); } 
	if(!isset($_GET['uid'])){ $uid = 0; } else { $uid = intval($_GET['uid']); }
?>

<div class="breadcrumb">Nachádzate sa:
	<a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?m=shop">Internetový obchod</a> &raquo;
    <a href="./index.php?m=shop&amp;c=user&amp;sp=view">Správa zákazníkov</a>
</div>

<strong class="h1">Správa zákazníkov</strong>

<div class="left">
		<?php include dirname(__FILE__)."/user.nav.php" ?>
</div>

<div class="right">
	<form class="shopSearch">
    	<input type="text" name="q" id="user-login" />
        <input type="submit" class="ibtn"  value="Hladať" />
         <input type="hidden" name="url" value="<?php echo $_SERVER['QUERY_STRING']; ?>" />
        <input type="hidden" name="table" value="user" />
    </form>
		<div class="cbox">
            <strong class="h img article">Zoznam registrovaných užívateľov</strong>
             <?php
               // $count = $conn->simpleQuery("SELECT count(*) FROM `user`");
               // $count = $count[0]["count(*)"];
               // $config['offset'] = ($s == 1 ? 0 :  ($s * $config["adminPagi"]) - $config["adminPagi"]);    
            ?>
            <table class="tc" id="dnd" >
              <thead>
                  <tr>
                    <th scope="col">&nbsp;ID</th>
                    <th scope="col">&nbsp;Login</th>
                    <th scope="col">&nbsp;Aktivný</th>
                    <th scope="col">&nbsp;Registrovaný</th>
                    <th scope="col">&nbsp;Meno</th>
                    <th scope="col">&nbsp;Zmazať</th>
                  </tr>
              </thead>
              <tbody class="user">
               <?php echo printCustomers($conn, (isset($_GET['q']) ? $_GET['q'] : NULL), $s) ; ?>
             </tbody>
        </table>
        
             <div id="pagi">
                <?php 
                $nav = new Navigator($config['count'], $s , './index.php?'.preg_replace("/&s=[0-9]/", "", $_SERVER['QUERY_STRING']) , $config["adminPagi"]);
                $nav->setSeparator("&amp;s=");
                echo $nav->smartNavigator();
                ?>
            </div>
        	<div class="clear"></div>
        </div>
        
       
</div>
<div class="clear"></div>


