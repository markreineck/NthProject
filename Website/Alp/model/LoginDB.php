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

function DoLogout ($cookie)
{
	$cookie->EndSession();
}

}
?>