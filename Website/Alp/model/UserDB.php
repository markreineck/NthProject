<?php
class UserDB extends DatabaseDB {

function UserDB($framework)
{
	$this->DatabaseDB($framework);
}

/**********************************************************************
 *	Query Functions
 **********************************************************************/
function GetUserList($org, $type)
{
	$where = ($org > 0) ? 'where o.orgid=' . $org : '';
	if ($type) {
		if (empty($where))
			$where = 'where ';
		else
			$where .= ' and ';

		if ($type == 'A') {
			$where .= 'u.hasaccount is not null';
		} else if ($type == 'I') {
			$where .= 'u.hasaccount is null';
		} else if ($type > 0) {
			$where .= 'u.status=' . $type;
		}
	}

	$sql = "select u.userid, u.firstname, u.lastname, u.name, u.initials, t.name typename, u.hasaccount, o.name orgname
from usernames u
inner join organizations o on u.orgid=o.orgid
left outer join userstatus t on u.status=t.statusid
$where
order by o.name, u.lastname";

	return $this->SelectAll($sql);
}

function GetUserName($userid)
{
	$sql = 'select name from usernames where userid='.$userid;
	return $this->Select($sql);
}

function ReadUser($userid=0)
{
	if ($userid == 0)
		$userid = $this->GetUserID();
	$sql = 'select orgid, initials, firstname, lastname, email, status from users where userid='.$userid;
	return $this->SelectRow($sql);
}

function ReadUserGlobalPrivs($userid)
{
	$sql = 'select superuser, usermaint from users where userid='.$userid;
	return $this->SelectRow($sql);
}

function ReadPreferences($userid)
{
	$sql = 'select defuser, namemode, notifynew, notifydone, notifyappr, notifyrej, notifymsg from users where userid='.$userid;
	return $this->SelectRow($sql);
}

function ReadUserTypes()
{
	$sql = 'select statusid, name from userstatus order by statusid';
	return $this->SelectAll($sql,2);
}

function ReadUserContactEditFields($userid)
{
	if ($userid > 0)
		$sql = "SELECT f.fieldid, f.datatype, f.maxlen, f.required, f.name, u.value
FROM fielddef f
left outer join (select fieldid, value from userfields where userid=$userid) u on f.fieldid=u.fieldid
where f.fieldtype='U' order by f.fieldid";
	else
		$sql = "SELECT f.fieldid, f.datatype, f.maxlen, f.required, f.name, null value
FROM fielddef f
where f.fieldtype='U' order by f.fieldid";

	return $this->SelectAll($sql);
}

function ReadUserAddProjects($userid, $deforg)
{
	$sql = "select prjid, name from projects where prjid not in (select prjid from projectusers where userid=$userid)";
	if ($deforg > 0) 
		$sql .= ' and orgid=' . $deforg;
	$sql .= ' order by name';

	return $this->SelectAll($sql,2);
}

function ReadUserProjectRights($userid)
{
	$sql = "SELECT pu.userid, pu.prjid, pu.superuser, pu.submit, pu.approval, pu.assigned, pu.assign, pu.edit, pu.publish, p.name, p.prjid FROM projectusers pu, projects p WHERE pu.userid=$userid AND pu.prjid=p.prjid order by p.name";
	return $this->SelectAll($sql);
}

/**********************************************************************
 *	Update Functions
 **********************************************************************/
function ResetUserAaccount($userid, $pwd, $salt)
{
	$sid = $this->GetSessionID();
	$sql = "call ResetUserAaccount($sid, $userid, '$pwd', '$salt')";
	return $this->ExecuteProc ($sql);
}

function ResetMyAaccount($pwd, $salt)
{
	$sid = $this->GetSessionID();
	$sql = "call ResetMyAaccount($sid, '$pwd', '$salt')";
	return $this->ExecuteProc ($sql);
}

function DeactivateUserAccount($userid)
{
	$sid = $this->GetSessionID();
	$sql = "call DeactivateUserAccount($sid, $userid)";
	return $this->ExecuteProc ($sql);
}

function CreateUser($orgid, $status, $first, $last, $ini, $email)
{
	$sid = $this->GetSessionID();
	$first = $this->MakeStringValue($first);
	$last = $this->MakeStringValue($last);
	$ini = $this->MakeStringValue($ini);
	$email = $this->MakeStringValue($email);
	$phone = $this->MakeStringValue($phone);
	$status = $this->MakeNumericValue($status);
	$pay = $this->MakeNumericValue($pay);
	$sql = "call CreateUser($sid, $orgid, $status, $first, $last, $ini, $email)";
	return $this->ExecuteProc ($sql);
}

function UpdateUser($userid, $orgid, $status, $first, $last, $ini, $email)
{
	$sid = $this->GetSessionID();
	$first = $this->MakeStringValue($first);
	$last = $this->MakeStringValue($last);
	$ini = $this->MakeStringValue($ini);
	$status = $this->MakeNumericValue($status);
	$sql = "call UpdateUser($sid, $userid, $orgid, $status, $first, $last, $ini, '$email')";
	return $this->ExecuteProc ($sql);
}

function UpdateMyUser($first, $last, $ini, $email)
{
	$sid = $this->GetSessionID();
	$first = $this->MakeStringValue($first);
	$last = $this->MakeStringValue($last);
	$ini = $this->MakeStringValue($ini);
	$status = $this->MakeNumericValue($status);
	$sql = "call UpdateMyUser($sid, $first, $last, $ini, '$email')";
	return $this->ExecuteProc ($sql);
}

function UpdateUserRate($userid, $status, $pay)
{
	$sid = $this->GetSessionID();
	$pay = $this->MakeNumericValue($pay);
	$sql = "call UpdateUserRate($sid, $userid, $pay)";
	return $this->ExecuteProc ($sql);
}

function UpdateUserField($userid, $field, $val)
{
	$sid = $this->GetSessionID();
	$val = $this->MakeStringValue($val);
	$field = $this->MakeNumericValue($field);
	$sql = "call UpdateUserField($sid, $userid, $field, $val)";
	return $this->ExecuteProc ($sql);
}

function SetGlobalRights($userid, $super, $usermaint)
{
	$sid = $this->GetSessionID();
	$super = $this->MakeNumericBoolValue($super);
	$usermaint = $this->MakeNumericBoolValue($usermaint);
	$sql = "call SetGlobalRights($sid, $userid, $super, $usermaint)";
	return $this->ExecuteProc ($sql);
}

function AddUserToProject($userid, $prj)
{
	$sid = $this->GetSessionID();
	$sql = "call AddUserToProject($sid, $userid, $prj)";
	return $this->ExecuteProc ($sql);
}

function RemoveUserFromProject($userid, $prj)
{
	$sid = $this->GetSessionID();
	$sql = "call RemoveUserFromProject($sid, $userid, $prj)";
	return $this->ExecuteProc ($sql);
}

function UpdateProjectUser($userid, $prj, $submit, $appr, $assn, $assnto, $edit, $release)
{
	$sid = $this->GetSessionID();
	$submit = $this->MakeNumericBoolValue($submit);
	$appr = $this->MakeNumericBoolValue($appr);
	$assn = $this->MakeNumericBoolValue($assn);
	$assnto = $this->MakeNumericBoolValue($assnto);
	$edit = $this->MakeNumericBoolValue($edit);
	$release = $this->MakeNumericBoolValue($release);

	$sql = "call UpdateProjectUser($sid, $userid, $prj, $submit, $appr, $assn, $assnto, $edit, $release)";
	return $this->ExecuteProc ($sql);
}

function ProjectSuperUser($userid, $prj)
{
	$sid = $this->GetSessionID();
	$sql = "call ProjectSuperUser($sid, $userid, $prj)";
	return $this->ExecuteProc ($sql);
}

function MyPreferences($defuser, $namemode, $notifynew, $notifydone, $notifyappr, $notifyrej, $notifymsg)
{
	$sid = $this->GetSessionID();
	$defuser = $this->MakeNumericValue($defuser);
	$namemode = $this->MakeStringValue($namemode);
	$notifynew = $this->MakeStringValue($notifynew);
	$notifydone = $this->MakeStringValue($notifydone);
	$notifyappr = $this->MakeStringValue($notifyappr);
	$notifyrej = $this->MakeStringValue($notifyrej);
	$notifymsg = $this->MakeStringValue($notifymsg);
	$sql = "call MyPreferences($sid, $defuser, $namemode, $notifynew, $notifydone, $notifyappr, $notifyrej, $notifymsg)";
	return $this->ExecuteProc ($sql);
}

}
?>