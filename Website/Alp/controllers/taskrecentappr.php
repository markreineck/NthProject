<?php
include 'TaskListController.php';

class taskrecentappr extends TaskListController implements AlpController {

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