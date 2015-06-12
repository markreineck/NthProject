<?php
$db = $this->Model();
$c = $this->Cookie();
$form = $this->Forms();
$errmsg = $db->ErrorMsg();

?>
<h1>Task List View</h1>
<?php
//<h3>
$result = $db->TaskSearch($PrjID, $UserID, $SprintID, $Priority);
/*
if ($AreaID > 0) {
	$prj = $db->ReadProjectAndArea($AreaID);
	echo $prj->project.': '.$prj->area;
	$result = $db->ListAreaTasks($AreaID);
	$PrjID = $prj->prjid;
} else {
	echo $db->ReadProjectName($PrjID);
	$result = $db->ListProjectTasks($PrjID);
}
*/
//</h3>
?>
<?php

$form->ShowFormErrors($errmsg, $OKMsg);
?>
<form method="post">
	<table class="ListTable">
		<tr>
<?php
//if ($db->HasMilestones()) {
if ($this->UserSetting('Milestones')) {
?>
			<th rowspan="2">Task</th>
			<th rowspan="2">Project Area</th>
			<th rowspan="2">Priority</th>
			<th rowspan="2">Assigned to</th>
			<th rowspan="2">Status</th>
			<th colspan="2" align="center">Milestone</th>
		</tr>
		<tr>
			<th>Start</th>
			<th>End</th>
<?php
} else {
?>
			<th width="40%" >Task</th>
			<th>Project Area</th>
			<th>Priority</th>
			<th>Assigned to</th>
			<th>Status</th>
<?php
	if ($this->UserSetting('TaskDates')) {
?>
			<th>Need By</th>
<?php
	}
}
?>
		</tr>
<?php
$count = 1;
$stripe = 1;

//if ($db->HasMilestones())
if ($this->UserSetting('Milestones'))
	$mslist = $db->GetMilestoneList($PrjID);
$arealist = $db->GetProjectAreaList($PrjID);
$asslist = $db->GetAssignToList($PrjID);
$statuslist = $db->GetTaskStatusList();

foreach ($result as $r) {
?>
		<tr class="stripe<?php echo $stripe; ?>">
			<td><?php echo $r->name; ?></td>
			<td>
<?php
	$form->ShowHiddenField ('TaskID'.$count, $r->taskid);
	$form->ShowHiddenField ('TaskName'.$count, $r->name);
	$form->ShowList ('Area'.$count, $arealist, 2, $r->areaid);
?>
			</td>
			<td>
<?php
	$form->ShowNumericList ('Priority'.$count, 1, 5, 1, $r->priority, 2);
?>
			</td>
			<td>
<?php
	$form->ShowList ('AssnTo'.$count, $asslist, 0, $r->assignedto);
?>
			</td>
			<td>
<?php
	$form->ShowList ('Status'.$count, $statuslist, 2, $r->status);
?>
			</td>
			<td>
<?php
	if ($this->UserSetting('Milestones')) {
//	if ($db->HasMilestones()) {
		$form->ShowList ('Start'.$count, $mslist, 0, $r->startmilestone);
?>
			</td>
			<td>
<?php
		$form->ShowList ('Finish'.$count, $mslist, 0, $r->endmilestone);
	} else if ($this->UserSetting('TaskDates')) {
		echo "<input name=\"Finish$count\" size=\"10\" maxlength=\"10\" value=\"$r->needby\">";
	}
?>
			</td>
		</tr>
<?php
	$count += 1;
	$stripe = ($stripe > 1) ? 1 : 2;
}
?>
		<tr>
			<td colspan="7" style="border-bottom:1px #999 solid;"></td>
		</tr>
		<tr>
			<td>
			<input type="hidden" name="TaskCnt" value="<?php echo $count;?>">
<?php
$form->ShowSubmitButton();
$form->ShowRedirectButton($c->GetLastTaskPage());
?>
			</td>
		</tr>
	</table>
</form>
