<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
	<style type="text/css">
	#marker_styles {
		text-align:center;
		margin : 20px 0;
	}
	.marker {
		margin : 20px auto;
		width:60em;
	}

	.marker-bib {
		font-size:1.05em;
		margin : 5px 0;
	}
	.marker-citation {
		font-size:1em;
		border-left:1px solid gray;
		padding-left:15px;
		margin : 10px 0 10px 15px;
		text-align:justify;
	}
	.marker-note {
		width:100%;
		font-size:0.9em;
		font-family:monospace;
		margin : 2px 0;
		min-height:30px;
		color:dimgrey;
	}
	</style>
</head>
<body>
	<h1><?php echo $v['title']; ?></h1>
	<?php echo $v['content']; ?>
</body>
</html>
