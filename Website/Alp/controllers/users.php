<?php
include 'UserController.php';

class users extends UserController implements AlpController {

function Start()
{
	$c = $this->Cookie();

	$ajax = $this->Ajax();
	$ajax->SetSection('AjaxList');
	$ajax->AddArg('sid', $this->Cookie()->GetSessionID());
	$ajax->SetFunction('GetUserList');
	$ajax->SetFields(array("DefaultUserType","DefaultOrg"));

	$this->PutData ('PageHeading', array('orglist', 'userstatuslist'));
	$this->LoadView('template2015');
}
}
?>
