<?php
$db = $this->Model();
$form = $this->Forms('ProjectFormClass');
$c = $this->Cookie();

$errmsg = $db->ErrorMsg();
$data = $db->ReadTask($TaskID);
?>
<h1>Task 
<?php
echo ($Function == 'C') ? 'Complete' : 'Rejected';
?>
</h1>
<h2><?php echo $data->name; ?>
</h2>
<?php
$form->ShowFormErrors($errmsg);
?>
<form method="post" name="form" id="form" >
	<table cellpadding="3" cellspacing="0">
<?php
$form->ShowHTMLEditField ('Comment', 'Comment', 4, 60);
?>
		<tr>
        <td></td>
        <td>
<?php
$form->ShowHiddenField ('TaskID', $TaskID);
$form->ShowHiddenField ('Function', $Function);
$form->ShowSubmitButton();
$form->ShowRedirectButton($c->GetLastTaskPage());
?>
		</td></tr>
	</table>
</form>
