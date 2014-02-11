<?php

class LoginDB extends DatabaseDB {

function LoginDB($framework, $pwd='', $username='', $dbname='', $host='')
{
	$this->DatabaseDB($framework, $pwd, $username, $dbname, $host);
}

function ReadSalt ($username)
{
	$sql = "select salt from users where email='$username'";
	return $this->Select($sql);
}

function LoginToDB ($username, $password)
{
	$salt = $this->ReadSalt ($username);

	if (empty($salt)) {
		$this->SetError(1, 'Invalid username and password');
	} else {
		$enc = $this->Framework()->LoadClass('EncryptionClass');

		$password = $enc->EncryptString($password, $salt);

		$sql = "call CreateUserSession('$username', '$password')";
		if (!$this->ExecuteProc($sql)) {
			$sesid = $this->SelectMysqlVariable('sessionid');

			if ($sesid > 0) {
				$sql = 'select s.sessionid, u.userid, u.superuser, u.usermaint, u.orgid, u.status, x.orgid owner, u.defuser, u.firstname, u.lastname, x.name orgname
	from users u, usersession s, subscription x
	where u.userid=s.userid and s.sessionid='.$sesid;
				return $this->SelectRow($sql);
				/*
				if ($data) {
					$defuser = ($data->defuser == 1) ? $data->userid : $data->defuser;
					$cookie->StartSession($sesid, $data->orgid, $data->owner, $data->superuser, $data->usermaint, $defuser);
				}
				*/
			}
		}

		if ($c->GetSessionID() < 1) {
			$this->SetError(1, 'Invalid username and password');
		}
	}
	return NULL;
}
/* No longer used 2/9/14
function DoLogin ($cookie, $username, $password)
{
	$sql = "call CreateUserSession('$username', '$password')";
	if (!$this->ExecuteProc($sql)) {
		$sesid = $this->SelectMysqlVariable('sessionid');

		if ($sesid > 0) {
			$sql = 'select u.userid, u.superuser, u.usermaint, u.orgid, u.status, x.orgid owner, u.defuser
from users u, usersession s, subscription x
where u.userid=s.userid and s.sessionid='.$sesid;
			$data = $this->SelectRow($sql);

			if ($data) {
				$defuser = ($data->defuser == 1) ? $data->userid : $data->defuser;
				$cookie->StartSession($sesid, $data->orgid, $data->owner, $data->superuser, $data->usermaint, $defuser);
			}
		}
	}
}
*/
function DoLogout ($cookie)
{
	$cookie->EndSession();
}

}
?>