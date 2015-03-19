<?php
include 'TaskListController.php';

class taskbyproject extends TaskListController implements AlpController {

public function __construct($url)
{
	parent::TaskListController($url);
}

function Start()
{
	$this->Ajax()->SetFunction('GetTasksByProject');
	$this->Ajax()->SetFields(array("TaskStatus","DefaultPrj","DefaultUser","DefaultMilestone"));
	$this->DoTaskListPage();
	$data = $this->Model()->ListActiveTasksByProject($this->Cookie());
	$this->PutData ('data', $data);
	$this->PutData ('icons', true);
	$this->PutData ('PageTitle', 'By Project');
	$this->PutData ('NextPage', 'showtasktree');
	$this->LoadView('home');
}
}
?>