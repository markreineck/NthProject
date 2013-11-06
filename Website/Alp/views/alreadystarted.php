<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
$this->LoadCSS();
$this->LoadCSSFile('login');
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Nth-Project</title>
</head>
<body>
<?php
$this->LoadView('externalheader');
?>
<div class="formbody">
<h1>Nth Project Startup</h1>
<p>NthProject has already been initialized on this server. </p>
<p>Return to the <a href="<?php echo $this->SiteURL(); ?>">login page</a> to log in.</p>
</div>
</body></html>
