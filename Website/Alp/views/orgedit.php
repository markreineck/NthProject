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
	if ($OrgID)
		$form->Populate();
	else if (!$db->HasError())
		$form->Populate();
?>
	<table width="100%" border="0" cellpadding="3" cellspacing="0" class="">
<?php
	$typelist = $db->GetOrgTypeList();
	$form->ShowBoundListField ('Organization Type', 'OrgType', $typelist, 2);
	$form->ShowBoundTextField ('Name', 'Name', 80, 80, 1);
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