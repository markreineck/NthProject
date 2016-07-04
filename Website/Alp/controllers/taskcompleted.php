<?php
include 'TaskListController.php';

class taskcompleted extends TaskListController implements AlpController {

function Start()
{
	$this->Ajax()->SetFunction('GetCompletedTasks');
	$this->Ajax()->SetFields(array("DefaultPrj","DefaultUser"));

	$this->DoTaskListPage(array('projectlist', 'assigntolist', 'filters'));
	$data = $this->Model()->ListCompletedTasksByProject($this->Cookie());
	$this->PutData ('data', $data);
	$this->PutData ('icons', false);
	$this->PutData ('PageTitle', 'Completed Tasks');
	$this->PutData ('NextPage', 'showtasktree');
	$this->LoadView('template2015');
}
}
?>