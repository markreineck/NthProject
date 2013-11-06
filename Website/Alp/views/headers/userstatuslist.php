<?php
$db = $this->Database();
$form = $this->Forms();
$c = $this->Cookie();
?>
<td>
User Status &nbsp;
<?php
$list = $db->ReadUserTypes();
array_unshift($list,array(0,'All Users'));
$form->ShowList ('DefaultUserType', $list, 2, $c->GetDefaultUserType(), 'ChangeUserStatus(this)');
?>
<script language="javascript">

function ChangeUserStatus(field) {

mycookie = new Cookie();
mycookie.SetValue("DefaultUserType", field.value, 1);
<?php
echo $this->Ajax()->DefaultFieldQuery();
?>
}
</script>
</td>
