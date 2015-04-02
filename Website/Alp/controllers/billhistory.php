<?php
include 'PayController.php';

class billhistory extends PayController implements AlpController {

public function __construct($url)
{
	parent::PayController($url);
}

function Start()
{
	$this->Ajax()->SetFunction('GetBillingHistory');
	$this->Ajax()->SetFields(array("DefaultPrj"));

	$this->PutData ('PageHeading', array('projectlist'));
	$this->LoadView('template2015');
}
}
?>