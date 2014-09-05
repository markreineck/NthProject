<?php
$db = $this->Model();
$c = $this->Cookie();
$form = $this->Forms('ProjectFormClass');
$errmsg = $db->ErrorMsg();

?>
<h1>Edit Milestone</h1>
<?php
$form->ShowFormErrors($errmsg);
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<?php
if ($MilestoneID > 0) {
	$data = $db->ReadMilestone($MilestoneID);
	$PrjID = $data->prjid;
	$name = $data->name;
	$targetdate = $data->targetdate;
	$descr = $data->descr;
	
} else {
	$name = '';
	$targetdate = '';
}
?>
	<table width="100%" border="0" cellpadding="3" cellspacing="0" class="">
<?php
$form->ShowHiddenField ('ProjectID', $PrjID);
$form->ShowHiddenField ('MilestoneID', $MilestoneID);
$form->ShowTextField ('Name', 'Name', 80, 80, $name, 1);
$form->ShowDatePickerField  ('Target Date', 'TargetDate', $targetdate, false, "+2y", "0");
if ($MilestoneID > 0)
	$form->ShowDatePickerField  ('Completion Date', 'CompletionDate', $data->completion, false, "+0y", "-1y");

$form->ShowHTMLEditField ('Description', 'Descr', 10, 60, $descr);
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
$form->ShowRedirectButton('milestones');
if ($MilestoneID > 0 && $db->IsGlobalSupervisor())
	$form->ShowRedirectButton('milestonedel?prj='.$PrjID.'&ms='.$MilestoneID, 'Delete');
?>
		</tr>
	</table>
</form>
