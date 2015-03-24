<?php
$form = $this->Forms();
$c = $this->Cookie();
?>
<div class="field-container inline">
Project Status&nbsp;<br />
<?php
$statuslist = array(
	array('*','All'),
	array('A','Active'),
	array('I','Inactive'),
	array('C','Complete')
);
$form->ShowList ('DefaultStatus', $statuslist, 2, $c->GetDefaultProjectStatus(), 'ChangeProjectStatus(this)');
?>
<script language="javascript">

function ChangeProjectStatus(field) {

mycookie = new Cookie();
mycookie.SetValue("DefaultPrjStatus", field.value, 1);
<?php
echo $this->Ajax()->DefaultFieldQuery();
?>
}
</script> 
</div>
