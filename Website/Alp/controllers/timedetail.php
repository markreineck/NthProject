<?php
include 'TimeReportController.php';

class timedetail extends TimeReportController implements AlpController {

public function __construct($url)
{
	parent::TimeReportController($url);
}

function Start()
{
    $c = $this->Cookie('ProjectCookie');
	$this->Ajax()->SetFunction('GetTimeDetail');
	$this->PutData ('PageHeading', array('projectlist', 'userlist', 'timeperiodlist'));
	$this->PutData ('data', $this->Model()->ReadTimeExport($c));
	$this->LoadView('home');
}
}
?>