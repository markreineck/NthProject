<?php
class taskstatuses extends AlpFramework implements AlpController {

function Start()
{
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'SuperDB'));
	if (isset($_GET['def']))
		$db->SetDefaultTaskStatus($_GET['def']);
	$this->PutData ('MenuID', 'Supervisor');
	$this->LoadView('template2015');
}
}
?>