<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Nth-Generation Project Management</title>
<?php
$this->LoadCSS();
$this->LoadCSSFile('login');
$form = $this->Forms();
?>
<link href="http://fonts.googleapis.com/css?family=Lusitana" type="text/css" rel="stylesheet">
<link href="css/nthprojectstyle.css" rel="stylesheet" type="text/css">
<script src="http://code.jquery.com/jquery.js"></script>
</head>
<body>
<header>
	<div class="container-full">
<?php
$this->LoadView('externalheader');
?>
    </div>
</header>
<br clear="all">
<div class="container">
	<form id="LoginForm" name="LoginForm" method="post" action="<?php echo $this->SiteURL(); ?>" class="form-signin" >
		<?php
        
        $form->ShowFormErrors($ErrorMsg);
        
        $args = '';
        foreach ($_GET as $name => $val) {
            if ($name == 'n')
                $nextpage = $val;
            else if ($args)
                $args .= "&$name=$val";
            else
                $args .= "?$name=$val";
        }
        if ($nextpage)
        
        $form->ShowHiddenField('NextPage', $nextpage . $args);
        ?>
		<h2 class="form-signin-heading">Project Login</h2>
		<label style="color:#FF3030;"><?php if (isset($msg)) echo $msg; ?></label>
		<label>Email Address</label><br />

		<input  type="email" name="UserName" id="UserName" class="form-control" />
		<br /><br />
		<label>Password</label><br />
		<input name="Password" type="password" id="Password" class="form-control" />
		<br /><br />
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="signin" id="signin" >Sign in</button>
	</form>    
</div> 
</body>
</html>
