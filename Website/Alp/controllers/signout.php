<?php
include 'TimeController.php';

class signout extends TimeController implements AlpController {

public function __construct($url)
{
	parent::TimeController($url);
}

function Start()
{
	if (isset($this->PostData['Password'])) {
		$db = $this->Database();

		$pwd = $this->PostData['Password'];
		$salt = $db->ReadSalt();
		if (empty($salt)) {
			$db->SetError(1, 'Invalid username and password');
		} else {
			$enc = $this->LoadClass('EncryptionClass');
	
			$pwd = $enc->EncryptString($pwd, $salt);
			$err = $db->ClockOut($pwd, $this->PostData['Comment']);
			if ($err == 0)
				$this->RedirectTo('signin');
		}
	}

	$this->LoadView('home');
}
}
?>