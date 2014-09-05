<?php
$db = $this->Model();
$c = $this->Cookie();
$form = $this->Forms();
$errmsg = $db->ErrorMsg();

if (!$db->IsGlobalSupervisor())
	$orgid = $db->IsSupervisor();
if (!$db->IsSupervisor()) {
?>
<p>You do not have rights to edit project defaults</p>
<?php
} else {
?>
	<table class="ListTable">
		<tr>
			<th>Project</th>
			<th>Tasks Assigned To</th>
			<th>Tasks Approved By</th>
			<th>Default Priority</th>
		</tr>
<?php
	$cnt = 0;
	$data = $db->ListProjectDefaults($orgid);

	if (isset($data)) {
		foreach ($data as $row) {
			$userlist = $db->ReadProjectUserList($row->prjid);
?>
		<tr>
			<td><?php echo $row->name; ?></td>
			<td>
<?php
			$form->ShowList ('AssignTo'.$cnt, $userlist, 0, $row->defassignedto);
?>
			</td>
			<td>
<?php
			$form->ShowList ('ApproveBy'.$cnt, $userlist, 0, $row->defapprovedby);
?>
			</td>
			<td>
<?php
			$form->ShowNumericList ('TaskPriority'.$cnt, 1, 5, 1, $row->defpriority, 0);
			$form->ShowHiddenField('PrjID'.$cnt, $row->prjid);
?>
			</td>
		</tr>
<?php
			$cnt++;
		}
?>
		<tr>
			<td colspan="4">
<?php
	$form->ShowSubmitButton();
	$form->ShowRedirectButton('projects');
	$form->ShowHiddenField('PrjCnt', $cnt);
?>
		</tr>
	</table>
<?php
	}
}
?>