<?php
include 'TimeReportController.php';

class mytime extends TimeReportController implements AlpController {

public function __construct($url)
{
	parent::TimeReportController($url);
}

function Start()
{
	$this->PutData ('PageHeading', array('projectlist', 'timeperiodlist'));
	$this->PutData ('NextPage', 'mytime');
	$this->PutData ('data', $this->Database()->ReadMyTime($this->Cookie()));
	$this->LoadView('home');
}
}
?>