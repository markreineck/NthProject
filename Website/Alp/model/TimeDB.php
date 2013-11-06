<?php
class TimeDB extends DatabaseDB {

function TimeDB($framework)
{
	$this->DatabaseDB($framework);
//	$this->ValidateUserSession($framework->Cookie());
}

/**********************************************************************
 *	Query Functions
 **********************************************************************/
function ReadUserProjectList()
{
	if ($this->IsSupervisor())
		return $this->ReadProjectList();

	$sql = 'select p.prjid, p.name from projects p, projectusers u where p.prjid=u.prjid and u.userid='.$this->GetUserID().' order by p.name';
	return $this->SelectAll($sql, 2);
}

function ReadSalt ()
{
	$sql = 'select u.salt from users u, usersession s where s.userid=u.userid and s.sessionid='.$this->GetSessionID();
	return $this->Select($sql);
}

function ReadTaskProject($taskid)
{
	$sql = "select a.prjid from tasks t inner join projectareas a on t.areaid=a.areaid where t.taskid=".$taskid;
	return $this->Select($sql);
}

/**********************************************************************
 *	Update Functions
 **********************************************************************/
function RecordTime($userid, $prjid, $start, $end, $note)
{
	$sid = $this->GetSessionID();
	$note = $this->MakeStringValue($note);
	$start2 = $this->MakeDateTimeValue($start);
	$end2 = $this->MakeDateTimeValue($end);
	$sql = "call RecordTime($sid, $userid, $prjid, $start2, $end2, $note)";
	return $this->ExecuteProc ($sql);
}

function ClockInToProject($prjid)
{
	$sid = $this->GetSessionID();
	$sql = "call ClockInToProject($sid, $prjid)";
	return $this->ExecuteProc ($sql);
}

function ClockInToTask($task)
{
	$sid = $this->GetSessionID();
	$sql = "call ClockInToTask($sid, $task)";
	return $this->ExecuteProc ($sql);
}

function ClockOut($pwd, $comment)
{
	$sid = $this->GetSessionID();
	$pwd = $this->MakeStringValue($pwd);
	$comment = $this->MakeStringValue($comment);
	$sql = "call ClockOut($sid, $pwd, $comment)";
	return $this->ExecuteProc ($sql);
}

function AdjustTime($timeid, $adjust)
{
	$sid = $this->GetSessionID();
	$sql = "call AdjustTime($sid, $timeid, $adjust)";
	return $this->ExecuteProc ($sql);
}

function AdjustTimeProject($timeid, $prjid)
{
	$sid = $this->GetSessionID();
	$sql = "call AdjustTimeProject($sid, $timeid, $prjid)";
	return $this->ExecuteProc ($sql);
}

function AdminClockOut($timeid, $hours, $min)
{
	$sid = $this->GetSessionID();
	if ($hours < 1) $hours = '0';
	$min *= 6;
	if ($min < 1)
		$min = '00';
	else if ($min > 60)
		$min = '00';
	else if ($min < 10)
		$min = '0'.$min;

	$sql = "call AdminClockOut($sid, $timeid, '$hours:$min:00')";
	return $this->ExecuteProc ($sql);
}

}
?>