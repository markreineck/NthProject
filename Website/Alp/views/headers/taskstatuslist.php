<?php
$db = $this->Model();
$form = $this->Forms();
$c = $this->Cookie();
$statuslist = $db->GetTaskStatusList();
array_unshift($statuslist, array(-7,'Cancelled'));
array_unshift($statuslist, array(-6,'Released'));
array_unshift($statuslist, array(-5,'Approved'));
array_unshift($statuslist, array(-4,'Complete'));
array_unshift($statuslist, array(-3,'Held'));
array_unshift($statuslist, array(-2,'Active'));
array_unshift($statuslist, array(-1,'All'));
?>
<div class="field-container inline">
Status&nbsp;<br />
<?php
$form->ShowList ('TaskStatus', $statuslist, 2, $c->GetDefaultTaskStatus(), 'ChangeTaskStatus(this)');
?>
<script language="javascript">

function ChangeTaskStatus(field) {

mycookie = new Cookie();
mycookie.SetValue("DefaultTaskStatus", field.value, 1);
<?php
echo $this->Ajax()->DefaultFieldQuery();
?>
}
</script> 
</div>
