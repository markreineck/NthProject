<?php
include 'TaskListController.php';

class taskbycreated extends TaskListController implements AlpController {

function Start()
{
	$this->Ajax()->SetFunction('GetTasksByCreatedOn');
	$this->Ajax()->SetFields(array("TaskStatus","DefaultPrj","DefaultUser","DefaultMilestone"));
	$this->DoTaskListPage();
	$this->LoadView('home');
}
}
?>