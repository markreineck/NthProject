<?php
include 'UserController.php';

class preferences extends UserController implements AlpController {

function Start()
{
	if ($this->IsPosted('User')) {
		$defuser = $this->PostedString('User');
		$db = $this->Model();
		$err = $db->MyPreferences($defuser, 
			$this->PostedString('UserNameView'), 
			$this->PostedString('NewTasks'), 
			$this->PostedString('CompletedTasks'), 
			$this->PostedString('ApprovedTasks'), 
			$this->PostedString('RejectedTasks'), 
			$this->PostedString('Messages'));
		if (!$err) {
			$this->PutData('OKMsg', 'Your information has been saved.');
			$c = $this->Cookie();
			if ($defuser > 0)
				$defuser = $db->GetUserID();
			$c->SetDefaultUser($defuser);
		}
	}

	$this->LoadView('template2015');
}
}
?>
