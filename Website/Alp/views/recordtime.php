<?php
$db = $this->Model();
$form = $this->Forms('ProjectFormClass');
$errmsg = $db->ErrorMsg();
if (isset($okmsg) && !$db->HasError())
	$form->NewData();
else
	$okmsg = '';
?>

<h1>Record Time</h1>
<?php
$form->ShowFormErrors($errmsg, $okmsg);
$userlist = $db->ReadOwnerUserList();
$prjlist = $db->ReadProjectList();
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<table border="0" cellpadding="2" cellspacing="0" class="">
<?php
$form->ShowListField ('Project', 'Project', $prjlist, 1);
$form->ShowListField ('Person', 'UserID', $userlist, 1);
$form->ShowDatePickerField  ('Date', 'Date', 'Today', true, "+0y", "-1y");

// Start time
$form->ShowInputLabel ('Time', '', true);
echo '<td>';
$form->ShowNumericList ('Time1', 1, 12, 1, '', true);
echo '&nbsp;:&nbsp;';
$form->ShowList ('Time2', array('00','06','12','18','24','30','36','42','48','54'), true);
echo '&nbsp;';
$form->ShowList ('Time3', array('AM','PM'), true);
$form->CloseFieldSection();

$form->AddValidationField ('Time1', 'Hour', 'Req', 1);
$form->AddValidationField ('Time2', 'Minute', 'Req', 1);
$form->AddValidationField ('Time3', 'AM/PM', 'Req', 1);

// Time worked
$form->ShowInputLabel ('Hours worked', '', true);
echo '<td>';
$form->ShowNumericList ('Hours1', 0, 10, 1, '', true);
echo '&nbsp;.&nbsp;';
$form->ShowNumericList ('Hours2', 0, 9, 1, '', true);
$form->CloseFieldSection();

$form->ShowTextAreaField ('Comment', 'Comment', 4, 60);
?>
		<tr>
			<td>
			</td>
			<td>
<?php
$form->ShowSubmitButton();
?>
		</td>
		</tr>
</table>
</form>
<?php
$form->FormValidation();
?>