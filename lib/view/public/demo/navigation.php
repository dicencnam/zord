<div id="navcontent">
	<ul>
		<li><a id="link_search" href="<?php echo BASEURL ?>page/search"><?php echo $v->search; ?></a></li>
		<li><a href="<?php echo BASEURL ?>page/xxx">xxx</a></li>
		<li class="navcit linkcit"><a href="<?php echo BASEURL; ?>page/marker"><?php echo  $v->citations; ?></a></li>
		<li>
		<?php
		if(!$_SESSION["IPCONNEXION"]){
			echo '<form name="userconnex" method="post"  action="'.BASEURL.'">';
			if(isset($_SESSION['connect_user_'.$_SESSION['switcher']['name']]) &&
				$_SESSION['connect_user_'.$_SESSION['switcher']['name']]===true) {
				echo '<input type="hidden" value="Start" name="module"/><input type="hidden" value="disconnect" name="action"/>';
				echo '<input class="connexion" id="to_disconnect" type="submit" name="submit" value="'.$v->disconnect.'"/>';
			} else {
				echo '<input type="hidden" value="Start" name="module"/><input type="hidden" value="connexion" name="action"/>';
				echo '<input class="connexion" id="to_connexion" type="submit" name="submit" value="'.$v->connexion.'"/>';
			}
			echo '</form>';
		}
		?>
		</li>
	</ul>
<div id="ariadne"></div>
<?php
	if(!isset($_SESSION["user"])){
		echo '<div class="nonconnect_message">'.$v->nonconnect.'</div>';
	}
?>
