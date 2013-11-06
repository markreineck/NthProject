<?php
include 'UserController.php';

class userreset extends UserController implements AlpController {

public function __construct($url)
{
	parent::UserController($url);
}

function Start()
{
	if (isset($this->PostData['UserID'])) {
		$userid = $this->PostData['UserID'];
		$pwd1 = $this->PostData['Password'];
		$pwd2 = $this->PostData['Password2'];
		if ($pwd1 != $pwd2) {
			$db->SetError(1, 'The passwords that you entered do not match');
		} else {
			$db = $this->Database();
			$enc = $this->LoadClass('EncryptionClass');
			$salt = $enc->CreateSalt();
			$pwd = $enc->EncryptString($pwd1, $salt);
			if (!$db->ResetUserAaccount($userid, $pwd, $salt))
				$this->RedirectTo('users');
		}
	} else {
		$userid = $this->GetData['userid'];
	}

	$this->PutData ('UserID', $userid);
	$this->LoadView('home');
}
}
?>