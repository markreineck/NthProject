<?php
include 'TaskController.php';

class taskhistory extends TaskController implements AlpController {

public function __construct($url)
{
	parent::TaskController($url);
}

function Start()
{
	$this->LoadView('home');
}
}
?>