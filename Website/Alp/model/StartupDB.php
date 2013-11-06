<?php
class StartupDB extends LoginDB {

function StartupDB($framework)
{
	$this->LoginDB($framework);
}

/**********************************************************************
 *	Query Functions
 **********************************************************************/
function ReadStartupStatus()
{
	$sql = 'select s.orgid, s.name, o.orgid aorgid from subscription s left outer join organizations o on s.orgid=o.orgid';
	$data = $this->SelectRow($sql);
	if ($data) {
		$sql = 'select min(userid) from users where usermaint is not null and orgid=' . $data->orgid;
		$data->superuser = $this->Select($sql);
	}
	return $data;
}

/**********************************************************************
 *	Update Functions
 **********************************************************************/
function CreateSubscription($name, $first, $last, $ini, $email, $pwd, $salt)
{
	$name = $this->MakeStringValue($name);
	$first = $this->MakeStringValue($first);
	$last = $this->MakeStringValue($last);
	$ini = $this->MakeStringValue($ini);
	$email = $this->MakeStringValue($email);
	$phone = $this->MakeStringValue($phone);
	$pay = $this->MakeNumericValue($pay);
	$sql = "call CreateSubscription($name, $first, $last, $ini, $email, '$pwd', '$salt', null, null)";
	return $this->ExecuteProc ($sql);
}

}
?>