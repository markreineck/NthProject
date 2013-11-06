<?php
class index extends AlpFramework {

public function __construct($url)
{
	parent::AlpFramework($url);
}

function Start()
{
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'LoginDB'));
	$db->DebugMode(0);

	if (isset($this->PostData['UserName'])) {

		$username = $this->PostData['UserName'];
		$pwd = $this->PostData['Password'];
		$salt = $db->ReadSalt ($username);

		if (empty($salt)) {
			if ($username == 'startup') {
				$this->RedirectTo('startup');
			}
			$db->SetError(1, 'Invalid username and password');
		} else {
			$enc = $this->LoadClass('EncryptionClass');
	
			$pwd = $enc->EncryptString($pwd, $salt);
			$db->DoLogin($c, $username, $pwd);
			if ($c->GetSessionID() < 1) {
				$db->SetError(1, 'Invalid username and password');
			}
		}
	} else {
		$db->DoLogout($c);
	}

	if ($c->GetSessionID() > 0) {
		if (isset($_POST['NextPage']) && $_POST['NextPage'])
			$this->RedirectTo($_POST['NextPage']);
		else
			$this->RedirectTo('home');
	} else {
		$this->PutData('ErrorMsg', $this->Database()->ErrorMsg());
		$this->LoadView('login');
	}
}

}
?>