<?php
class index extends AlpFramework {

function Start()
{
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'LoginDB'));
	$db->DebugMode(0);

	if ($this->IsPosted('UserName')) {
		$username = $this->PostedString('UserName');
		$password = $this->PostedString('Password');

		if ($username == 'startup') {
			$this->RedirectTo('startup');
		}

		$data = $db->LoginToDB ($username, $password);

		if ($data && isset($data->sessionid) && $data->sessionid) {
			$defuser = ($data->defuser == 1) ? $data->userid : $data->defuser;
			$c->StartSession($data->sessionid, $data->orgid, $data->owner, $data->superuser, $data->usermaint, $defuser);
		}
	} else {
		$db->DoLogout($c);
	}

	if ($c->GetSessionID() > 0) {
		if (isset($_POST['NextPage']) && $_POST['NextPage'] && $_POST['NextPage'] != 'logintest')
			$this->RedirectTo($_POST['NextPage']);
		else
			$this->RedirectTo('home');
	} else {
		$this->PutData('ErrorMsg', $db->ErrorMsg());
		$this->LoadView('login');
	}
}

}
?>