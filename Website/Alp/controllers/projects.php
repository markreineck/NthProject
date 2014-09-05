<?php
include 'ProjectController.php';

class projects extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
	$c = $this->Cookie();

	$ajax = $this->Ajax();
	$ajax->SetSection('ProjectList');
	$ajax->AddArg('sid', $this->Cookie()->GetSessionID());
	$ajax->SetFunction('GetProjectList');
	$ajax->SetFields(array("DefaultStatus","DefaultOrg"));

	$this->PutData ('PageHeading', array('orglist', 'prjstatuslist'));
	$this->LoadView('home');
}
}
?>