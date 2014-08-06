<?php

class TaskEmailClass extends EmailClass 
{

var $framework;

function TaskEmailClass($framework)
{
	$this->framework = $framework;
	$this->EmailClass($framework);
}

function SendTaskStatus($taskid, $task, $recipient, $title, $subject, $note='')
{
	$db = $this->framework->Database();
	$assn = $db->ReadTaskUsers($taskid);
	$me = $db->GetUserID();

	$cnt = 0;
	if ($recipient == 'T' && $me != $assn->assignedto) {
		$this->To($assn->assignedemail);
		$cnt++;
	}
	if ($recipient == 'A' && $me != $assn->approvedby) {
		$this->To($assn->approvedemail);
		$cnt++;
	}
	if ($recipient == 'S' && $me != $assn->submittedby) {
		$this->To($assn->submittedemail);
		$cnt++;
	}

	if ($cnt) {
		$msg = "
<p>$title:</p>
<p>
Task: <a href=\"http://projects.nth-generation.com/taskinfo?tid=$taskid\">$task->name</a><br>
Project: $task->project<br>
Area: $task->area<br>
Priority: $task->priority<br>
Assigned To: $task->assignedname<br>
</p>
$note
";

		$this->Subject("$subject: $task->project: $task->name");
		$this->Message($msg);
		$this->Send();
	}
}

function SendTaskAssigned($taskid)
{
	$data = $this->framework->Database()->ReadTaskDesc($taskid);
	$this->SendTaskStatus($taskid, $data, 'T', 'The following task has been assigned to you', 'New Task');
}

function SendTaskApproved($taskid)
{
	$data = $this->framework->Database()->ReadTaskDesc($taskid);
	$this->SendTaskStatus($taskid, $data, 'S', 'The following task has been approved', 'Task approved');
}

function SendTaskComplete($taskid, $data='')
{
	if (!$data)
		$data = $this->framework->Database()->ReadTaskDesc($taskid);
	$this->SendTaskStatus($taskid, $data, 'A', 'The following task has been completed', 'Task complete');
}

function SendTaskDisapproved($taskid, $data='')
{
	if (!$data)
		$data = $this->framework->Database()->ReadTaskDesc($taskid);
	$this->SendTaskStatus($taskid, $data, 'T', 'The following task has not been approved', 'Task not approved');
}

}
?>