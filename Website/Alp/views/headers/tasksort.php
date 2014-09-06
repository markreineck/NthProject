<?php
$db = $this->Model();
$form = $this->Forms();
$c = $this->Cookie();
$sortlist = array(
	array('Project', 'By Project'),
	array('Priority', 'By Priority'),
	array('Due', 'By Due Date'),
	array('AssignedTo', 'By Assigned To'),
	array('CreatedOn', 'New Tasks')
);
?>
<td>
Sort by&nbsp;
<?php
$form->ShowList ('TaskSort', $sortlist, 2, $c->GetDefaultTaskStatus(), 'ChangeTaskSort(this)');
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
</td>
