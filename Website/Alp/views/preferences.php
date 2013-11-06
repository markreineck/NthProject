<?php
$db = $this->Database();
$form = $this->Forms();
$errmsg = $db->ErrorMsg();

$ds = $db->ReadPreferences($db->GetUserID());
?>					
<h1>Preferences</h1>
<?php
$form->ShowFormErrors($errmsg, $OKMsg);
?>
	<form method="post">
	<table border="0" cellpadding="3" cellspacing="0" class="">
<?php
$defuser = $ds->defuser;
$notify = $ds->notify;
$namemode = $ds->namemode;
?>
		<tr>
			<td>
				<h3>Default Task Filter Options</h3>
			</td>
		</tr>
<?php
$userlist = array(
	array('-1','All'),
	array('1','Assigned to Me'),
	array('-2','Unassigned'),
);

$namelist = array(
	array('A','Full Name'),
	array('F','First Name'),
	array('L','Last Name'),
	array('I','Initials')
);

$form->ShowListField ('User', 'User', $userlist, 2, $defuser);
//$form->ShowListField ('Task Status', 'TaskStatus', $userlist, 1, $status);
//$form->ShowListField ('User Name View', 'UserNameView', $namelist, 2, $namemode);
//$form->ShowCheckBoxField ('Show Task Numbers', 'TaskNumbers', 1, $checked);
?>
		<tr>
			<td>
				<h3>Notification Options</h3>
			</td>
		</tr>
<?php

$notelist = array(
	array(0,'No notification'),
	array('P','Notify on the web site'),
	array('E','Notify by email'),
	array('B','Notify on the website and by email')
);
$form->ShowListField ('New Tasks', 'NewTasks', $notelist, 0, $ds->notifynew);
$form->ShowListField ('Completed Tasks', 'CompletedTasks', $notelist, 0, $ds->notifydone);
$form->ShowListField ('Approved Tasks', 'ApprovedTasks', $notelist, 0, $ds->notifyappr);
$form->ShowListField ('Rejected Tasks', 'RejectedTasks', $notelist, 0, $ds->notifyrej);
$form->ShowListField ('Messages', 'Messages', $notelist, 2, $ds->notifymsg);
?>
		<tr>
			<td>
<?php
$form->ShowSubmitButton();
?>
			</td>
		</tr>
</table>
</form>
