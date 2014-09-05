<?php
$db = $this->Model();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
<title>Project Management</title>
<script type="text/javascript" src="js/menu.js"></script>
<?php
$this->LoadCSS();
$this->LoadSystemJavascript('Cookie');
?>
</head>
<body>
<div class="banner">
	<table width="100%">
		<tr>
			<td align="left" width="150"><table width="150" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td align="left" valign="bottom" style="color: #989898; font-size:18px; padding:0; font-weight:900; text-shadow: 0px 1px 0px #000;"><?php echo $this->UserSetting('SubscriptionName'); ?></td>
					</tr>
					<tr>
						<td align="left" valign="bottom">Project Management</td>
					</tr>
				</table>
			</td>
			<td align="left" valign="bottom"><?php echo date("D F j, Y, g:i a"); ?></td>
			<td>
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr align="right">
						<td colspan="4">Welcome <b><?php echo $db->GetUserFirstName(); ?></b>! (<?php echo $this->Cookie()->GetSessionID(); ?>)</td>
					</tr>
					<tr align="right">
						<td>
						<a href="myaccount">Change Password</a>&nbsp;|&nbsp;
						<a href="preferences">Preferences </a>&nbsp;|&nbsp;
						<a href="myuser">Your Account</a>&nbsp;|&nbsp;
						<a href="/">Logout</a>
<?php
/*
if ($db->IsClockedIn()) {
?>
							<a href="signout">Logout</a>
<?php } else { ?>
							<a href="signin?err3">Login</a>
<?php
} 
*/
?>
						<td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<div class="top">
<table width="100%">
	<tr>
		<td width="150"><div style="width:150px;height:1px"></div></td>
<?php
if (isset($PageHeading))
	foreach ($PageHeading as $head)
		$this->LoadView('headers/'.$head);
?>
	</tr>
</table>
</div>
<div class="innercontent">
	<table border="0" cellpadding="0" cellspacing="0" style="font-size:12px">
		<tr>
			<td valign="top" width="150" class="menupanel">
<?php include ('menu.php'); ?>
			</td>
			<td width="100%" valign="top"><div class="datapanel">
				<div class="container">
					<div class="datacontent">
<?php
$page = (isset($NextPage)) ? $NextPage : $this->Controller();
if ($page != 'home')
	$this->LoadView($page);
?>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>
<div style="clear:both; height:10px;"></div>
<div class="footer"><a target="_blank" href="http://www.nth-generation.com">Web site design and hosting provided by Nth Generation </a></div>
</div>
</body></html>
