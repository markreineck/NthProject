<?php
include 'TaskListController.php';

class taskcosts extends TaskListController implements AlpController {

function Start()
{
	$this->Ajax()->SetFunction('GetTasksWithCosts');
	$this->Ajax()->SetFields(array("TaskStatus","DefaultPrj","DefaultUser"));
	$this->DoTaskListPage();
	$this->LoadView('template2015');
}
}
?>