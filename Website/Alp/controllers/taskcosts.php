<?php
include 'TaskListController.php';

class taskcosts extends TaskListController implements AlpController {

public function __construct($url)
{
	parent::TaskListController($url);
}

function Start()
{
	$this->Ajax()->SetFunction('GetTasksWithCosts');
	$this->Ajax()->SetFields(array("TaskStatus","DefaultPrj","DefaultUser"));
	$this->DoTaskListPage();
	$this->LoadView('template2015');
}
}
?>