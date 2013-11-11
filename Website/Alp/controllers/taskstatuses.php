<?php
class taskstatuses extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::AlpFramework($url);
}

function Start()
{
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'SuperDB'));
	if (isset($_GET['def']))
		$db->SetDefaultTaskStatus($_GET['def']);
	$this->LoadView('home');
}
}
?>