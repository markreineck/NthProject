<?php
// 12-28-12 Not used
$db = $this->Database();
$form = $this->Forms();

if (isset($this->PostData['DefaultOrg']))
	$pdb->SetDefaultCompany($this->PostData['DefaultOrg']);
else if (isset($this->PostData['DefaultStatus']))
	$pdb->SetDefaultProjectStatus($this->PostData['DefaultStatus']);
?>
<div class="top">
<table width="100%">
	<tr>
		<td width="150"><div style="width:150px;height:1px"></div></td>
		<td>
			<form method="post">
			Show Organization &nbsp;
<?php
$orglist = $db->ReadCompanyList();
array_unshift($orglist,array(0,'All Organizations'));
$form->ShowList ('DefaultOrg', $orglist, 2, $db->GetDefaultCompany(), 'form.submit()');
?>
			</form>
		</td>
		<td>
			<form method="post">
			Project Status&nbsp;
<?php
$statuslist = array(
	array('*','All'),
	array('A','Active'),
	array('I','Inactive'),
	array('C','Complete')
);
$form->ShowList ('DefaultStatus', $statuslist, 2, $db->GetDefaultProjectStatus(), 'form.submit()');
?>
			</form>
		</td>
	</tr>
</table>
</div>