<?php
$db = $this->Model();
$c = $this->Cookie();
$form = $this->Forms();

if ($db->IsGlobalSupervisor()) {
?>
<h1>Add / Edit Status</h1>
<?php
	$form->ShowFormErrors($db->ErrorMsg());
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<table cellpadding="3" cellspacing="0">
<?php
	$statusid = $_GET['id'];
	if ($statusid > 0) {
		$data = $db->ReadTaskStatus($statusid);
		$sname = $data->name;
		$hold = $data->hold;
	}
	else {
		$sname = '';
		$hold = '';
	}
	$form->ShowTextField ('Status Name', 'StatusName', 40, 40, $sname, 1);		
	$form->ShowCheckBoxField ('Hold', 'Hold', 1, $hold);	
?>
	<tr>
    	<td></td>
    	<td>
<?php
	$form->ShowSubmitButton();
	$form->ShowRedirectButton('taskstatuses', 'Cancel');
	if ($statusid > 0) {
		$form->ShowRedirectButton('taskstatusdel?id='.$statusid, 'Delete');
		$form->ShowHiddenField('StatusID', $statusid);
	}
?>
        </td>
    </tr>
</table>
</form>
<?php
}
?>
