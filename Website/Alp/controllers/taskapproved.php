<?php
include 'TaskListController.php';

class taskapproved extends TaskListController implements AlpController {

public function __construct($url)
{
	parent::TaskListController($url);
}

function Start()
{
	$this->LoadLibrary('DateRange');

	$this->Ajax()->SetFunction('GetApprovedTasks');
	$this->Ajax()->SetFields(array("DefaultDateRange","DefaultPrj","DefaultUser","DefaultMilestone"));

	$filters = array('projectlist', 'milestone', 'assigntolist', 'timeperiodlist');
	$this->DoTaskListPage($filters);

	$data = $this->Database()->ListApprovedTasksByProject($this->Cookie());
	$this->PutData ('data', $data);
	$this->PutData ('icons', false);
	$this->PutData ('PageTitle', 'Approved Tasks');
	$this->PutData ('NextPage', 'showtasktree');
	$this->LoadView('home');
}
}
?>