<form id="connexion" name="userconnex" method="post"  action="<?php echo BASEURL; ?>" autocomplete="off">
	<input type="hidden" value="Start" name="module"/>
	<input type="hidden" value="connect" name="action"/>
	<input type="hidden" value="<?php echo $v['lasthref'] ?>" name="lasthref"/>
	<label><?php echo $v['lang']->login ?></label><input type="text" name="login" autocomplete="off"/><br/>
	<label><?php echo $v['lang']->password ?></label><input type="password" name="password" /><br/>
	<div>
	<input type="submit" name="submit" />
	<div>
</form>
