<?php
class userstatuses extends AlpFramework implements AlpController {

function Start()
{
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'SuperDB'));
	$this->PutData ('MenuID', 'Supervisor');
	$this->LoadView('template2015');
}
}
?>
