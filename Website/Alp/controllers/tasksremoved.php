<?php
include 'TaskListController.php';

class tasksremoved extends TaskListController implements AlpController {

public function __construct($url)
{
	parent::TaskListController($url);
}

function Start()
{
	$this->DoTaskListPage(array('projectlist', 'assigntolist', 'filters'));
	$this->LoadView('home');
}
}
?>