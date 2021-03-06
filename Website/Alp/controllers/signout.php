<?php
include 'TimeController.php';

class signout extends TimeController implements AlpController {

function Start()
{
	if ($this->IsPosted('Password')) {
		$db = $this->Model();

		$pwd = $this->PostedString('Password');
		$salt = $db->ReadSalt();
		if (empty($salt)) {
			$db->SetError(1, 'Invalid username and password');
		} else {
			$enc = $this->LoadClass('EncryptionClass');
	
			$pwd = $enc->EncryptString($pwd, $salt);
			$err = $db->ClockOut($pwd, $this->PostedString('Comment'));
			if ($err == 0)
				$this->RedirectTo('mytime');
		}
	}

	$this->LoadView('template2015');
}

}
?>
