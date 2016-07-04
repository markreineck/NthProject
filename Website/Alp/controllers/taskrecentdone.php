<?php
include 'TaskListController.php';

class taskrecentdone extends TaskListController implements AlpController {

function Start()
{
	$this->Ajax()->SetFunction('GetTasksByCompleted');
	$this->Ajax()->SetFields(array("DefaultPrj","DefaultUser"));

	$this->DoTaskListPage(array('projectlist', 'assigntolist', 'filters'));
	$this->LoadView('template2015');
}
}
?>