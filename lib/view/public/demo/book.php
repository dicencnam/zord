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
	<?php echo $v['data']; ?>
	<?php echo $v['jscss']; ?>
	<title><?php echo $v['title']; ?></title>
</head>
<body<?php echo $v['level']; ?>>
	<abbr class="unapi-id" title="<?php echo $v['unapi']; ?>"></abbr>
	<header id="header">
		<?php echo $v['header']; ?>
	</header>
	<nav id="navbar">
		<?php echo $v['navigation']; ?>
	</nav>
	<section id="content">
		<nav id="toc">
			<div id="tocSearchBlock">
				<div id="tocSearch">
					<button id="searchButton" title="<?php echo $v['lang']->search; ?>">
						<img src="<?php echo BASEURL ?>public/img/demo/search.png" />
					</button><input id="tocSearchQuery" type="search" placeholder="<?php echo $v['lang']->search; ?>" required/><br/>
				</div>
			</div>
			<div id="tocContent">
				<?php echo $v['toc']; ?>
			</div>
			<div id="tocButton">
				<span>≡</span>
			</div>
		</nav>
		<div id="tools">
			<a class="fa fa-search fa-fw" href="<?php echo BASEURL ?>page/search" title="<?php echo $v['lang']->search_back; ?>"><i class="fa fa-arrow-left fa-stack-1x tocSearchBack"></i></a>
			<i id="tool_citation" class="fa fa-bookmark fa-fw" title="<?php echo $v['lang']->cite; ?>"></i>
			<i id="swicthTemoin" class="fa fa-tag fa-fw" title="<?php echo $v['lang']->references; ?>"></i>
			<a id="get_book" class="fa fa-book fa-fw" target="_blank" title="<?php echo $v['lang']->get_book; ?>"></a>
			<i id="tool_bug" class="fa fa-bug fa-fw" title="<?php echo $v['lang']->misprint; ?>"></i>
		</div>
		<article id="tei">
			<div id="markerAnchorLeft">❯</div>
			<div id="markerAnchorRight">❮</div>
			<?php echo $v['tei']; ?>
			<section id="footnotes"></section>
		</article>
	</section>
	<footer id="footer">
		<?php echo $v['footer']; ?>
	</footer>
	<div id="citationsImport">
		<button id="citationsButton">Citation</button>
	</div>
	<?php echo $v['dialog']; ?>
</body>
</html>
