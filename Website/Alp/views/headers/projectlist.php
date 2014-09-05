<?php
$db = $this->Model();
$form = $this->Forms();
$c = $this->Cookie();
?>
<td>
Project &nbsp;
<?php
$prjlist = $db->ReadProjectList();
array_unshift($prjlist, array(-1,'All'));
$form->ShowList ('DefaultPrj', $prjlist, 2, $c->GetDefaultProject(), 'ChangeDefaultProject(this)');
?>
<script language="javascript">

function ChangeDefaultProject(field) {

mycookie = new Cookie();
mycookie.SetValue("DefaultPrj", field.value, 1);
mycookie.SetValue("DefaultMilestone", 0, 1);
<?php
echo $this->Ajax()->DefaultFieldQuery();
if (array_search('milestone',$PageHeading) && $this->UserSetting('Milestones')) {
	echo $this->Ajax()->FieldQuery('GetMilestoneSelectList', 'DefaultMilestone');
}
?>
}
</script> 
</td>