<?php
include 'OrgController.php';

class companies extends OrgController implements AlpController {

public function __construct($url)
{
	parent::OrgController($url);
}

function Start()
{
	$this->LoadView('home');
}
}
?>
