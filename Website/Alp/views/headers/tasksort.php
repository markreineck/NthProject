<?php
$form = $this->Forms();
$c = $this->Cookie();

echo "
<!-- TaskSort=".$c->GetTaskSort()." -->
";

$sortlist = array(
	array('Priority', 'By Priority'),
	array('Project', 'By Project'),
	array('Due', 'By Due/Completed Date'),
	array('AssignedTo', 'By Assigned To'),
	array('CreatedOn', 'New Tasks')
);
?>

<div class="field-container inline">
Sort by&nbsp;<br />
<?php
$form->ShowList ('TaskSort', $sortlist, 2, $c->GetTaskSort(), 'ChangeTaskSort(this)');
?>
<script language="javascript">

function ChangeTaskSort(field) {

mycookie = new Cookie();
mycookie.SetValue("TaskSort", field.value, 1);

<?php
echo $this->Ajax()->DefaultFieldQuery();
?>
}
</script> 
</div>