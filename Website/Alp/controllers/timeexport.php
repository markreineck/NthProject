<?php
include 'TimeReportController.php';

class timeexport extends TimeReportController implements AlpController {

public function __construct($url)
{
	parent::TimeReportController($url);
}

function Start()
{
	$this->Ajax()->SetFunction('GetTimeExport');
	$this->PutData ('PageHeading', array('projectlist', 'userlist', 'timeperiodlist'));
	$this->PutData ('data', $this->Model()->ReadTimeExport($this->Cookie()));
	$this->LoadView('template2015');
}
}
?>