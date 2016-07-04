<?php
// Depricated 2/16/2015
include 'TaskListController.php';

class taskbyperson extends TaskListController implements AlpController {

function Start()
{
	$this->Ajax()->SetFunction('GetTasksByAssignedTo');
	$this->Ajax()->SetFields(array("TaskStatus","DefaultPrj","DefaultUser","DefaultMilestone"));
	$this->DoTaskListPage();
	$this->LoadView('home');
}
}
?>