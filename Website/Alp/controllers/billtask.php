<?php
include 'PayController.php';

class billtask extends PayController implements AlpController {

public function __construct($url)
{
	parent::PayController($url);
}

function Start()
{
	$db = $this->Model();
	$billcnt = 0;

	if ($this->IsPosted('TaskCnt')) {
		$N = $this->PostedDigit('TaskCnt'); 
		for ($i=0; $i < $N; $i++) {
			if ($this->DataChanged('Billed'.$i)) {
				if ($this->IsPosted('Billed'.$i)) {
					$db->TaskIsBilled($this->PostedDigit('TaskID'.$i));
				} else {
					$db->TaskNotBilled($this->PostedDigit('TaskID'.$i));			  
				}
				if (!$db->HasError())
					$billcnt++;
			}
		}
	}

	$this->Ajax()->SetFunction('GetBillableTasks');
	$this->Ajax()->SetFields(array("DefaultPrj","DefaultUser","DefaultPaymentStatus"));

	$msg = ($billcnt > 0) ? $billcnt . ' tasks updated' : '';
	$this->PutData ('OKMsg', $msg);
	$this->PutData ('PageHeading', array('projectlist', 'paystatuslist', 'assigntolist'));
	$this->PutData ('ErrorMsg', $db->ErrorMsg());
	$this->LoadView('home');
}
}
?>