<?php
include 'TimeReportController.php';

class signedin extends TimeReportController implements AlpController {

function Start()
{
	$this->PutData ('MenuID', 'Time');
	$this->LoadView('template2015');
}
    
}
?>