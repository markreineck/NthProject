<?php
include 'TaskListController.php';

class taskbycreated extends TaskListController implements AlpController {

public function __construct($url)
{
	parent::TaskListController($url);
}

function Start()
{
	$this->Ajax()->SetFunction('GetTasksByCreatedOn');
	$this->Ajax()->SetFields(array("TaskStatus","DefaultPrj","DefaultUser","DefaultMilestone"));
	$this->DoTaskListPage();
	$this->LoadView('home');
}
}
?>