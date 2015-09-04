<?php
$db = $this->Model();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Nth-Generation Project Management</title>
<?php
$this->LoadSystemJavascript('Cookie');
?>
<link href="http://fonts.googleapis.com/css?family=Lusitana" type="text/css" rel="stylesheet">
<link href="css/nthprojectstyle.css" rel="stylesheet" type="text/css">
<script src="http://code.jquery.com/jquery.js"></script>
<script type="text/javascript" src="js/menu2015.js"></script>
</head>

<body>
<header>
	<div class="container-full">
    	<div class="logo push-left">
        	<a href="/"><?php echo $this->UserSetting('SubscriptionName'); ?></a>
            <span class="sub-heading">project management</span>
        </div>
        <div class="account-nav push-right">
        	<ul class="nav">
                <li class="greetings"><i class="glyphicon glyphicon-user"></i> <span>Hi <?php echo $db->GetUserFirstName(); ?></b>!</span></li>
                <li class="dropdown">
                    <button id="AccountNav" data-toggle="dropdown" type="button" class="navbar-toggle collapsed" aria-haspopup="true" aria-expanded="false">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="AccountNav">
                        <li><a role="menuitem" href="myaccount"><i class="glyphicon glyphicon-lock"></i> Change Password</a></li>
                        <li><a role="menuitem" href="preferences"><i class="glyphicon glyphicon-cog"></i> Preferences</a></li>
                        <li><a role="menuitem" href="myuser"><i class="glyphicon glyphicon-user"></i> Your Account</a></li>
                    </ul>
                </li>
                <li>
                    <a href="" class="logout"><i class="glyphicon glyphicon-log-out"></i></a>
                </li>
            </ul>
        </div>
    </div>
</header>

    <div class="container-full" style="padding-bottom: 0;">
<?php include ('menu2015.php'); ?>    	
    </div>
<div class="container-full">
	<div class="page-body">
<?php /*
        <div class="page-title">
            <h1>
<?php echo $PageTitle; ?>            
            </h1>
        </div>
*/ ?>
        <div class="page-content">
        	<div class="left-body-sidebar">
<?php
if (isset($PageHeading))
	foreach ($PageHeading as $head)
		$this->LoadView('headers/'.$head);
?>        
            </div>
            <div class="right-body-content">
<?php
$page = (isset($NextPage)) ? $NextPage : $this->Controller();
if ($page != 'home')
	$this->LoadView($page);
?>
            </div>
        </div>
    </div>
</div>
<div class="container-full footer">
	<span>Web site design and hosting provided by <a href="http://www.nth-generation.com">Nth Generation</a> </span>
</div>
<script type="text/javascript" src="js/dropdown.js"></script>
</body>
</html>