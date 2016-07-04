<?php
include 'ProjectController.php';

class projects extends ProjectController implements AlpController {

function Start()
{
	$c = $this->Cookie();

	$ajax = $this->Ajax();
	$ajax->SetSection('ProjectList');
	$ajax->AddArg('sid', $this->Cookie()->GetSessionID());
	$ajax->SetFunction('GetProjectList');
	$ajax->SetFields(array("DefaultStatus","DefaultOrg"));

	$this->PutData ('PageHeading', array('orglist', 'prjstatuslist'));
    $this->LoadView('template2015');
}
}
?>