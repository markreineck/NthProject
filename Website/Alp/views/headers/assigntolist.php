<?php
$db = $this->Model();
$form = $this->Forms();
$c = $this->Cookie();
?>
<td>
Assigned to &nbsp;
<?php
$userlist = $db->ReadUserList('Act');
array_unshift($userlist, array(-2,'Unassigned'));
array_unshift($userlist, array(-1,'All'));
$form->ShowList ('DefaultUser', $userlist, 0, $c->GetDefaultUser(), 'ChangeAssignedTo(this)');
?>
<script language="javascript">

function ChangeAssignedTo(field) {

mycookie = new Cookie();
mycookie.SetValue("DefaultUser", field.value, 1);
<?php
echo $this->Ajax()->DefaultFieldQuery();
?>
}
</script> 
</td>