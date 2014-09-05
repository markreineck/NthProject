<?php
class startup extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::AlpFramework($url);
	$db = $this->LoadModel(array('DatabaseDB', 'LoginDB', 'StartupDB'));
}

function Post()
{
	$db = $this->Model();
	if (isset($_POST['Organization'])) {
		$pwd1 = $_POST['Password'];
		$pwd2 = $_POST['Password2'];
		if ($pwd1 != $pwd2) {
			$db->SetError(1, 'The passwords that you entered do not match');
		} else {
			$enc = $this->LoadClass('EncryptionClass');
			$salt = $enc->CreateSalt();
			$pwd = $enc->EncryptString($pwd1, $salt);
			$email = $_POST['Email'];

			if (!$db->CreateSubscription($_POST['Organization'],
					$_POST['FirstName'],
					$_POST['LastName'],
					$_POST['Initials'],
					$email, $pwd, $salt,
					$_POST['SecQstn'],
					$_POST['SecAns'])) {

				$c = $this->Cookie('ProjectCookie');
				$db->DoLogin($c, $email, $pwd);
				if ($c->GetSessionID() < 1) {
					$db->SetError(1, 'Invalid username and password');
				} else {
					$this->RedirectTo('home');
				}
			}
		}
	} else {
		$data = $db->ReadStartupStatus();
		if (!$data) {
			$this->LoadView('startup');
		} else if ($data->superuser) {
			$this->LoadView('alreadystarted');
		} else if ($data->aorgid) {
echo 'No super user account';
		} else if ($data->name) {
echo 'No organization record';
		} else {
			$this->LoadView('startup');
		}
	}
}

function Start()
{
	$this->LoadView('startup');
}

}
?>