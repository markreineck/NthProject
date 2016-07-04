<?php
include 'TimeController.php';

class purgetask extends TimeController implements AlpController {

function Start()
{
	$this->PutData ('MenuID', 'Supervisor');
	$this->LoadView('template2015');
}
}
?>