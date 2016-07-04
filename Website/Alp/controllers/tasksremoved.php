<?php
include 'TaskListController.php';

class tasksremoved extends TaskListController implements AlpController {

function Start()
{
	$this->DoTaskListPage(array('projectlist', 'assigntolist', 'filters'));
	$this->LoadView('template2015');
}
}
?>