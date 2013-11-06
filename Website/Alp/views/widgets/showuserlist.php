<?php
$db = $this->Database();
$this->LoadLibrary('iconlinks');

if ($db->IsUserManager()) {
?>
<table class="ListTable">
	<tr>
		<th>Company</th>
		<th>Name</th>
		<th>User Type</th>
		<th colspan="4">Actions</th>
	</tr>
<?php
	$data = $db->GetUserList($Company, $UserStatus);
	$stripe = 1;

	foreach ($data as $usersql) { 
		$userid = $usersql->userid;
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td><?php echo $usersql->orgname; ?></td>
		<td><?php echo $usersql->name; ?></td>
		<td><?php echo $usersql->typename; ?></td>
		<td>
<?php
		MakeIconLink('pencil.png', 'useredit?userid='.$userid, 'Edit');
?>
		</td>
<?php
		if ($usersql->hasaccount < 1) {
?>
		<td></td>
		<td>
<?php
			MakeIconLink('unlock.png', 'userreset?userid='.$userid, 'Enable account');
?>
		</td>
<?php
		} else {
?>
		<td>
<?php
			MakeIconLink('key.png', 'userrights?userid='.$userid, 'Rights');
?>
		</td>
		<td>
<?php
			MakeIconLink('unlock.png', 'userreset?userid='.$userid, 'Reset password');
?>
		</td>
<?php
		}
?>
	</tr>
<?php 
		$stripe = ($stripe > 1) ? 1 : 2;
	}
?>
</table>
<?php
}
?>
