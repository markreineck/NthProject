<?php
include 'TaskListController.php';

class taskbyperson extends TaskListController implements AlpController {

public function __construct($url)
{
	parent::TaskListController($url);
}

function Start()
{
	$this->Ajax()->SetFunction('GetTasksByAssignedTo');
	$this->Ajax()->SetFields(array("TaskStatus","DefaultPrj","DefaultUser","DefaultMilestone"));
	$this->DoTaskListPage();
	$this->LoadView('home');
}
}
?>