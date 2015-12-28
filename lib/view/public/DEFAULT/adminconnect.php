<form id="connexion-admin" name="userconnex" method="post"  action="<?php echo BASEURL; ?>" autocomplete="off">
	<h1 style="text-align:center;"><?php echo $v['lang']->title; ?></h1>
	<input type="hidden" value="Start" name="module"/>
	<input type="hidden" value="connectAdmin" name="action"/>
	<label><?php echo $v['lang']->login ?></label><input type="text" name="login" autocomplete="off"/><br/>
	<label><?php echo $v['lang']->password ?></label><input type="password" name="password" /><br/>
	<div>
		<input type="submit" value="<?php echo $v['lang']->valid ?>" />
	<div>
</form>
