<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes"/>
</head>
<body>
<div>
<h1 style="text-align:center;" ><?php echo $v->connect ?></h1>
<form name="adminconnex" method="post" style="width:350px; margin:auto;text-align:center;border:1px solid black;padding:10px;border-radius:10px;" action="<?php echo BASEURL; ?>">
	<input type="hidden" value="Admin" name="module"/>
	<input type="hidden" value="connect" name="action"/>
<label style="width:100px;display: inline-block;text-align: right;"><?php echo $v->login ?></label><input type="text" name="login" /><br/>
<label style="width:100px;display: inline-block;text-align: right;"><?php echo $v->password ?></label><input type="password" name="password" /><br/>
<input type="submit" name="submit" />
<br/><br/>
<hr/>
<div style="text-align:center;"><span class="admin-button"><a href="<?php echo BASEURL; ?>" style="text-decoration:none;color:black;">←</a></span></div>
</form>
</div>
</body>
</html>
