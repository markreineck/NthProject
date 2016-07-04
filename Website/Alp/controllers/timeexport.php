<?php
include 'TimeReportController.php';

class timeexport extends TimeReportController implements AlpController {

function Start()
{
	$this->Ajax()->SetFunction('GetTimeExport');
	$this->PutData ('PageHeading', array('projectlist', 'userlist', 'timeperiodlist', 'timegroup'));
	$this->PutData ('data', $this->Model()->ReadTimeExport($this->Cookie()));
	$this->LoadView('template2015');
}
}
?>
