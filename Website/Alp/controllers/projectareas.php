<?php
include 'ProjectController.php';

class projectareas extends ProjectController implements AlpController {

function Start()
{
	$c = $this->Cookie();

	$ajax = $this->Ajax();
	$ajax->SetSection('AjaxList');
	$ajax->AddArg('sid', $this->Cookie()->GetSessionID());
	$ajax->SetFunction('GetProjectAreaList');
	$ajax->SetFields(array("DefaultPrj"));

	$this->PutData ('PageHeading', array('projectlist'));
	$this->LoadView('template2015');
}
}
?>
