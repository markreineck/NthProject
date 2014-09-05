<?php
$db = $this->Model();
$c = $this->Cookie();
$form = $this->Forms();
$errmsg = $db->ErrorMsg();

if ($db->IsSupervisor()) {
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
		<tr>
			<td>Name</td>
			<td>Responsible</td>
		</tr>
<?php
	$userlist = $db->ReadProjectUserList($PrjID);

	$form->ShowHiddenField ('ProjectID', $PrjID);
	$form->ShowHiddenField ('AreaCnt', 10);
	for ($x=0; $x<10; $x++) {
?>
		<tr>
			<td><input type="text" size="80" maxlength="80" name="Name<?php echo$x; ?>"></td>
			<td><?php $form->ShowList ('Responsible'.$x, $userlist); ?></td>
		</tr>
<?php
	}
?>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>
<?php
	$form->ShowSubmitButton();
	$form->ShowRedirectButton('projectareas');
	if ($AreaID > 0 && $db->IsGlobalSupervisor())
		$form->ShowRedirectButton('projectareadel?prj='.$PrjID.'&area='.$AreaID, 'Delete');
?>
		</tr>
	</table>
</form>
<?php
}
?>
