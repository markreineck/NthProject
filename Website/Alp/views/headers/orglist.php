<?php
$db = $this->Database();

if ($this->UserSetting('Organizations') && $db->GetCompanyID() == $db->GetOwnerCompanyID()) {

	$form = $this->Forms();
	$c = $this->Cookie();
?>
<td>
Show Organization &nbsp;
<?php
	$orglist = $db->ReadCompanyList();
	array_unshift($orglist,array(0,'All Organizations'));
	$form->ShowList ('DefaultOrg', $orglist, 2, $c->GetDefaultCompany(), 'ChangeOrganization(this)');
?>
<script language="javascript">

function ChangeOrganization(field) {

mycookie = new Cookie();
mycookie.SetValue("DefaultCompany", field.value, 1);
<?php
echo $this->Ajax()->DefaultFieldQuery();
?>
}
</script>
</td>
<?php
}
?>