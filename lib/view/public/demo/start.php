<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head id="head">
	<meta charset="UTF-8" />
	<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
	<link rel="icon" type="image/x-icon" href="<?php echo BASEURL.'public/img/'.$_SESSION['switcher']['name'].'/favicon.ico' ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes"/>
	<meta http-equiv="Cache-Control" content="no-cache" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	<?php echo $v['jscss']; ?>
	<title><?php echo $v['title']; ?></title>
</head>
<body>
	<header id="header">
		<?php echo $v['header']; ?>
	</header>
	<nav id="navbar">
		<?php echo $v['navigation']; ?>
	</nav>
	<section id="content">
		<?php echo $v['content']; ?>
	</section>
	<footer id="footer">
		<?php echo $v['footer']; ?>
	</footer>

	<?php echo $v['dialog']; ?>
</body>
</html>
