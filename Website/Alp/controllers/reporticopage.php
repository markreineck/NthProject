<?php
include 'ProjectController.php';

class reporticopage extends ProjectController implements AlpController {

function Start()
{
	$this->PutData ('PrjID', $prjid);
	$this->LoadView('template2015');
}
}
?>