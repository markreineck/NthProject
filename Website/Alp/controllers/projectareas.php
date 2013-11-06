<?php
include 'ProjectController.php';

class projectareas extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
	$c = $this->Cookie();

	$ajax = $this->Ajax();
	$ajax->SetSection('AjaxList');
	$ajax->AddArg('sid', $this->Cookie()->GetSessionID());
	$ajax->SetFunction('GetProjectAreaList');
	$ajax->SetFields(array("DefaultPrj"));
/*
	if (isset($this->PostData['DefaultPrj']))
		$c->SetDefaultProject($this->PostData['DefaultPrj']);
	else if (isset($this->GetData['pid']))
		$c->SetDefaultProject($this->GetData['pid']);
*/
	$this->PutData ('PageHeading', array('projectlist'));
	$this->LoadView('home');
}
}
?>