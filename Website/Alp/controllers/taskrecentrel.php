<?php
include 'TaskListController.php';

class taskrecentrel extends TaskListController implements AlpController {

function Start()
{
	$this->Ajax()->SetFunction('GetRecentReleasedTasks');
	$this->Ajax()->SetFields(array("DefaultDateRange","DefaultPrj","DefaultUser","DefaultMilestone"));

	$filters = array('projectlist', 'milestone', 'assigntolist', 'timeperiodlist');
	$this->DoTaskListPage($filters);
	$this->LoadView('template2015');
}
}
?>