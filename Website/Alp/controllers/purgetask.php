<?php
include 'TimeController.php';

class purgetask extends TimeController implements AlpController {

public function __construct($url)
{
	parent::TimeController($url);
}

function Start()
{
	$this->LoadView('home');
}
}
?>