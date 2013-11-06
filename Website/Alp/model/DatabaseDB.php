<?php

class DatabaseDB extends DatabaseClass 
{

var $UserID;
var $TimeID;
var $UserEmail;
var $FirstName;
var $OrgID;
var $UserMaint;
var $SuperUser;
var $OwnerID;
var $ProjectSuper;
var $ProjectRelease;
var $ProjectSubmit;


function DatabaseDB($framework, $pwd='', $username='', $dbname='', $host='')
{
	$this->DatabaseClass($framework);
}

function ReadUserSession($sid)
{
	$sql = 'select s.userid, t.timeid, u.email, u.firstname, u.orgid, u.superuser, u.usermaint
from usersession s
inner join users u on s.userid=u.userid
left outer join (select timeid, userid from usertime where endon is null) t on s.userid=t.userid
where expireson>now() and s.sessionid=' . $sid;

	$data = $this->SelectRow($sql);
	if ($data && $data->userid > 0) {
		$this->SessionID = ($data->userid > 0) ? $sid : 0;
		$this->UserID = $data->userid;
		$this->TimeID = $data->timeid;
		$this->UserEmail = $data->email;
		$this->FirstName = $data->firstname;
		$this->OrgID = $data->orgid;
		$this->UserMaint = $data->usermaint;
		$this->SuperUser = $data->superuser;

		$sql = 'select orgid from subscription limit 1';
		$this->OwnerID = $this->Select($sql);

		if ($this->SuperUser) {
			$this->ProjectSuper = true;
			$this->ProjectRelease = true;
			$this->ProjectSubmit = true;
		} else {
			$sql = 'select max(superuser) superuser, max(publish) publish, max(submit) submit from projectusers where userid=' . $this->UserID;
			$data = $this->SelectRow($sql);
			$this->ProjectSuper = $data->superuser;
			$this->ProjectRelease = $data->publish;
			$this->ProjectSubmit = $data->submit;
		}
	} else {
		$sid = 0;
	}
	return $sid;
}

function ValidateUserSession($cookie)
{
	$sid = $cookie->GetSessionID();
	if ($sid > 0) {
		$mode = $this->DebugMode();
		$this->DebugMode(0);

		$sql = "RenewUserSession($sid)";
		$this->ExecuteProc($sql);
		$sid = $this->ReadUserSession($sid);
		$this->DebugMode($mode);
	}
	if ($sid == 0) {
		$args = '';
		foreach ($_GET as $name => $val) {
			if ($name == 'p') $name = 'n';
			if ($args)
				$args .= "&$name=$val";
			else
				$args .= "?$name=$val";
		}
		$this->framework->RedirectTo('/'.$args);
	}
}

function IsClockedIn()
{
	return $this->TimeID > 0;
}

function IsGlobalUserManager()
{
	if ($this->UserMaint < 1)
		return false;
	return ($this->OwnerID == $this->OrgID);
}

function IsUserManager()
{
	return (($this->UserMaint > 0) ? ($this->OwnerID == $this->OrgID) ? 1 : $this->OrgID : 0);
}

function IsGlobalSupervisor()
{
	if ($this->SuperUser < 1)
		return false;
	return ($this->OwnerID == $this->OrgID);
}

function IsSupervisor()
{
	return (($this->SuperUser > 0) ? $this->OrgID : 0);
}

function IsProjectSupervisor()
{
	return $this->ProjectSuper;
}

function CanRelease()
{
	return $this->ProjectRelease;
}

function CanSubmit()
{
	return $this->ProjectSubmit;
}

function GetSubscriptionName()
{
	return ($this->SubscrName);
}

function GetUserID()
{
	return ($this->UserID);
}

function GetSessionID()
{
	return ($this->SessionID);
}

function GetUserFirstName()
{
	return ($this->FirstName);
}

function GetCompanyID()
{
	return ($this->OrgID);
}

function GetUserEmail()
{
	return ($this->UserEmail);
}

function GetOwnerCompanyID()
{
	return ($this->OwnerID);
}

function ReadOrgName($id)
{
	$sql = "select name from organizations where orgid=" . $id;

	return $this->Select($sql);
}

function ReadCompanyList()
{
	$sql = "SELECT orgid, name from organizations order by name";
	return $this->SelectAll($sql,2);
}

function ReadUserList()
{
	$sql = "SELECT userid, name from usernames order by lastname, name";
	return $this->SelectAll($sql,2);
}

function GetTaskStatusList()
{
	$sql = 'select statusid, name from taskstatus order by name';
	return $this->SelectAll($sql, 2);
}

function ReadProjectList()
{
	$where = (($this->OwnerID == $this->OrgID)) ? '' : 'and orgid='.$this->OrgID;
	$sql = "select prjid, name from projects where status='A' and completed is null $where order by name";
	return $this->SelectAll($sql, 2);
}

function ReadProjectUserList($prjid)
{
	$owner = $this->GetOwnerCompanyID();
	$sql = "select userid, name, lastname, firstname from usernames where superuser>0 and (orgid=$owner or orgid in (select orgid from projects where prjid=$prjid))
union all
select distinct u.userid, u.name, u.lastname, u.firstname from usernames u, projectusers p where u.userid=p.userid and p.prjid=$prjid
order by lastname, firstname";
	return $this->SelectAll($sql, 2);
}

function ListProjectAreas($prjid)
{
	$sql = "select areaid, name from projectareas where prjid=$prjid order by name";
	return $this->SelectAll($sql, 2);
}

}
?>