<?php
$db = $this->Model();
$form = $this->Forms();

if ($this->UserSetting('Organizations') && $db->IsOwnerOrg()) {
	$c = $this->Cookie();
?>
<div class="field-container inline">
Show Organization &nbsp;<br />
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
</div>
<?php
} else {
	$form->ShowHiddenField('DefaultOrg',$db->GetCompanyID());
}
?>