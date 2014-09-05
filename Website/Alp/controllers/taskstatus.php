<?php
include 'TaskController.php';

class taskstatus extends TaskController implements AlpController {

public function __construct($url)
{
	parent::TaskController($url);
}

function Start()
{
	$db = $this->Model();

	$okmsg = '';
	$emailaddr = array();

	$this->LoadLibrary('taskicons');
	$this->ApproveTasks();

	if ($this->IsPosted('TaskID')) {
		$taskid = $this->PostedDigit('TaskID');
		$notes = $this->PostedString('Comment');
		$func = $this->PostedString('Function');
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
	} else if (isset($this->GetNumber('cid'))) {
		$func = 'C';
		$taskid = $this->GetNumber('cid');
	} else if (isset($this->GetNumber('rid'))) {
		$func = 'R';
		$taskid = $this->GetNumber('rid');
	} else {
		$this->RedirectTo($c->GetLastTaskPage());
	}

	$this->PutData ('TaskID', $taskid);
	$this->PutData ('Function', $func);
	$this->LoadView('home');
}
}
?>