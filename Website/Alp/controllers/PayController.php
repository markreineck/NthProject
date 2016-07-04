<?php
include 'TaskBaseController.php';

abstract class PayController extends TaskBaseController {

public function __construct($url)
{
	parent::__construct($url);
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'TaskDB', 'TaskListDB'));
	$db->ValidateUserSession($this->Cookie());
	$c->SetLastTaskPage($this->Controller());
	$this->LoadLibrary('checkfilters');
	$this->LoadLibrary('taskicons');

	$ajax = $this->Ajax();
	$ajax->SetPage('ajaxtasklist');
	$ajax->SetSection('TaskList');
	$ajax->AddArg('sid', $this->Cookie()->GetSessionID());

	$this->PutData ('MenuID', 'Financial');
}
}
?>
