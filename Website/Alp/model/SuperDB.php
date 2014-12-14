<?php
class SuperDB extends DatabaseDB {

function SuperDB($framework)
{
	$this->DatabaseDB($framework);
	$this->ValidateUserSession($framework->Cookie());
}

/**********************************************************************
 *	Query Functions
 **********************************************************************/

function ReadSubscription()
{
	$sql = 'select name, timetracking, milestones, organizations, contractors from subscription limit 1';
	return $this->SelectRow($sql);
}

function ListUserStatuses()
{
	$sql = 'select statusid, paytype, name from userstatus';
	return $this->SelectAll($sql);
}

function ListTaskStatuses()
{
	$sql = 'select statusid, hold, name from taskstatus';
	return $this->SelectAll($sql);
}

function ReadTaskStatus($id)
{
	$sql = 'select statusid, hold, name from taskstatus where statusid='.$id;
	return $this->SelectRow($sql);
}

function ReadUserType($id)
{
	$sql = 'select statusid, paytype, name from userstatus where statusid='.$id;
	return $this->SelectRow($sql);
}

function CountTasksWithStatus($id)
{
	$sql = 'select count(*) from tasks where status='.$id;
	return $this->Select($sql);
}

function CountUsersWithType($id)
{
	$sql = 'select count(*) from users where status='.$id;
	return $this->Select($sql);
}

function ReadOldestTime()
{
	$sql = 'select min(starton) from usertime';
	return $this->Select($sql);
}

function ReadOldTime($date)
{
	$date = $this->MakeDateValue($date);
	$sql = 'select count(*) from usertime where endon<' . $date;
	return $this->Select($sql);
}

/**********************************************************************
 *	Update Functions
 **********************************************************************/
function UpdateSubscription($name, $time, $ms, $orgs, $fixed)
{
	$sid = $this->GetSessionID();
	$time = $this->MakeNumericBoolValue($time);
	$ms = $this->MakeNumericBoolValue($ms);
	$orgs = $this->MakeNumericBoolValue($orgs);
	$fixed = $this->MakeNumericBoolValue($fixed);
	$name = $this->MakeStringValue($name);
	$sql = "call UpdateSubscription($sid, $name, $time, $ms, $orgs, $fixed)";
	return $this->ExecuteProc ($sql);
}

function SetDefaultTaskStatus($id)
{
	$sid = $this->GetSessionID();
	$id = $this->MakeNumericValue($id);
	$sql = "call SetDefaultTaskStatus($sid, $id)";
	$err = $this->ExecuteProc ($sql);
	if (!$err)
		$this->SetTaskStatus($id);
	return $err;
}

function CreateTaskStatus($hold, $name)
{
	$sid = $this->GetSessionID();
	$hold = $this->MakeNumericBoolValue($hold);
	$name = $this->MakeStringValue($name);
	$sql = "call CreateTaskStatus($sid, $hold, $name)";
	return $this->ExecuteProc ($sql);
}

function UpdateTaskStatus($id, $hold, $name)
{
	$sid = $this->GetSessionID();
	$hold = $this->MakeNumericBoolValue($hold);
	$name = $this->MakeStringValue($name);
	$sql = "call UpdateTaskStatus($sid, $id, $hold, $name)";
	return $this->ExecuteProc ($sql);
}

function DeleteTaskStatus($delid, $newid)
{
	$sid = $this->GetSessionID();
	$newid = $this->MakeNumericValue($newid);
	$sql = "call DeleteTaskStatus($sid, $delid, $newid)";
	return $this->ExecuteProc ($sql);
}

function CreateUserStatus($id, $pay, $name)
{
	$sid = $this->GetSessionID();
	$pay = $this->MakeStringValue($pay);
	$name = $this->MakeStringValue($name);
	$sql = "call CreateUserStatus($sid, $pay, $name)";
	return $this->ExecuteProc ($sql);
}

function UpdateUserStatus($id, $pay, $name)
{
	$sid = $this->GetSessionID();
	$pay = $this->MakeStringValue($pay);
	$name = $this->MakeStringValue($name);
	$sql = "call UpdateUserStatus($sid, $id, $pay, $name)";
	return $this->ExecuteProc ($sql);
}

function DeleteUserStatus($delid)
{
	$sid = $this->GetSessionID();
	$newid = $this->MakeNumericValue($newid);
	$sql = "call DeleteUserStatus($sid, $delid)";
	return $this->ExecuteProc ($sql);
}

function PurgeTime($date)
{
	$sid = $this->GetSessionID();
	$date = $this->MakeDateValue($date);
	$sql = "call PurgeTime($sid, $date)";
	return $this->ExecuteProc ($sql);
}

}
?>