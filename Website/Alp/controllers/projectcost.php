<?php
include 'ProjectController.php';

class projectcost extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::__construct($url);
}

function Start()
{
	$prjid = $this->Cookie('ProjectCookie')->GetDefaultProject();

	$ajax = $this->Ajax();
	$ajax->SetSection('AjaxList');
	$ajax->AddArg('sid', $this->Cookie()->GetSessionID());
	$ajax->SetFunction('GetProjectCost');
	$ajax->SetFields(array("DefaultPrj"));

	$this->PutData ('PageHeading', array('projectlist'));
	$this->PutData ('ProjectID', $prjid);
	$this->LoadView('template2015');
}
}
?>