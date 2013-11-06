<?php
class userstatuses extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::AlpFramework($url);
}

function Start()
{
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'SuperDB'));
	$this->LoadView('home');
}
}
?>