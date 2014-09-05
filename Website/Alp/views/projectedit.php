<?php
$db = $this->Model();
$c = $this->Cookie();
$form = $this->Forms('ProjectFormClass');

if ($db->IsSupervisor()) {
?>
<h1>Add / Edit Project</h1>
<?php
	$form->ShowFormErrors($db->ErrorMsg());
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<?php
	if ($PrjID > 0) {
		$data = $db->ReadProject($PrjID);
		$org = $data->orgid;
		$name = $data->name;
		$priority = $data->priority;
		$status = $data->status;
		$started = $data->started;
		$targetdate = $data->targetdate;
		$completed = $data->completed;
		$timerpt = $data->timerpt;
		$notes = $data->notes;		
	} else {
		$org = (isset($OrgID)) ? $OrgID : 0;
		$name = '';
		$priority = 3;
		$status = 'A';
		$started = 'Today';
		$targetdate = '';
		$timerpt = 1;
	}
	$form->ShowHiddenField ('ProjectID', $PrjID);
?>
	<table width="100%" border="0" cellpadding="3" cellspacing="0" class="">
<?php
	$orglist = $db->ReadCompanyList();
	$statuslist = $db->GetProjectStatusList();
	$timelist = array(
		array(1,'By Project'),
		array(2,'By Project Area'),
		array(3,'By Task')
	);

	
	$form->ShowTextField ('Project Name', 'PrjName', 80, 80, $name, 1);
	if ($this->UserSetting('Organizations'))
		$form->ShowListField ('Organization', 'Organization', $orglist, 2, $org);
	else
		$form->ShowHiddenField ('Organization', $db->GetOwnerCompanyID());
	$form->ShowNumericListField ('Priority', 'Priority', 1, 5, 1, $priority, 2);
	$form->ShowListField ('Status', 'Status', $statuslist, 2, $status);
	$form->ShowDatePickerField  ('Start Date', 'StartDate', $started, false, "+2y", "-2y");
	$form->ShowDatePickerField  ('Target Date', 'TargetDate', $targetdate, false, "+2y", "-2y");
	if ($PrjID > 0)
		$form->ShowDatePickerField  ('Completion Date', 'CompletionDate', $completed, false, "+0y", "-2y");
//	if ($db->HasTimeTracking())
	if ($this->UserSetting('Time'))
		$form->ShowListField ('Time Reporting', 'TimeRpt', $timelist, 0, $timerpt);
	else
		$form->ShowHiddenField ('TimeRpt', 1);
		
	$form->ShowHTMLEditField ('Notes', 'Notes', 5, 60, $notes);
?>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td></td>
			<td>
<?php
	$form->ShowSubmitButton();
	if ($PrjID > 0 && $db->IsGlobalSupervisor())
		$form->ShowRedirectButton('projectdel?prj='.$PrjID, 'Delete');
?>
		</tr>
	</table>
</form>
<?php
}
?>
