<?php
function TaskStatus ($task)
{
	if ($task->approved)
		return 'Approved';
	else if ($task->approved)
		return 'Complete';
	else
		return $task->status;
}

// Moved to TaskListController
function TaskInfoLink($taskid, $descr)
{
	echo "<a href=\"taskinfo?tid=$taskid\">$descr</a>";
}

function TaskDoneLink($taskid)
{
	MakeIconLink('check.png', 'taskstatus?cid='.$taskid, 'Complete');
//	echo '<a href="taskstatus?cid='.$taskid.'"><img src="/image/check.png" alt="Complete" title="Complete" class="icons"></a>';
}

function TaskApproveLink($taskid)
{
	MakeIconLink('bluecheck.png', '?apprid='.$taskid, 'Approve');
//	echo '<a href="?apprid='.$taskid.'"><img src="/image/redcheck.png" alt="Approve" title="Approve" class="icons"></a>';
}

function TaskDispproveLink($taskid)
{
	MakeIconLink('bluex.png', 'taskstatus?rid='.$taskid, 'Disapprove');
//	echo '<a href="taskstatus?rid='.$taskid.'"><img src="/image/x.png" alt="Disapprove" title="Disapprove" class="icons"></a>';
}

function CompleteApproveIcon($userid, $superrights, $task)
{
	if (empty($task->complete)) {
		if ($task->assignedto == $userid || $superrights > 0)
			TaskDoneLink($task->taskid); 
	} else if (empty($task->approved)) {
		if ($task->approvedby == $userid || $superrights > 0)
			TaskApproveLink($task->taskid); 
	}
}

function ApproveRejectIcon($userid, $superrights, $task)
{
	if (empty($task->complete)) {
		if ($task->assignedto == $userid || $superrights > 0)
			TaskApproveLink($task->taskid); 
	} else {
		if ($task->approvedby == $userid || $superrights > 0)
			TaskDispproveLink($task->taskid); 
	}
}

function ShowTaskIcons($userid, $superrights, $task)
{
?>
	<td width="24" id="icon1<?php echo $task->taskid; ?>">
<?php
CompleteApproveIcon($userid,  $superrights > 0 || $task->superuser > 0, $task);
/*
if (empty($task->complete)) {
	if ($task->assignedto == $userid || $superrights > 0 || $task->superuser > 0)
		TaskDoneLink($task->taskid); 
} else if (empty($task->approved)) {
	if ($task->approveby == $userid || $superrights > 0 || $task->superuser > 0)
		TaskApproveLink($task->taskid); 
}
*/
?>
	</td>
	<td width="24" id="icon2<?php echo $task->taskid; ?>">
<?php
ApproveRejectIcon($userid,  $superrights > 0 || $task->superuser > 0, $task);
/*
if (empty($task->complete)) {
	if ($superrights > 0 || $task->superuser > 0)
		TaskApproveLink($task->taskid); 
} else {
	if ($task->approveby == $userid || $superrights > 0 || $task->superuser > 0)
		TaskDispproveLink($task->taskid); 
}
*/
?>
	</td>
<?php
}

function AddTaskToAreaLink($prjid, $areaid, $areaname)
{
?>
		<form action="newtask" method="post">
			<input type="hidden" value="<?php echo  $areaid; ?>" name="areaid" id="areaid" />
			<input type="hidden" value="<?php echo  $areaname; ?>" name="areaname" id="areaname" />
			<div style="float:right">
			<input type="image" src="/image/addtask.png" title="Add New Task" />
			</div>
		</form>
<?php
}

function AddTaskToProjectLink($prjid)
{
?>
		<form action="newtask" method="post">
			<input type="hidden" value="<?php echo  $prjid; ?>" name="prjid" id="prjid" />
			<div style="float:right">
			<input type="image" src="/image/addtask.png" title="Add New Task" />
			</div>
		</form>
<?php
}
?>