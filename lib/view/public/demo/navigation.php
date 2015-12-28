<div id="navcontent">
	<ul>
		<li><a href="<?php echo BASEURL ?>"><?php echo $v->home; ?></a></li>
		<li><a id="link_search" href="<?php echo BASEURL ?>page/search/?default=true"><?php echo $v->search; ?></a></li>
		<li><a href="<?php echo BASEURL ?>page/xxx">xxx</a></li>
		<li id="pageNotices"><a href="<?php echo BASEURL ?>page/notices"><?php echo $v->notices; ?></a></li>
		<li class="navcit linkcit"><a href="<?php echo BASEURL; ?>page/marker"><?php echo  $v->citations; ?></a></li>
		<?php
		if(!$_SESSION["IPCONNEXION"]){
			echo '<li><form name="userconnex" method="post" id="userconnex" action="'.BASEURL.'">';
			if(isset($_SESSION['connect_user_'.$_SESSION['switcher']['name']]) &&
				$_SESSION['connect_user_'.$_SESSION['switcher']['name']]===true) {
				echo '<input type="hidden" value="Start" name="module"/><input type="hidden" value="disconnect" name="action"/>';
				echo '<input class="connexion" id="to_disconnect" type="submit" name="bntsubmit" value="'.$v->disconnect.'"/>';
			} else {
				echo '<input type="hidden" value="Start" name="module"/><input type="hidden" value="connexion" name="action"/>';
				echo '<input class="connexion" id="to_connexion" type="submit" name="bntsubmit" value="'.$v->connexion.'"/>';
			}
			echo '</form></li>';
		}
		?>
	</ul>
<div id="ariadne"></div>
<?php
	if(!isset($_SESSION["user"])){
		echo '<div class="nonconnect_message">'.$v->nonconnect.'</div>';
	}
?>
