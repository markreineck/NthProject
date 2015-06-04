<?php
include 'UserController.php';

class userreset extends UserController implements AlpController {

function Start()
{
	$db = $this->Model();

	if ($this->IsPosted('DeActID')) {
		if (!$db->DeactivateUserAccount($this->PostedDigit('DeActID')))
			$this->RedirectTo('users');
	} else if ($this->IsPosted('UserID')) {
		$userid = $this->PostedDigit('UserID');
		$pwd1 = $this->PostedString('Password');
		$pwd2 = $this->PostedString('Password2');
		if ($pwd1 != $pwd2) {
			$db->SetError(1, 'The passwords that you entered do not match');
		} else {
			$enc = $this->LoadClass('EncryptionClass');
			$salt = $enc->CreateSalt();
			$pwd = $enc->EncryptString($pwd1, $salt);
			if (!$db->ResetUserAaccount($userid, $pwd, $salt))
				$this->RedirectTo('users');
		}
	} else {
		$userid = $this->GetNumber('userid');
	}

	$this->PutData ('UserID', $userid);
	$this->LoadView('template2015');
}
}
?>
