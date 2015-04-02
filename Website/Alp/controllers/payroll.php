<?php
include 'TimeReportController.php';

class payroll extends TimeReportController implements AlpController {

public function __construct($url)
{
	parent::TimeReportController($url);
}

function Start()
{
	$this->Ajax()->SetFunction('GetPayroll');
	$this->PutData ('PageHeading', array('timeperiodlist'));
	$this->LoadView('template2015');
}
}
?>