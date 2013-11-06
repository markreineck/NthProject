<?php
include 'TaskListController.php';

class taskbymilestone extends TaskListController implements AlpController {

public function __construct($url)
{
	parent::TaskListController($url);
}

function Start()
{
	$c = $this->Cookie();

	if (isset($_GET['mid']))
		$c->SetDefaultMilestone($_GET['mid']);

	$this->Ajax()->SetFunction('GetTasksByMilestone');
	$this->Ajax()->SetFields(array("DefaultPrj","DefaultUser","DefaultMilestone"));
	$this->DoTaskListPage();
	$this->LoadView('home');
}
}
?>