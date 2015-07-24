<?php
include 'TaskListController.php';

class taskrecentappr extends TaskListController implements AlpController {

public function __construct($url)
{
	parent::TaskListController($url);
}

function Start()
{
	$this->LoadLibrary('DateRange');

	$this->Ajax()->SetFunction('GetRecentApprovedTasks');
	$this->Ajax()->SetFields(array("DefaultDateRange","DefaultPrj","DefaultUser"));

	$filters = array('projectlist', 'assigntolist', 'timeperiodlist');
	$this->DoTaskListPage($filters);
	$this->LoadView('template2015');
}
}
?>