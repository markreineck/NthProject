<?php
include 'UserController.php';

class userdel extends UserController implements AlpController {

public function __construct($url)
{
	parent::UserController($url);
	$this->DBTable('User');
}

function Post()
{
	$usertbl = $this->DBTable();
	$usertbl->SetPostedKey();
	if ($usertbl->DataChanged()) {
		$err = $usertbl->DoUpdate();
	}

	if (!$err) {
		$this->RedirectTo('users');
	}
}

function Start()
{
	if ($this->IsPosted('UserID')) {
		$userid = $this->PostedDigit('UserID');
	} else {
		$userid = $this->GetNumber('userid');
	}
	$usertbl = $this->DBTable();
	$usertbl->SetKey(array('userid' => $userid));

	$this->PutData ('Verb', 'Edit');
	$this->PutData ('UserID', $userid);
	$this->LoadView('template2015');
}
}
?>