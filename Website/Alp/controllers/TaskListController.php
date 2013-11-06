<?php
include 'TaskBaseController.php';

abstract class TaskListController extends TaskBaseController {

public function TaskListController($url)
{
	parent::TaskBaseController($url);

	if (isset($_POST['Keyword']) && is_numeric($_POST['Keyword'])) {
		$this->RedirectTo('taskinfo?tid='.$_POST['Keyword']);
	}

	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'TaskDB', 'TaskListDB'));
	$db->ValidateUserSession($this->Cookie());
	$this->Cookie()->SetLastTaskPage($this->Controller());

	$ajax = $this->Ajax();
	$ajax->SetPage('ajaxtasklist');
	$ajax->SetSection('TaskList');
	$ajax->AddArg('sid', $this->Cookie()->GetSessionID());
}

function DoTaskListPage($filters=NULL)
{
	$this->LoadLibrary('checkfilters');

	$this->LoadLibrary('iconlinks');
	$this->LoadLibrary('taskicons');

	$this->ApproveTasks();

	if (!$filters)
		$filters = array('projectlist', 'milestone', 'taskstatuslist', 'assigntolist', 'filters');
	$this->PutData ('PageHeading', $filters);
}
}
?>
