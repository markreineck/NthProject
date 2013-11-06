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

	if (isset($this->PostData['DefaultPrj']))
		$c->SetDefaultProject($this->PostData['DefaultPrj']);
	else if ($this->GetData['id'])
		$c->SetDefaultProject($this->GetData['id']);

	$filters = array('projectlist');
	$this->PutData ('PageHeading', $filters);
	$this->LoadView('home');
}
}
?>