<?php
class taskstatuses extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::AlpFramework($url);
	echo 'Debug=' . $this->DebugMode() . '<br>';
}

function Start()
{
	echo 'Debug=' . $this->DebugMode() . '<br>';
	$c = $this->Cookie('ProjectCookie');
	echo 'Debug=' . $this->DebugMode() . '<br>';
	$db = $this->LoadModel(array('DatabaseDB', 'SuperDB'));
	echo 'Debug=' . $this->DebugMode() . '<br>';
	if (isset($_GET['def']))
		$db->SetDefaultTaskStatus($_GET['def']);
	echo 'Debug=' . $this->DebugMode() . '<br>';
	$this->LoadView('home');
}
}
?>