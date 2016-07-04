<?php
include 'PayController.php';

class mypayhistory extends PayController implements AlpController {

function Start()
{
	$db = $this->Model();

	$data = $db->ListMyPaidTasks($this->Cookie());

	$this->PutData ('PageHeading', array('projectlist'));
	$this->PutData ('NextPage', 'payhistory');
	$this->PutData ('data', $data);

	$this->LoadView('template2015');
}
}
?>