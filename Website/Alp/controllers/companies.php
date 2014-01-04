<?php
include 'OrgController.php';

class companies extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::AlpFramework($url);
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'OrgDB'));
	$db->ValidateUserSession($this->Cookie());
}

function Start()
{
	$this->LoadView('home');
}
}
?>
