<h3>Edit Organization</h3>
<?php 
$db = $this->Model();

if ($db->IsGlobalSupervisor()) {
	$form = $this->Forms();
	$orgtbl = $this->DBTable();
	$errmsg = $db->ErrorMsg();
	$form->ShowFormErrors($errmsg);
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<?php
	if (!$db->HasError() && $orgtbl->HasKey())
		$orgtbl->Populate();
?>
	<table border="0" cellpadding="3" cellspacing="0" class="">
<?php
	$typelist = $db->GetOrgTypeList();
	$orgtbl->ShowListField ('OrgType', $typelist);
	$orgtbl->ShowTextField ('Name');
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