<?php
include 'UserController.php';

class useredit extends UserController implements AlpController {

public function __construct($url)
{
	parent::UserController($url);
	$this->DBTable('User');
}

function Post()
{
	$usertbl = $this->DBTable();
	$usertbl->SetPostedKey();
	$err = 0;

	if ($usertbl->DataChanged()) {
		$err = $usertbl->DoUpdate();
	}

	if (!$err) {
		$this->RedirectTo('users');
	} else {
		$this->Start();
	}
}

function Start()
{
	if ($this->IsPosted('UserID')) {
		$userid = $this->PostedDigit('UserID');
	} else {
		$userid = $this->GetDigit('userid');
	}
	$usertbl = $this->DBTable();
	$usertbl->SetKey(array('userid' => $userid));

	$this->PutData ('Verb', 'Edit');
	$this->PutData ('UserID', $userid);
	$this->LoadView('home');
}
}
?>