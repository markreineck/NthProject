<?php
include 'TaskController.php';

class taskstatus extends TaskController implements AlpController {

public function __construct($url)
{
	parent::TaskController($url);
}

function Start()
{
	$db = $this->Database();

	$okmsg = '';
	$emailaddr = array();

	$this->LoadLibrary('taskicons');
	$this->ApproveTasks();

	if (isset($this->PostData['TaskID'])) {
		$taskid = $this->PostData['TaskID'];
		$notes = $this->PostData['Comment'];
		$func = $this->PostData['Function'];
		if ($func == 'C') {
			$err = $db->CompleteTask($taskid, $notes);
			if (!$err) {
				$email = $this->LoadClass(array('EmailClass', 'TaskEmailClass'));
				$email->SendTaskComplete($taskid);
			}
		} else if ($func == 'R') {
			$err = $db->DisapproveTask($taskid, $notes);
			if (!$err) {
				$email = $this->LoadClass(array('EmailClass', 'TaskEmailClass'));
				$email->SendTaskDisapproved($taskid);
			}
		}
		if ($err == 0) {
			$this->RedirectTo($this->Cookie()->GetLastTaskPage());
		}
	} else if (isset($this->GetData['cid'])) {
		$func = 'C';
		$taskid = $this->GetData['cid'];
	} else if (isset($this->GetData['rid'])) {
		$func = 'R';
		$taskid = $this->GetData['rid'];
	} else {
		$this->RedirectTo($c->GetLastTaskPage());
	}

	$this->PutData ('TaskID', $taskid);
	$this->PutData ('Function', $func);
	$this->LoadView('home');
}
}
?>