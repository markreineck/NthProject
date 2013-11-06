<?php
include 'PayController.php';

class billtask extends PayController implements AlpController {

public function __construct($url)
{
	parent::PayController($url);
}

function Start()
{
	$db = $this->Database();
	$billcnt = 0;

	if (isset($this->PostData['TaskCnt'])) {
		$N = $this->PostData['TaskCnt']; 
		for ($i=0; $i < $N; $i++) {
			if ($this->DataChanged('Billed'.$i)) {
				if (isset($this->PostData['Billed'.$i])) {
					$db->TaskIsBilled($this->PostData['TaskID'.$i]);
				} else {
					$db->TaskNotBilled($this->PostData['TaskID'.$i]);			  
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