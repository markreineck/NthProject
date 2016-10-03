<?php
include 'TaskListController.php';

class taskbypriority extends TaskListController implements AlpController {

function Start()
{
	$this->Ajax()->SetFunction('ActiveTaskList');
//	$this->Ajax()->SetFunction('GetTasksByPriority');
	$this->Ajax()->SetFields(array('TaskSort','TaskStatus','DefaultPrj','DefaultUser','DefaultMilestone','Submitter'));
	$this->DoTaskListPage();
	$this->LoadView('template2015');
}
}
?>