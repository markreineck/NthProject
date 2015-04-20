<?php
$form = $this->Forms();
$c = $this->Cookie();
echo "
<!-- TaskSort=".$c->GetTimeGroup()." -->
";
$sortlist = array(
	array('User', 'By User'),
	array('Project', 'By Project'),
	array('', 'None')
);
?>

<div class="field-container inline">
Group by&nbsp;<br />
<?php
$form->ShowList ('TimeGroup', $sortlist, 2, $c->GetTimeSort(), 'ChangeTimeGroup(this)');
?>
<script language="javascript">
function ChangeTimeSort(field) {
mycookie = new Cookie();
mycookie.SetValue("TimeGroup", field.value, 1);
<?php
echo $this->Ajax()->DefaultFieldQuery();
?>
}
</script> 
</div>
