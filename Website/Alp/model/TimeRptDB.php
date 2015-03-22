<?php
class TimeRptDB extends TimeDB {

function TimeRptDB($framework)
{
	$this->TimeDB($framework);
}

/**********************************************************************
 *	Query Functions
 **********************************************************************/
function ReadMyTime($cookie)
{
	$start = $this->MakeDateValue($cookie->GetDefaultStartDate());
	$end = $cookie->GetDefaultEndDate();
	$project = $cookie->GetDefaultProject();

	$where = "t.userid=u.userid and starton>=$start and t.userid=" . $this->GetUserID();
	if ($end)
		$where .= ' and endon<' . $this->MakeNextDayValue($end);

	if ($project > 0)
		$where .= " and p.prjid=$project";

	$collist = "t.timeid, u.name, t.adjustment, t.reason, p.prjid, p.name project, t.userid, t.starton,
date_format(t.starton,'%b %e') startdate,
date_format(t.starton,'%H:%i') starttime,
date_format(t.endon,'%H:%i') endtime,
round(time_to_sec(timediff(ifnull(t.endon,now()),t.starton))/3600,1) elapsetime";

	$sql = "select $collist, t.comment
from usertime t, projects p, usernames u
where t.prjid=p.prjid and t.taskid is null and $where
union
select $collist, ifnull(k.name,t.comment)
from usertime t, tasks k, projectareas a, projects p, usernames u
where t.taskid=k.taskid and k.areaid=a.areaid and a.prjid=p.prjid and $where
order by starton";

	return $this->SelectAll($sql);
}

function ReadTimeData($cookie)
{
	$start = $this->MakeDateValue($cookie->GetDefaultStartDate());
	$end = $cookie->GetDefaultEndDate();
	$project = $cookie->GetDefaultProject();
	$staff = $cookie->GetDefaultUser();
	
	$where = "t.userid=u.userid and starton>=$start";
	if ($end)
		$where .= ' and endon<' . $this->MakeNextDayValue($end);

	if ($project > 0)
		$where .= " and t.prjid=$project";
	if ($staff > 0)
		$where .= " and t.userid=$staff";

	$collist = "t.timeid, u.name, t.adjustment, t.starton, t.reason, t.comment, p.prjid, p.name project, t.userid, 
date_format(t.starton,'%b %e') startdate,
date_format(t.starton,'%H:%i') starttime,
date_format(t.endon,'%H:%i') endtime,
round(time_to_sec(timediff(ifnull(t.endon,now()),t.starton))/3600,1) elapsetime";

	$sql = "select $collist
from usertime t, projects p, usernames u
where t.prjid=p.prjid and $where
union
select $collist
from usertime t, tasks k, projectareas a, projects p, usernames u
where t.prjid is null and t.taskid=k.taskid and k.areaid=a.areaid and a.prjid=p.prjid and $where
order by starton";

	return $this->SelectAll($sql);
}

function ReadTimeExport($cookie)
{
	$start = $this->MakeDateValue($cookie->GetDefaultStartDate());
	$end = $cookie->GetDefaultEndDate();
	$project = $cookie->GetDefaultProject();
	$staff = $cookie->GetDefaultUser();
	
	$where = "t.userid=u.userid and starton>=$start";
	if ($end)
		$where .= ' and endon<' . $this->MakeNextDayValue($end);

	if ($project > 0)
		$where .= " and p.prjid=$project";
	if ($staff > 0)
		$where .= " and t.userid=$staff";

	$collist = "t.timeid, u.name, t.adjustment, t.starton, t.reason, t.comment, p.prjid, p.name project, t.userid, 
date_format(t.starton,'%b %e') startdate,
date_format(t.starton,'%H:%i') starttime,
date_format(t.endon,'%H:%i') endtime,
round(time_to_sec(timediff(ifnull(t.endon,now()),t.starton))/3600,1) elapsetime";

	$sql = "select $collist, k.name task
from usertime t, tasks k, projectareas a, projects p, usernames u
where t.prjid is null and t.taskid=k.taskid and k.areaid=a.areaid and a.prjid=p.prjid and $where
union all
select $collist, null
from usertime t, projects p, usernames u
where t.prjid=p.prjid and t.taskid is null and $where
order by name, project, starton";

	return $this->SelectAll($sql);
}

function ReadSignedInUsers()
{
	$sql = "
select t.timeid, t.userid, u.name username, p.name projectname, t.taskid, x.name task,
round(time_to_sec(timediff(now(), t.starton))/3600,1) hours,
round(time_to_sec(timediff(now(), t.starton))/60) minutes,
DATE_FORMAT(t.starton, '%b %e %H:%i') starttime 
from usertime t
inner join usernames u on t.userid=u.userid
inner join tasks x on t.taskid=x.taskid
inner join projectareas a on x.areaid=a.areaid
left outer join projects p on p.prjid=a.prjid
where t.endon is null
union all
select t.timeid, t.userid, u.name username, p.name projectname, null, null,
round(time_to_sec(timediff(now(), t.starton))/3600,1) hours,
round(time_to_sec(timediff(now(), t.starton))/60) minutes,
DATE_FORMAT(t.starton, '%b %e %H:%i') starttime 
from usertime t
inner join usernames u on t.userid=u.userid
inner join projects p on p.prjid=t.prjid
where t.endon is null";

	return $this->SelectAll($sql);
}

function ReadPayroll($start, $end)
{
	$start = $this->MakeDateValue($start);
	$end = $this->MakeNextDayValue($end);
	
	$where = "starton>=$start and endon<$end";

	$sql = "select u.name, t.time, u.payrate, t.time*u.payrate pay
from usernames u, (
select userid, sum(round(time_to_sec(timediff(ifnull(t.endon,now()),t.starton))/3600,1)) time
from usertime t where $where group by userid) t
where t.userid=u.userid
order by u.name";

	return $this->SelectAll($sql);
}

}
?>