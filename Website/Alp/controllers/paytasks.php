<?php
include 'PayController.php';

class paytasks extends PayController implements AlpController {

public function __construct($url)
{
	parent::PayController($url);
}

function Start()
{
	$db = $this->Model();

	$taskcnt = 0;
	if ($this->IsPosted('TaskCnt')) {
		$N = $this->PostedDigit('TaskCnt'); 
		for ($i=0; $i < $N; $i++) {
			if ($this->DataChanged('Paid'.$i)) {
				if ($this->IsPosted('Paid'.$i)) {
					$db->TaskIsPaid($this->PostedDigit('TaskID'.$i));
				} else {
					$db->TaskNotPaid($this->PostedDigit('TaskID'.$i));			  
				}
				if (!$db->HasError())
					$taskcnt++;
			}
		}
	}

	$this->Ajax()->SetFunction('GetPayableTasks');
	$this->Ajax()->SetFields(array("DefaultPrj","DefaultUser","DefaultPaymentStatus"));

	$msg = ($taskcnt > 0) ? $taskcnt . ' tasks have been changed' : '';
	$this->PutData ('OKMsg', $msg);
	$this->PutData ('PageHeading', array('projectlist', 'paystatuslist', 'assigntolist'));
	$this->LoadView('home');
}
}
?>