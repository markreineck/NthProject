<h3>Edit Organization</h3>
<?php 
$db = $this->Database();

if ($db->IsGlobalSupervisor()) {
	$form = $this->Forms();
	$errmsg = $db->ErrorMsg();
	$form->ShowFormErrors($errmsg);
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<?php
	if (!$db->HasError())
		$form->Populate();
?>
	<table border="0" cellpadding="3" cellspacing="0" class="">
<?php
	$typelist = $db->GetOrgTypeList();
	$form->ShowBoundListField ('OrgType', $typelist);
	$form->ShowBoundTextField ('Name');
?>
		<tr>
			<td></td><td>
<?php
	$form->ShowSubmitButton();
	$form->ShowRedirectButton('companies');
	$form->ShowSubmitButton("Delete");
?>
			</td>
		</tr>
	</table>
</form>
<?php
	$form->FormValidation();
}
?>