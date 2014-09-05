<?php
include 'ProjectController.php';

class projectinfo extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
	$c = $this->Cookie();

	$this->LoadLibrary('iconlinks');
	$this->LoadLibrary('taskicons');

	if ($this->IsPosted('DefaultPrj'))
		$c->SetDefaultProject($this->PostedNumber('DefaultPrj'));
	else if (isset($_GET['id'])
		$c->SetDefaultProject($_GET['id']);

	$filters = array('projectlist');
	$this->PutData ('PageHeading', $filters);
	$this->LoadView('home');
}
}
?>