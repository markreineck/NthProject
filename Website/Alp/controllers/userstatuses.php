<?php
class userstatuses extends AlpFramework implements AlpController {

function Start()
{
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'SuperDB'));
	$this->LoadView('template2015');
}
}
?>
