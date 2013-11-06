<?php
include 'ProjectController.php';

class milestones extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
	$c = $this->Cookie();

	if (isset($this->PostData['DefaultPrj']))
		$c->SetDefaultProject($this->PostData['DefaultPrj']);
	else if (isset($this->GetData['pid']))
		$c->SetDefaultProject($this->GetData['pid']);

	$ajax = $this->Ajax();
	$ajax->SetSection('AjaxList');
	$ajax->AddArg('sid', $this->Cookie()->GetSessionID());
	$ajax->SetFunction('GetMilestoneList');
	$ajax->SetFields(array("DefaultPrj"));

	$this->PutData ('PageHeading', array('projectlist'));
	$this->LoadView('home');
}
}
?>