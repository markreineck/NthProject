<?php
include 'TaskListController.php';

class taskbypriority extends TaskListController implements AlpController {

public function __construct($url)
{
	parent::TaskListController($url);
}

function Start()
{
	$this->Ajax()->SetFunction('ActiveTaskList');
//	$this->Ajax()->SetFunction('GetTasksByPriority');
	$this->Ajax()->SetFields(array("TaskSort","TaskStatus","DefaultPrj","DefaultUser","DefaultMilestone"));
	$this->DoTaskListPage();
	$this->LoadView('template2015');
}
}
?>