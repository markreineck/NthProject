<?php
include 'UserController.php';

class useradd extends UserController implements AlpController {

public function __construct($url)
{
	parent::__construct($url);
	$this->DBTable('User');
}

function Post()
{
	$usertbl = $this->DBTable();
	if ($usertbl->DataChanged()) {
		$err = $usertbl->DoCreate();
	}

	if (!$err) {
		$this->RedirectTo('users');
	}
}

function Start()
{
//	$c = $this->Cookie();
	$this->PutData ('NextPage', 'useredit');
	$this->PutData ('Verb', 'Create');
	$this->PutData ('OrgID', $orgid);
	$this->LoadView('template2015');
}
}
?>
