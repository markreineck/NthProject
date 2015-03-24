<?php
include 'ProjectController.php';

class reporticopage extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{

	$this->PutData ('PrjID', $prjid);
	$this->LoadView('home');
}
}
?>