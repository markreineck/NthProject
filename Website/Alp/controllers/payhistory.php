<?php
include 'PayController.php';

class payhistory extends PayController implements AlpController {

function Start()
{
	$db = $this->Model();

	$this->Ajax()->SetFunction('GetPaymentHistory');
	$this->Ajax()->SetFields(array("DefaultPrj","DefaultUser"));

	$data = $db->ListPaidTasks($this->Cookie());

	$this->PutData ('PageHeading', array('projectlist', 'assigntolist'));
	$this->PutData ('data', $data);

	$this->LoadView('template2015');
}
}
?>