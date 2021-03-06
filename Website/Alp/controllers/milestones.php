<?php
include 'ProjectController.php';

class milestones extends ProjectController implements AlpController {

function Start()
{
	$c = $this->Cookie();

	if ($this->IsPosted('DefaultPrj'))
		$c->SetDefaultProject($this->PostedDigit('DefaultPrj'));
	else if (isset($_GET['pid']))
		$c->SetDefaultProject($_GET['pid']);

	$ajax = $this->Ajax();
	$ajax->SetSection('AjaxList');
	$ajax->AddArg('sid', $this->Cookie()->GetSessionID());
	$ajax->SetFunction('GetMilestoneList');
	$ajax->SetFields(array("DefaultPrj"));

	$this->PutData ('PageHeading', array('projectlist'));
	$this->LoadView('template2015');
}
}
?>
