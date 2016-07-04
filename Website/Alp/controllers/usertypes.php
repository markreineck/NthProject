<?php
class usertypes extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::AlpFramework($url);
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'OrgDB'));
	$db->ValidateUserSession($this->Cookie());
	$this->DBTable('UserType');
}

function Start()
{
	$this->PutData ('MenuID', 'Supervisor');
	$this->LoadView('template2015');
}
}
?>
