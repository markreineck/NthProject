<?php
include 'TimeReportController.php';

class signedin extends TimeReportController implements AlpController {

public function __construct($url)
{
	parent::TimeReportController($url);
}

function Start()
{
	$this->LoadView('home');
}
}
?>