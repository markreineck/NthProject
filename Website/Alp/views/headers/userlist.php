<?php
$db = $this->Database();
$form = $this->Forms();
$c = $this->Cookie();
?>
<td>
Show User &nbsp;
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
</td>