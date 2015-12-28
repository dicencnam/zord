<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head id="head">
<meta charset="UTF-8" />
<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes"/>
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<title><?php echo $v['lang']->title ?></title>
<link rel="stylesheet" type="text/css" href="<?php echo BASEURL; ?>public/css/admin.css"/>
<script language="Javascript">
var PATH = '<?php echo BASEURL ?>';
var WEBSITES = <?php echo $v['websites'] ?>;
var WEBSITESURL = <?php echo $v['websitesURL'] ?>;
</script>
</head>
<body id="body">
<div class="admin-logout">
	<form method="post" action="<?php echo BASEURL; ?>">
		<input type="hidden" value="Adminconnect" name="module"/>
		<input type="hidden" value="disconnect" name="action"/>
		<input type="submit" name="submit" value="<?php echo $v['lang']->logout ?>"/>
	</form>
</div>

<div>
	<h1 style="text-align:center;" ><?php echo ZORD.' '.ZORD_VERSION.'<br/>'.$v['lang']->backOffice ?></h1>
	<div id="bartools" style="text-align:center;border-top:1px solid gray;border-bottom:1px solid gray;padding:5px;"></div>
</div>

<div id="workspace" ></div>
<script type="text/javascript" src="<?php echo BASEURL; ?>js/req.js"></script>
<script type="text/javascript" src="<?php echo BASEURL; ?>js/browserDetect.js"></script>
<script type="text/javascript" src="<?php echo BASEURL; ?>public/init.js"></script>

</body>
</html>
