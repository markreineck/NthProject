<?php
include 'UserController.php';

class myaccount extends UserController implements AlpController {

public function __construct($url)
{
	parent::UserController($url);
}

function Start()
{
	if ($this->IsPosted('UserID')) {
		$db = $this->Model();

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
	$this->PutData ('NextPage', 'userreset');
	$this->LoadView('home');
}
}
?>