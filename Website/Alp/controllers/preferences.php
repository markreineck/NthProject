<?php
include 'UserController.php';

class preferences extends UserController implements AlpController {

public function __construct($url)
{
	parent::UserController($url);
}

function Start()
{
	if (isset($this->PostData['User'])) {
		$defuser = $this->PostData['User'];
		$db = $this->Database();
		$err = $db->MyPreferences($defuser, 
			$this->PostData['NewTasks'], 
			$this->PostData['CompletedTasks'], 
			$this->PostData['ApprovedTasks'], 
			$this->PostData['RejectedTasks'], 
			$this->PostData['Messages']);
		if (!$err) {
			$this->PutData('OKMsg', 'Your information has been saved.');
			$c = $this->Cookie();
			if ($defuser > 0)
				$defuser = $db->GetUserID();
			$c->SetDefaultUser($defuser);
		}
	}

	$this->LoadView('home');
}
}
?>