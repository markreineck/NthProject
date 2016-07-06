<?php
$db = $this->Model();
$c = $this->Cookie();
$form = $this->Forms('ProjectFormClass');
$json = $this->LoadClass('JsonClass');
$json->JsonBase();
?>
<script type="text/javascript">
jprj = new AlpJson('ErrorMsg', 'StatusMsg');

jprj.ChangeProjectHandler = function()
{
	data = this.ExtractJsonData();
	if (data) {
		this.FillList('Area', data.Areas, true);
		this.FillList('StartMS', data.Milestones, true);
		this.FillList('EndMS', data.Milestones, true);
		this.FillList('AssignTo', data.AssignTo, true, data.Defaults.defassignedto);
		this.FillList('ApproveBy', data.ApproveBy, true, data.Defaults.defapprovedby);
	}
}

jprj.ChangeProject = function(prjid)
{
	handler = function()
	{
		this.json.ChangeProjectHandler();
	}
	this.DoJson('GetTaskLists', handler, 'SessionID='+<?php echo $c->GetSessionID(); ?>+'&PrjID='+prjid);
}
</script>
<h1>Add A New Task</h1>
<?php
$form->ShowFormErrors($db->ErrorMsg());

if ($PrjID < 1 || !($AreaID > 0)) {
	$arealist = $db->GetProjectAreaList($PrjID);
	if (count($arealist) == 1) {
		$AreaID = $arealist[0][0];
	}
}
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
	<table width="100%" border="0" cellpadding="3" cellspacing="0">
<?php
$def = $db->ReadProjectDefaults($PrjID);
$statuslist = $db->GetTaskStatusList($PrjID);
$mslist = $db->GetMilestoneList($PrjID);
//if (!($AreaID > 0)) {
	$arealist = $db->GetProjectAreaList($PrjID);
	if (count($arealist) == 1) {
		$AreaID = $arealist[0][0];
	}
//}
$prjlist = $db->ReadSubmitProjects();

$form->ShowTextField ('Description', 'Description', 80, 80, '', 1);
$form->ShowListField ('Project', 'DefaultPrj', $prjlist, 1, $PrjID, "jprj.ChangeProject(this.value)");
$form->ShowListField ('Area', 'Area', $arealist, 1, $AreaID);
$form->ShowListField ('Status', 'Status', $statuslist, 1, $db->GetDefaultTaskStatus());
$form->ShowNumericListField ('Priority', 'Priority', 1, 5, 1, $def->defpriority, 2);
if ($this->UserSetting('Milestones')) {
	$form->ShowListField ('Start After', 'StartMS', $mslist, 0);
	$form->ShowListField ('Need By', 'EndMS', $mslist, 0);
	$form->ShowHiddenField ('StartOn', '');
	$form->ShowHiddenField ('DoBy', '');
} else if ($this->UserSetting('TaskDates')) {
	$form->ShowDateField ('Start After', 'StartOn');
	$form->ShowDateField ('Need By', 'DoBy');
	$form->ShowHiddenField ('StartMS', '');
	$form->ShowHiddenField ('EndMS', '');
} else {
	$form->ShowHiddenField ('StartOn', '');
	$form->ShowHiddenField ('DoBy', '');
	$form->ShowHiddenField ('StartMS', '');
	$form->ShowHiddenField ('EndMS', '');
}
if ($this->UserSetting('TaskCost')) {	
	$form->ShowNumericField ('Cost', 'Cost', 0, 1000000, 7, '');
}
$form->ShowListField ('Assign to', 'AssignTo', $db->GetAssignToList($PrjID), 0, $def->defassignedto);
$form->ShowListField ('Approve by to', 'ApproveBy', $db->GetApproveByList($PrjID), 0, $def->defapprovedby);
$form->ShowHTMLEditField ('Notes', 'Notes', 5, 80);
?>
		<tr>
			<td></td>
			<td>
<?php
$form->ShowSubmitButton();
$form->ShowRedirectButton($c->GetLastTaskPage());
//$form->ShowHiddenField ('PrjID', $PrjID);
?>
			</td>
		  </tr>
	</table>
</form>
<script language="javascript">
/*

function ChangeProject(field) {

mycookie = new Cookie();
mycookie.SetValue("DefaultPrj", field.value, 1);
<?php
$ajax = $this->Ajax();
echo $ajax->FieldQuery('GetProjectAreaSelectList', 'Area');
if ($this->UserSetting('Milestones')) {
	echo $ajax->FieldQuery('GetMilestoneSelectList', 'EndMS');
	echo "document.getElementById('StartMS').innerHTML = document.getElementById('EndMS').innerHTML;";
}
?>
}
*/
</script> 

<?php
$form->FormValidation();
$this->Ajax()->AjaxBase();
?>