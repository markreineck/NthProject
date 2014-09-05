<?php
$db = $this->Model();
$form = $this->Forms();
$c = $this->Cookie();
$errmsg = $db->ErrorMsg();

if ($db->IsUserManager()) {
?>

<h1>User Rights</h1>
<div style="color:#0063C6; font-weight:600;">Project Rights for
<?php
	echo $db->GetUserName($UserID);
?>
</div>
<?php
	$form->ShowFormErrors($db->ErrorMsg(), $msg);

	$prjlist = $db->ReadUserAddProjects($UserID, $c->GetDefaultCompany());
	if ($prjlist) {
?>
<form name="form1" method="post" action="">
	<table >
<?php
		$form->ShowListField ('Add User to Project', 'NewProject', $prjlist);
?>
		<tr>
			<td>
<?php
	$form->ShowSubmitButton('Add Project');
	$form->ShowHiddenField ('UserID', $UserID);
?></td>
		</tr>
	</table>
</form>
<?php
	}
?>
<form name="form2" method="post">
	<table>
<?php
	$privs = $db->ReadUserGlobalPrivs($UserID);
	$form->ShowHiddenField ('UserID', $UserID);
	$form->ShowCheckBoxField ('Supervisor', 'Supervisor', 1, $privs->superuser);
	$form->ShowCheckBoxField ('User Maintenance', 'UserMaint', 1, $privs->usermaint);
?>
	</table>
<?php
	if (!$privs->superuser) {
		$privs = $db->ReadUserProjectRights($UserID);

		if ($privs) {
?>
	<table width="100%" class="tabletd" cellspacing="0" border="1" cellpadding="3">
		<tr class="tableclass">
			<th width="25%">Project</th>
			<th width="5%">Superuser</th>
			<th width="5%">Submit</th>
<?php
			if ($this->UserSetting('ApproveTasks')) {
?>
			<th width="5%">Approve</th>
<?php
			}
?>
			<th width="5%">Assign</th>
			<th width="5%">Edit</th>
			<th width="15%">To be assigned to</th>
<?php
			if ($this->UserSetting('ReleaseTasks')) {
?>
			<th width="5%">Release</th>
<?php
			}
?>
			<th width="20%">&nbsp;</th>
		</tr>
<?php
			$bg = "#F0F1F2";
			$cnt = 0;
			foreach ($privs as $row) {
				$bg = ($bg=='#F0F1F2' ? '#FFFFFF' : '#F0F1F2');
				//$puid = $arrpusers[$ctr]["projectusersid"];	
				$prjid = $row->prjid;	
?>
		<tr bgcolor="<?php echo $bg;?>" class="hover">
			<td align="left">
<?php
				echo $row->name;
				$form->ShowHiddenField ('PrjID'.$cnt, $prjid);
?>
			</td>
			<td align="center">
<?php
				$form->ShowCheckBox ('Super'.$prjid, 1, $row->superuser);
?>
			</td>
			<td align="center">
<?php
				$form->ShowCheckBox ('Submit'.$prjid, 1, $row->submit);
				if ($this->UserSetting('ApproveTasks')) {
?>
			</td>
			<td align="center">
<?php
					$form->ShowCheckBox ('Apprv'.$prjid, 1, $row->approval);
				}
?>
			</td>
			<td align="center">
<?php
				$form->ShowCheckBox ('Assign'.$prjid, 1, $row->assign);
?>
			</td>
			<td align="center">
<?php
				$form->ShowCheckBox ('Edit'.$prjid, 1, $row->edit);
?>
			</td>
			<td align="center">
<?php
				$form->ShowCheckBox ('BeAssign'.$prjid, 1, $row->assigned);
				if ($this->UserSetting('ReleaseTasks')) {
?>
			</td>
			<td align="center">
<?php
					$form->ShowCheckBox ('Release'.$prjid, 1, $row->publish);
				}
?>
			</td>
			<td align="center"><a name="remove" href="?userid=<?php echo $UserID.'&delid='.$prjid;?>" id="remove" >Remove from project</a></td>
		</tr>
<?php
				$cnt++;
			}
?>
	</table>
	<input type=hidden name="maxproject" value=<?php echo $cnt;?> />
<?php
		}
	}
?>
	<br />
<?php
	$form->ShowSubmitButton('Save');
	$form->ShowRedirectButton ('users', 'Users');
?>
</form>
<?php
}
?>
<br clear="all"
