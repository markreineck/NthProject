<?php
include 'TimeReportController.php';

class payroll extends TimeReportController implements AlpController {

function Start()
{
	$this->Ajax()->SetFunction('GetPayroll');
	$this->PutData ('PageHeading', array('timeperiodlist'));
	$this->LoadView('template2015');
}
}
?>