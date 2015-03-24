<?php
$db = $this->Model();
$form = $this->Forms();
$c = $this->Cookie();
?>
<div class="field-container inline">
Show User &nbsp;<br />
<?php
$userlist = $db->ReadUserList();
array_unshift($userlist, array(-1,'All'));
$form->ShowList ('DefaultUser', $userlist, 2, $c->GetDefaultUser(), 'ChangeDefaultUser(this)');
?>
<script language="javascript">

function ChangeDefaultUser(field) {

mycookie = new Cookie();
mycookie.SetValue("DefaultUser", field.value, 1);
<?php
echo $this->Ajax()->DefaultFieldQuery();
?>
}
</script> 
</div>