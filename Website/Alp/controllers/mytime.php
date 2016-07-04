<?php
include 'TimeReportController.php';

class mytime extends TimeReportController implements AlpController {

function Start()
{
    $c = $this->Cookie('ProjectCookie');
    $this->Ajax()->SetFunction('GetMyTime');
	$this->PutData ('PageHeading', array('projectlist', 'timeperiodlist'));
	$this->PutData ('NextPage', 'mytime');
	$this->PutData ('StartDate', $c->GetDefaultStartDate());
	$this->PutData ('EndDate', $c->GetDefaultEndDate());
	$this->PutData ('data', $this->Model()->ReadMyTime($c));
	$this->LoadView('template2015');
}

}
?>