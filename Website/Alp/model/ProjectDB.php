<?php
class ProjectDB extends DatabaseDB {

function ProjectDB($framework)
{
	$this->DatabaseDB($framework);
}

/**********************************************************************
 *	Query Functions
 **********************************************************************/
function ListProjects($org, $status, $mode=0)
{
	$sql = 
"SELECT p.prjid, p.orgid, p.priority, DATE_FORMAT(p.started, '%b %e, %Y') started, 
DATE_FORMAT(p.targetdate, '%b %e, %Y') targetdate, DATE_FORMAT(p.completed, '%b %e, %Y') 
completed, p.name, p.status, o.name orgname
FROM projects p, organizations o where p.orgid=o.orgid ";
			
	if ($org > 0)
		$sql .= " AND o.orgid=".$org;

	if ($status == 'A')
		$sql .= " and p.status='A' and p.completed is null";
	elseif ($status == 'I')
		$sql .= " and p.status='I'";
	elseif ($status == 'C')
		$sql .= " and p.completed is not null";
	$sql .= " order by p.name";

	return $this->SelectAll($sql, $mode);
}

function ListMyProjects($org, $status, $mode=0)
{
	$sid = $this->GetSessionID();
	$sql = 
"SELECT p.prjid, p.orgid, p.priority, DATE_FORMAT(p.started, '%b %e, %Y') started, 
DATE_FORMAT(p.targetdate, '%b %e, %Y') targetdate, DATE_FORMAT(p.completed, '%b %e, %Y') 
completed, p.name, p.status, o.name orgname
FROM projects p, organizations o, projectusers u, usersession s
where p.orgid=o.orgid and p.prjid=u.prjid and u.userid=s.userid and s.sessionid=$sid and u.superuser is not null";
			
	if ($org > 0)
		$sql .= " AND o.orgid=".$org;

	if ($status == 'A')
		$sql .= " and p.status='A' and p.completed is null";
	elseif ($status == 'I')
		$sql .= " and p.status='I'";
	elseif ($status == 'C')
		$sql .= " and p.completed is not null";
	$sql .= " order by p.name";

	return $this->SelectAll($sql, $mode);
}

function ListProjectTimeCosts($project)
{
	$collist = "t.userid, 
time_to_sec(timediff(ifnull(t.endon,now()),t.starton))/3600-ifnull(t.adjustment,0) elapsetime";

	$sql = "select t.elapsetime, t.userid, u.name, u.payrate/100 payrate, t.elapsetime*u.payrate/100 pay 
from (
select round(sum(t.elapsetime),1) elapsetime, t.userid from
(select $collist
from usertime t
where t.prjid=$project
union all
select $collist
from usertime t, tasks k, projectareas a
where t.prjid is null and t.taskid=k.taskid and k.areaid=a.areaid and a.prjid=$project
) t
group by t.userid) t, usernames u
where t.userid=u.userid
group by t.userid";

	return $this->SelectAll($sql);
}

function ListProjectTaskCosts($project)
{
	$sql = "select t.assignedto, t.taskcnt, t.cost, u.name
from (
select t.assignedto, sum(t.cost) cost, count(*) taskcnt
from tasks t
inner join projectareas a on a.areaid=t.areaid
where t.removed is null and t.cost is not null and a.prjid=$project
group by t.assignedto
) t, usernames u
where t.assignedto=u.userid
";
	return $this->SelectAll($sql);
}

function ListProjectDefaults($org=0)
{
	$sql = 
'select p.prjid, p.name, p.defpriority, p.defassignedto, p.defapprovedby
from projects p ';

	if ($org > 0)
		$sql .= 'where p.orgid='.$org;
	else
		$sql .= ', organizations o where o.orgid=p.orgid';

	$sql .= " and p.completed is null and p.status='A' order by p.name";

	return $this->SelectAll($sql);
}

function CountProjectAreaPaidTasks($areaid)
{
	$sql = 'select count(*) from tasks where cost is not null and (paid is not null or cost is not null) and areaid='.$areaid;
	return $this->Select($sql);
}

function CountProjectAreaTasks($areaid)
{
	$sql = 'select count(*) from tasks where areaid='.$areaid;
	return $this->Select($sql);
}

function CountProjectAreaActiveTasks($areaid)
{
	$sql = 'select count(*) from tasks where complete is null and areaid='.$areaid;
	return $this->Select($sql);
}

function CountProjectAreaCompleteTasks($areaid)
{
	$sql = 'select count(*) from tasks where complete is not null and areaid='.$areaid;
	return $this->Select($sql);
}

function ReadAllProjectAreas()
{
	$sql = "select a.areaid, a.prjid, a.name, a.price, a.completed, a.due 
from projectareas a order by name";
	return $this->SelectAll($sql, 2);
}

function ReadProjectAreas($prjid)
{
	$sql = "select a.areaid, a.name, u.name responsible, a.price, DATE_FORMAT(a.completed, '%b %e, %Y') completed, DATE_FORMAT(a.due, '%b %e, %Y') due 
from projectareas a
left outer join usernames u on a.responsible=u.userid
where prjid=$prjid order by name";
	return $this->SelectAll($sql);
}

function ListMilestones($prjid)
{
	$sql = "select prjid, milestoneid, name, DATE_FORMAT(completion, '%b %e, %Y') completedon, DATE_FORMAT(targetdate, '%b %e, %Y') target 
from milestones
where prjid=$prjid order by ifnull(completion, targetdate)";
	return $this->SelectAll($sql);
}

function GetProjectStatusList()
{
	return array(
		array('A','Active'),
		array('I','Inactive')
	);
}

function ReadProject($id)
{
	$sql = "select orgid, name, timerpt, priority, status, defpriority, defpriority, defapprovedby,
date_format(started,'%b %e, %Y') started, date_format(targetdate,'%b %e, %Y') targetdate, date_format(completed,'%b %e, %Y') completed, notes from projects where prjid=" . $id;
 	return $this->SelectRow($sql);
}

function ReadProjectArea($id)
{
	$sql = "select prjid, name, responsible, date_format(due,'%b %e, %Y') targetdate, price, date_format(completed,'%b %e, %Y') completed, date_format(paid,'%b %e, %Y') paid from projectareas where areaid=" . $id;

	return $this->SelectRow($sql);
}

function ReadMilestone($id)
{
	$sql = "select prjid, name, date_format(targetdate,'%b %e, %Y') targetdate, date_format(completion,'%b %e, %Y') completion, descr from milestones where milestoneid=" . $id;

	return $this->SelectRow($sql);
}

function ReadProjectName($id)
{
	$sql = "select name from projects where prjid=" . $id;

	return $this->Select($sql);
}

function ReadProjectLinks($id)
{
	$sql = "select linkid, linkname, url from projectlinks where prjid=" . $id;

	return $this->SelectAll($sql);
}

function IsProjectSuperUser($pid)
{
	if ($this->IsGlobalSupervisor())
		return true;

	$super = $this->IsSupervisor();
	if ($super) {
		$sql = "select count(*) from projects where prjid=$id and orgid=$super";
		if ($this->Select($sql) > 0)
			return true;
	}

	$sid = $this->GetSessionID();
	$sql = "select u.superuser from projectusers u, usersession s where prjid=$pid and s.sessionid=$sid and s.userid=u.userid and s.expireson>now()";

	return $this->Select($sql);
}

/**********************************************************************
 *	Update Functions
 **********************************************************************/
function CreateProject($orgid, $name, $start, $target, $priority, $status, $timerpt, $notes)
{
	$sid = $this->GetSessionID();
	$name = $this->MakeStringValue($name);
	$status = $this->MakeStringValue($status);
	$start = $this->MakeDateValue($start);
	$target = $this->MakeDateValue($target);
	$priority = $this->MakeNumericValue($priority);
	$timerpt = $this->MakeNumericValue($timerpt);	
	$notes = $this->MakeStringValue($notes);	
	$sql = "call CreateProject($sid, $orgid, $name, $start, $target, $priority, $status, $timerpt, $notes)";
	return $this->ExecuteProc ($sql);
}

function UpdateProject($prjid, $orgid, $name, $priority, $status, $timerpt, $notes)
{
	$sid = $this->GetSessionID();
	$name = $this->MakeStringValue($name);
	$status = $this->MakeStringValue($status);
	$priority = $this->MakeNumericValue($priority);
	$timerpt = $this->MakeNumericValue($timerpt);
	$notes = $this->MakeStringValue($notes, true);
	$sql = "call UpdateProject($sid, $prjid, $orgid, $name, $priority, $status, $timerpt, $notes)";
	return $this->ExecuteProc ($sql);
}

function UpdateProjectDates($prjid, $start, $target, $comp)
{
	$sid = $this->GetSessionID();
	$start = $this->MakeDateValue($start);
	$target = $this->MakeDateValue($target);
	$comp = $this->MakeDateValue($comp);
	$sql = "call UpdateProjectDates($sid, $prjid, $start, $target, $comp)";
	return $this->ExecuteProc ($sql);
}

function DeleteProject($prjid)
{
	$sid = $this->GetSessionID();
	$sql = "call DeleteProject($sid, $prjid)";
	return $this->ExecuteProc ($sql);
}

function CreateProjectArea($prjid, $resp, $name, $target, $price)
{
	$sid = $this->GetSessionID();
	$name = $this->MakeStringValue($name);
	$target = $this->MakeDateValue($target);
	$resp = $this->MakeNumericValue($resp);
	$price = $this->MakeNumericValue($price);
	$sql = "call CreateProjectArea($sid, $prjid, $resp, $name, $target, $price)";
	return $this->ExecuteProc ($sql);
}

function UpdateProjectArea($areaid, $resp, $name, $target, $price)
{
	$sid = $this->GetSessionID();
	$name = $this->MakeStringValue($name);
	$target = $this->MakeDateValue($target);
	$resp = $this->MakeNumericValue($resp);
	$price = $this->MakeNumericValue($price);
	$sql = "call UpdateProjectArea($sid, $areaid, $resp, $name, $target, $price)";
	return $this->ExecuteProc ($sql);
}

function MergeProjectAreas($srcarea, $destarea)
{
	$sid = $this->GetSessionID();
	$sql = "call MergeProjectAreas($sid, $srcarea, $destarea)";
	return $this->ExecuteProc ($sql);
}

function DeleteProjectArea($areaid)
{
	$sid = $this->GetSessionID();
	$sql = "call DeleteProjectArea($sid, $areaid)";
	return $this->ExecuteProc ($sql);
}

function CreateMilestone($prjid, $name, $target, $descr)
{
	$sid = $this->GetSessionID();
	$name = $this->MakeStringValue($name);
	$target = $this->MakeDateValue($target);
	$descr = $this->MakeStringValue($descr, true);	
	$sql = "call CreateMilestone($sid, $prjid, $name, $target, $descr)";
	return $this->ExecuteProc ($sql);
}

function UpdateMilestone($msid, $name, $target, $comp, $descr)
{
	$sid = $this->GetSessionID();
	$name = $this->MakeStringValue($name);
	$target = $this->MakeDateValue($target);
	$comp = $this->MakeDateValue($comp);
	$descr = $this->MakeStringValue($descr, true);
	$sql = "call UpdateMilestone($sid, $msid, $name, $target, $comp, $descr)";
	return $this->ExecuteProc ($sql);
}

function CompleteMilestone($msid)
{
	$sid = $this->GetSessionID();
	$sql = "call MilestoneComplete($sid, $msid, curdate())";
	return $this->ExecuteProc ($sql);
}

function UpdateProjectDefaults($prjid, $priority, $assnto, $apprby)
{
	$sid = $this->GetSessionID();
	$priority = $this->MakeNumericValue($priority);
	$assnto = $this->MakeNumericValue($assnto);
	$apprby = $this->MakeNumericValue($apprby);
	$sql = "call UpdateProjectDefaults($sid, $prjid, $priority, $assnto, $apprby)";
	return $this->ExecuteProc ($sql);
}

function CreateProjectLinks($prjid, $linkname, $linkurl)
{
	$sid = $this->GetSessionID();
	$linkname = $this->MakeStringValue($linkname);
	$linkurl = $this->MakeStringValue($linkurl);
	$sql = "call AddProjectLink($sid, $prjid, $linkname, $linkurl)";
	return $this->ExecuteProc ($sql);
}

function UpdateProjectLinks($linkid, $linkname, $linkurl)
{
	$sid = $this->GetSessionID();
	$linkname = $this->MakeStringValue($linkname);
	$linkurl = $this->MakeStringValue($linkurl);
	$sql = "call UpdateProjectLink($sid, $linkid, $linkname, $linkurl)";
	return $this->ExecuteProc ($sql);
}
}
?>