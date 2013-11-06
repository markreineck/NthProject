<?php
include 'UserController.php';

class users extends UserController implements AlpController {

public function __construct($url)
{
	parent::UserController($url);
}

function Start()
{
	$c = $this->Cookie();

	$ajax = $this->Ajax();
	$ajax->SetSection('AjaxList');
	$ajax->AddArg('sid', $this->Cookie()->GetSessionID());
	$ajax->SetFunction('GetUserList');
	$ajax->SetFields(array("DefaultUserType","DefaultOrg"));
/*
	if (isset($this->PostData['DefaultUserType']))
		$c->SetDefaultUserType($this->PostData['DefaultUserType']);
	if (isset($this->PostData['DefaultOrg']))
		$c->SetDefaultCompany($this->PostData['DefaultOrg']);
*/
	$this->PutData ('PageHeading', array('orglist', 'userstatuslist'));
	$this->LoadView('home');
}
}
?>
