<?php
$db = $this->Model();
$c = $this->Cookie();
$form = $this->Forms();

if ($db->IsGlobalSupervisor()) {
?>
<h1>Add / Edit User Types</h1>
<?php
	$form->ShowFormErrors($db->ErrorMsg());
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<table cellpadding="3" cellspacing="0">
<?php
	$statusid = $_GET['id'];
	$paylist = array(array('H','Hourly'), array('P','Per Task'));
	if ($statusid > 0) {
		$data = $db->ReadUserType($statusid);
		$sname = $data->name;
		$paytype = $data->paytype;
	} else {
		$sname = '';
		$hold = '';
	}

	$form->ShowTextField ('Status Name', 'StatusName', 40, 40, $sname, 1);		
	$form->ShowListField ('Payment Status', 'PayType', $paylist, 0, $paytype);	
?>
	<tr>
    	<td></td>
    	<td>
<?php
	$form->ShowSubmitButton();
	$form->ShowRedirectButton('userstatuses', 'Cancel');
	if ($statusid > 0) {
		$form->ShowRedirectButton('userstatusdel?id='.$statusid, 'Delete');
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
