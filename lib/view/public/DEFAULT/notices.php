<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head id="head">
	<meta charset="UTF-8" />
	<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
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
	<div style="width:800px;margin:25px auto 0 auto;">
		<h1 style="text-align:center;"><?php echo $v['lang']->page_tilte; ?></h1>
		<div style="height:30px;">
			<button style="float:left;" id="allselect"><?php echo $v['lang']->allselect; ?></button>
			<button style="float:left;" id="allunselect"><?php echo $v['lang']->allunselect; ?></button>
			<button style="float:right;" id="create_marcxml">MARC-XML</button>
			<button style="float:right;" id="create_mods">MODS</button>
		</div>
	</div>
	<div id="start_novelty" style="margin: 0px auto 50px auto;">
		<table id="noveltyHtml">
			<thead>
				<tr>
					<th style="width: 55px;"> </th>
					<th style="width: 55px;"><?php echo $v['lang']->id; ?></th>
					<th style="width: 150px;"><?php echo $v['lang']->authors; ?></th>
					<th style="width: 500px;"><?php echo $v['lang']->title; ?></th>
					<th style="width: 200px;"><?php echo $v['lang']->editors; ?></th>
					<th style="width: 55px;"><?php echo $v['lang']->date; ?></th>
				</tr>
			</thead>
			<tbody><?php echo $v['noveltyHtml']; ?></tbody>
		</table>
	</div>
	<table id="docsHtml">
		<thead>
			<tr>
				<th style="width: 55px;"> </th>
				<th style="width: 55px;"><?php echo $v['lang']->id; ?></th>
				<th style="width: 150px;"><?php echo $v['lang']->authors; ?></th>
				<th style="width: 500px;"><?php echo $v['lang']->title; ?></th>
				<th style="width: 200px;"><?php echo $v['lang']->editors; ?></th>
				<th style="width: 55px;"><?php echo $v['lang']->date; ?></th>
			</tr>
		</thead>
		<tbody><?php echo $v['docsHtml']; ?></tbody>
	</table>
</body>
</html>
