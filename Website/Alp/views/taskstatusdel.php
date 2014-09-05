<?php
$db = $this->Model();
$c = $this->Cookie();
$form = $this->Forms();

if ($db->IsGlobalSupervisor()) {
?>
<h1>Delete Task Status</h1>
<?php
	$form->ShowFormErrors($db->ErrorMsg());
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<?php
	$data = $db->ReadTaskStatus($StatusID);
?>
<p>Status <b><?php echo $data->name; ?></b> has <?php echo $TaskCnt; ?> tasks assigned to it.</p>
<br>
<p>What status would you like to give these tasks?</p>
<?php
	$list = $db->GetTaskStatusList();
	$form->ShowList ('NewStatus', $list, 1);
	echo '<br><br>';
	$form->ShowSubmitButton();
	$form->ShowRedirectButton('taskstatuses', 'Cancel');
	$form->ShowHiddenField('StatusID', $StatusID);
?>
</form>
<?php
}
?>
