<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
<link rel="icon" type="image/png" href="<?php echo BASEURL; ?>liseuse/favicon.png"/>
<link rel="shortcut icon" type="image/x-icon" href="<?php echo BASEURL; ?>liseuse/favicon.png"/>
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="viewport" content="initial-scale=1">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-touch-fullscreen" content="yes" />
<style>
.titlebook {
	text-align: center;
	font-size: 18px;
	font-family: sans-serif;
	margin: 20px auto;
	text-transform: uppercase;
}
</style>
</head>
<body>
<div id="body"></div>
<script language="Javascript">
	// configuration de la liseuse
	var CONFIG = {
		epub : '<?php echo $v["name"]; ?>',
		liseusePath : '<?php echo BASEURL; ?>public/',
		epubsPath : '../public/epubs/',
		options : {}
	};
</script>
<script type="text/javascript" src="<?php echo BASEURL; ?>js/req.js"></script>
<script type="text/javascript" src="<?php echo BASEURL; ?>js/browserDetect.js"></script>
<script type="text/javascript" src="<?php echo BASEURL; ?>public/liseuse/init.js"></script>
</body>
</html>
