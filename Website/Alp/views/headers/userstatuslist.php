<?php
$db = $this->Model();
$form = $this->Forms();
$c = $this->Cookie();
?>
<div class="field-container inline">
User Status &nbsp;<br />
<?php
$list = $db->ReadUserTypes();
array_unshift($list,array('I','All Inactive Users'));
array_unshift($list,array('A','All Active Users'));
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
</div>
