<?php
I don't think this is used

class home extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::AlpFramework($url);
}

function Start()
{
$c = $this->Cookie('ProjectCookie');
$db = $this->LoadModel();

if (isset($this->PostData['Email'])) {

	$pwd1 = $this->PostData['Password'];
	$pwd2 = $this->PostData['Password2'];
	if ($pwd1 != $pwd2) {
		$err = 1;
		$db->SetError(1, 'The passwords that you entered do not match');
	} else {
		$enc = $this->LoadClass('EncryptionClass');
		$salt = $enc->CreateSalt();
		$pwd = $enc->EncryptString($pwd1, $salt);

		$org = $this->PostData['OrgName'];
		$first = $this->PostData['FirstName'];
		$last = $this->PostData['LastName'];
		$email = $this->PostData['Email'];
		$qstn = $this->PostData['SecQstn'];
		$ans = $this->PostData['SecAns'];

		$init = substr($first,0,1) . substr($last,0,1);

		$sql = "call CreateSubscription ('$org', 1, 1, 1, '$first', '$last', '$init', '$email', '$pwd', '$salt', '$qstn', '$ans')";

		$err = $db->ExecuteProc($sql);
	}
	if (!$err) {
		$this->RedirectTo('/');
	}
}

$this->LoadView('register');
}
}
?>