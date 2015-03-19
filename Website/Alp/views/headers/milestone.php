<?php
if ($this->UserSetting('Milestones')) {
	$db = $this->Model();
	$form = $this->Forms();
	$c = $this->Cookie();
	$statuslist = $db->GetMilestoneList($c->GetDefaultProject());
	array_unshift($statuslist, array(-1,'All'));
?>

<div class="field-container inline">
Milestone&nbsp;<br />
<?php
	$form->ShowList ('DefaultMilestone', $statuslist, 2, $c->GetDefaultMilestone(), 'ChangeMilestone(this)');
?>
<script language="javascript">

function ChangeMilestone(field) {

mycookie = new Cookie();
mycookie.SetValue("DefaultMilestone", field.value, 1);
<?php
	echo $this->Ajax()->DefaultFieldQuery();
?>
}
</script> 
</div>
<?php
}
?>