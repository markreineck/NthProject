<?php
include 'UserController.php';

class myaccount extends UserController implements AlpController {

public function __construct($url)
{
	parent::UserController($url);
}

private function ShowPage($okmsg='')
{
	$this->PutData ('OKMsg', $okmsg);
	$this->LoadView('home');
}

function Post()
{
	$db = $this->Model();

	$pwd1 = $this->PostedString('Password');
	$pwd2 = $this->PostedString('Password2');
	if ($pwd1 != $pwd2) {
		$db->SetError(1, 'The passwords that you entered do not match');
	} else {
		$enc = $this->LoadClass('EncryptionClass');
		$salt = $enc->CreateSalt();
		$pwd = $enc->EncryptString($pwd1, $salt);
		if (!$db->ResetMyAaccount($pwd, $salt))
			$okmsg = 'Your password has been changed';
	}
	$this->ShowPage($okmsg);
}

function Start()
{
	$userid = $this->GetNumber('userid');
	$this->ShowPage($userid);
}

}
?>