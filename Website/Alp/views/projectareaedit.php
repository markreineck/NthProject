<?php
$db = $this->Database();
$c = $this->Cookie();
$form = $this->Forms();
$errmsg = $db->ErrorMsg();

if ($db->IsProjectSuperUser($PrjID)) {
?>
<h1>Edit Project Area</h1>
<?php
	$form->ShowFormErrors($errmsg);
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<?php
	if ($AreaID > 0) {
		$data = $db->ReadProjectArea($AreaID);
		$PrjID = $data->prjid;
		$name = $data->name;
		$resp = $data->responsible;
		$price = $data->price;
		$targetdate = $data->targetdate;
		$completed = $data->completed;
		$paid = $data->paid;
	} else {
		$org = (isset($OrgID)) ? $OrgID : 0;
		$name = '';
		$priority = 3;
		$status = 'A';
		$started = 'Today';
		$targetdate = '';
		$timerpt = 1;
	}
?>
	<table width="100%" border="0" cellpadding="3" cellspacing="0" class="">
<?php
	$userlist = $db->ReadProjectUserList($PrjID);

	$form->ShowHiddenField ('ProjectID', $PrjID);
	$form->ShowHiddenField ('AreaID', $AreaID);
	$form->ShowTextField ('Name', 'Name', 80, 80, $name, 1);
	$form->ShowListField ('Responsible', 'Responsible', $userlist, 0, $resp);
	$form->ShowDateField  ('Target Date', 'TargetDate', $targetdate, false);
	if ($AreaID > 0)
		$form->ShowDateField  ('Completion Date', 'CompletionDate', $completed, false);
//	if ($db->HasTimeTracking()) {
	if ($this->UserSetting('Time')) {
		$form->ShowNumericField  ('Price', 'Price', 0, 999999, 5, '');
		if ($AreaID > 0) {
			$form->ShowNumericField  ('Paid', 'PaidAmt', 0, 999999, 5, $paid);
			$form->ShowDateField  ('Paid Date', 'PaidDate', $paid, false);
		}
	}
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
	//$form->ShowRedirectButton('projectareas');
	if ($AreaID > 0 && $db->IsGlobalSupervisor())
		$form->ShowRedirectButton('projectareadel?prj='.$PrjID.'&area='.$AreaID, 'Delete', 'DeleteBtn');
?>
		</tr>
	</table
</form>
<?php
}
?>
