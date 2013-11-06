<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Untitled Document</title>
<?php
$json = $this->LoadClass('JsonClass');
$json->JsonBase();
?>
<script type="text/javascript">
jlist = new AlpJson('ErrorMsg', 'StatusMsg');
</script>
</head>

<body>
<form name="form1" method="post" action="">
	<label for="List"></label>
	<select name="List" id="List">
	</select>
<input type="button" name="Button" id="Button" value="Go" onClick="jlist.JSONFillList('ProjectList','List')">
</form>
<div id="FillDiv"></div>
<input type="button" name="Button" id="Button" value="Go" onClick="jlist.JSONFillHtml('Test','FillDiv')">
<div id="ErrorMsg"></div>
<div id="StatusMsg"></div>
</body>
</html>