<?php
// Depricated 2/16/2015
include 'TaskListController.php';

class taskbytarget extends TaskListController implements AlpController {

function Start()
{
	$this->Ajax()->SetFunction('GetTasksByDue');
	$this->Ajax()->SetFields(array("TaskStatus","DefaultPrj","DefaultUser","DefaultMilestone"));
	$this->DoTaskListPage();
	$this->LoadView('home');
}
}
?>