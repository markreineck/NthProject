<?php

class TaskDB extends DatabaseDB {

function TaskDB($framework)
{
	$this->DatabaseDB($framework);
//	$this->ValidateUserSession($framework->Cookie());
}

/**********************************************************************
 *	Query Functions
 **********************************************************************/
function GetCompanyList()
{
	$sql = "select o.orgid, o.name, s.name status, ifnull(u.cnt,0) ucnt, ifnull(p.cnt,0) pcnt
from organizations o
left outer join (select orgid, count(orgid) cnt from projects group by orgid) p on o.orgid=p.orgid
left outer join (select orgid, count(orgid) cnt from users group by orgid) u on o.orgid=u.orgid
left outer join orgstatus s on o.status=s.statusid
group by o.orgid order by o.name";
	return $this->SelectAll($sql);
}

function ReadProjectDefaults($prjid)
{
	$sql = 
'select prjid, name, defpriority, defassignedto, defapprovedby
from projects where prjid=' . $prjid;
	return $this->SelectRow($sql);
}

function ReadSubmitProjects()
{
	if ($this->IsSupervisor())
		return $this->ReadProjectList();

	$sid = $this->GetSessionID();
	$sql = "select p.prjid, p.name
from projects p, projectusers u, usersession s
where p.status='A' and p.completed is null and p.prjid=u.prjid and u.userid=s.userid and s.sessionid=$sid
order by p.name";
	return $this->SelectAll($sql, 2);
}

function ReadTaskUnix($taskid)
{
	$sql = 'select taskid, areaid, status, edited, editedby, priority, 
UNIX_TIMESTAMP(submittedon) submittedon, UNIX_TIMESTAMP(removed) removed, UNIX_TIMESTAMP(released) released,
UNIX_TIMESTAMP(startafter) startafter, UNIX_TIMESTAMP(needby) needby, UNIX_TIMESTAMP(complete) complete, 
UNIX_TIMESTAMP(approved) approved, startmilestone, endmilestone, 
removedby, assignedto, submittedby, approvedby, releasedby, name, branch
from tasks where taskid='.$taskid;
	return $this->SelectRow($sql);
}

function ReadTask($taskid)
{
	$sql = 'select taskid, areaid, status, edited, editedby, priority, submittedon, removed, released,
startafter, needby, complete, approved, startmilestone, endmilestone, 
removedby, assignedto, submittedby, approvedby, releasedby, name, branch
from tasks where taskid='.$taskid;
	return $this->SelectRow($sql);
}

function ReadTaskDesc($taskid)
{
	$sql = "select p.orgid, p.name project, a.prjid, t.areaid, a.name area, t.priority, t.name, t.status statusid, s.name status, 
t.startmilestone, t.endmilestone, t.cost, t.removed, t.submittedby, t.assignedto, t.approvedby, t.branch,
date_format(t.submittedon,'%b %d, %Y') submittedon,
date_format(t.complete,'%b %d, %Y') complete,
date_format(t.approved,'%b %d, %Y') approved,
date_format(t.needby,'%b %d, %Y') needby,
t.startafter, sm.name startmsname, em.name endmsname,
t.assignedto, at.name assignedname, at.email assignedemail, 
t.submittedby, su.name submittedname, su.email submittedemail, 
t.approvedby, ap.name approvedname, ap.email approvedemail
from tasks t
inner join projectareas a on t.areaid=a.areaid
inner join projects p on p.prjid=a.prjid
left outer join taskstatus s on t.status=s.statusid
left outer join milestones sm on t.startmilestone=sm.milestoneid
left outer join milestones em on t.endmilestone=em.milestoneid
left outer join usernames at on t.assignedto=at.userid
left outer join usernames su on t.submittedby=su.userid
left outer join usernames ap on t.approvedby=ap.userid
where t.taskid=".$taskid;
	return $this->SelectRow($sql);
}

function ReadTaskNotes($taskid)
{
	$sql = "select n.noteid, n.fromid, f.name fromname, f.email, toid, t.name toname, n.message,
date_format(n.senton,'%b %d, %Y %H:%i') sent
from tasknotes n
left outer join usernames f on n.fromid=f.userid
left outer join taskmessages m on n.noteid=m.noteid
left outer join usernames t on m.toid=t.userid
where taskid=$taskid and n.message is not null order by n.senton";
	return $this->SelectAll($sql);
}

function ReadTaskComments($taskid)
{
	$sql = "select taskid, noteid, fromid, msgtype, UNIX_TIMESTAMP(senton) senton, subject, message
from tasknotes n where taskid=$taskid";
	return $this->SelectAll($sql);
}

function ReadTaskUsers($taskid)
{
	$sql = "select 
t.assignedto, at.name assignedname, at.email assignedemail, 
t.submittedby, su.name submittedname, su.email submittedemail, 
t.approvedby, ap.name approvedname, ap.email approvedemail
from tasks t
left outer join usernames at on t.assignedto=at.userid
left outer join usernames su on t.submittedby=su.userid
left outer join usernames ap on t.approvedby=ap.userid
where taskid=$taskid";
	return $this->SelectRow($sql);
}

function ReadTaskFiles($taskid)
{
	$sql = "select f.fileid, f.uploadedon, u.name loadedby, f.descr from taskfiles f, usernames u where taskid=$taskid and f.uploadedby=u.userid order by uploadedon";
	return $this->SelectAll($sql);
}

function ReadAreaProject($aid)
{
	$sql = "select prjid from projectareas where areaid=$aid";
	return $this->Select($sql);
}

function ReadMyProjectRights($pid)
{
	$sql = "select superuser, approval, assigned, assign, edit from projectusers where prjid=$pid and userid=".$this->GetUserID();
	return $this->SelectRow($sql);
}

function ListAreaTasks($area)
{
	$sql = "select t.taskid, t.areaid, t.priority, t.status, t.name, t.startmilestone, t.endmilestone, t.assignedto, t.needby
from tasks t
where t.areaid=$area and t.complete is null and t.removed is null order by t.priority";
	return $this->SelectAll($sql);
}

function ListProjectTasks($prjid)
{
	$sql = "select t.taskid, t.areaid, t.priority, t.status, t.name, t.startmilestone, t.endmilestone, t.assignedto, t.needby
from tasks t
inner join projectareas a on a.areaid=t.areaid
where a.prjid=$prjid and t.complete is null and t.removed is null order by t.priority";
	return $this->SelectAll($sql);
}

function TaskSearch($prjid, $assn, $milestone, $priority)
{
	$sql = "select t.taskid, t.areaid, t.priority, t.status, t.name, t.startmilestone, t.endmilestone, t.assignedto, t.needby
from tasks t
inner join projectareas a on a.areaid=t.areaid
where t.complete is null and t.removed is null';
	if ($prjid)
		$sql .= ' and a.prjid=' . $prjid;
	if ($assn)
		$sql .= ' and t.assignedto=' . $assn;
	if ($milestone)
		$sql .= ' and t.endmilestone=' . $milestone;
	if ($priority)
		$sql .= ' and t.priority=' . $priority;
	$sql .= ' order by t.priority";
	return $this->SelectAll($sql);
}

function ListProjectMilestones($prjid)
{
	$sql = "select milestoneid, name from milestones where prjid=$prjid and completion is null order by name";
	return $this->SelectAll($sql, 2);
}

function ReadProjectAndArea($areaid)
{
	$sql = "select p.name project, a.prjid, a.name area
from projectareas a
inner join projects p on p.prjid=a.prjid
where a.areaid=".$areaid;
	return $this->SelectRow($sql);
}

function ReadProjectName($prjid)
{
	$sql = "select name from projects where prjid=".$prjid;
	return $this->Select($sql);
}

/**********************************************************************
 *	Task Selection List Functions
 **********************************************************************/
function GetProjectPrivList($prjid, $priv)
{
	$sql = 'select p.orgid from projects p where p.prjid='.$prjid;
	$porg = $this->Select($sql);
	$sorg = $this->GetOwnerCompanyID();
	$swhere = ($porg == $sorg) ? 'orgid='.$porg
		: "(orgid=$porg or orgid=$sorg)";

	$sql = "select u.userid, u.name from usernames u, (
select userid from projectusers
where prjid=$prjid and ($priv is not null or superuser is not null)
union
select userid from users where $swhere and superuser is not null
) x
where u.userid=x.userid and u.status is not null and u.hasaccount is not null order by u.lastname, u.firstname";
	return $this->SelectAll($sql, 2);
}

function GetSubmittedByList($prjid)
{
	return $this->GetProjectPrivList($prjid, 'submit');
}

function GetAssignToList($prjid)
{
	return $this->GetProjectPrivList($prjid, 'assigned');
}

function GetApproveByList($prjid)
{
	return $this->GetProjectPrivList($prjid, 'approval');
}

function GetMilestoneList($prjid)
{
	$sql = "select milestoneid, name from milestones where prjid=$prjid and completion is null order by name";
	return $this->SelectAll($sql, 2);
}

function GetProjectAreaList($prjid)
{
	$sql = "select areaid, name from projectareas where prjid=$prjid order by name";
	return $this->SelectAll($sql, 2);
}

/**********************************************************************
 *	Update Functions
 **********************************************************************/
function CreateTask($prjid, $area, $status, $priority, $name, $startms, $endms, $starton, $doby, $assnto, $apprby, $descr, $cost)
{
	$sid = $this->GetSessionID();
	$prjid = $this->MakeNumericValue($prjid);
	$area = $this->MakeNumericValue($area);
	$status = $this->MakeNumericValue($status);
	$priority = $this->MakeNumericValue($priority);
	$startms = $this->MakeNumericValue($startms);
	$endms = $this->MakeNumericValue($endms);
	$assnto = $this->MakeNumericValue($assnto);
	$apprby = $this->MakeNumericValue($apprby);
	$starton = $this->MakeDateValue($starton);
	$doby = $this->MakeDateValue($doby);
	$name = $this->MakeStringValue($name);
	$descr = $this->MakeStringValue($descr);
	$cost = $this->MakeNumericValue($cost);
	$sql = "call CreateTask($sid, $prjid, $area, $status, $priority, $name, $startms, $endms, $starton, $doby, $assnto, $apprby, $descr, $cost)";

	if ($this->ExecuteProc ($sql))
		return 0;
	return $this->SelectMysqlVariable('taskid');
}

function EditTask($taskid, $area, $status, $priority, $name='', $branch='')
{
	$sid = $this->GetSessionID();
	$area = $this->MakeNumericValue($area);
	$status = $this->MakeNumericValue($status);
	$priority = $this->MakeNumericValue($priority);
	$name = $this->MakeStringValue($name);
	$branch = $this->MakeStringValue($branch);

	$sql = "call EditTask($sid, $taskid, $area, $status, $priority, $name, $branch)";

	return $this->ExecuteProc ($sql);
}

function EditTaskCost($taskid, $cost){
	$sid = $this->GetSessionID();
	$cost =  $this->MakeNumericValue($cost);
	
	$sql = "call EditTaskCost($sid, $taskid, $cost)";
	
	return $this->ExecuteProc ($sql);
}

function EditTaskDates($taskid, $starton, $doby)
{
	$sid = $this->GetSessionID();
	$starton = $this->MakeDateValue($starton);
	$doby = $this->MakeDateValue($doby);

	$sql = "call EditTaskDates($sid, $taskid, $starton, $doby)";

	return $this->ExecuteProc ($sql);
}

function EditTaskMilestones($taskid, $starton, $doby)
{
	$sid = $this->GetSessionID();
	$starton = $this->MakeNumericValue($starton);
	$doby = $this->MakeNumericValue($doby);

	$sql = "call EditTaskMilestones($sid, $taskid, $starton, $doby)";

	return $this->ExecuteProc ($sql);
}

function EditTaskAssignment($taskid, $role, $userid)
{
	$sid = $this->GetSessionID();
	$userid = $this->MakeNumericValue($userid);

	$sql = "call EditTaskAssignment($sid, $taskid, '$role', $userid)";

	return $this->ExecuteProc ($sql);
}

function AddTaskNote($taskid, $descr)
{
	$sid = $this->GetSessionID();
	if ($taskid > 0 && !empty($descr)) {
		$descr = $this->MakeStringValue($descr, true);
		$sql = "call AddTaskNote($sid, $taskid, null, null, $descr)";
		$err = $this->ExecuteProc ($sql);
	} else {
		$err = 1;
	}
	return $err;
}

function AddTaskMessage($taskid, $to, $subj, $descr)
{
	$sid = $this->GetSessionID();
	if ($taskid > 0 && !empty($descr)) {
		$to = $this->MakeNumericValue($to);
		$subj = $this->MakeStringValue($subj);
		$descr = $this->MakeStringValue($descr, true);
		$sql = "call AddTaskNote($sid, $taskid, $to, $subj, $descr)";
		$err = $this->ExecuteProc ($sql);
	} else {
		$err = 1;
	}
	return $err;
}

function EditTaskNote($taskid, $noteid, $descr)
{
	$sid = $this->GetSessionID();
	if ($taskid > 0 && $noteid > 0) {
		$html = $this->Framework()->LoadClass('HtmlText');
		$descr = $this->MakeStringValue(htmlentities($descr), true);
		$sql = "call EditTaskNote($sid, $taskid, $noteid, $descr)";
		$err = $this->ExecuteProc ($sql);
	} else {
		$err = 1;
	}
	return $err;
}

function DeleteTaskNote($taskid, $noteid)
{
	$sid = $this->GetSessionID();
	if ($taskid > 0 && $noteid > 0) {
		$sql = "call DeleteTaskNote($sid, $taskid, $noteid)";
		$err = $this->ExecuteProc ($sql);
	} else {
		$err = 1;
	}
	return $err;
}

function AddTaskFile($taskid, $name, $type, $descr, $filename)
{
	$sid = $this->GetSessionID();
	if ($taskid > 0 && !empty($filename)) {
		$name = $this->MakeStringValue($name);
		$descr = $this->MakeStringValue($descr);

		$fp = fopen($filename, 'r');
		$data = fread($fp, filesize($filename));
		$data = addslashes($data);
		fclose($fp);

		$sql = "call AddTaskFile($sid, $taskid, $name, '$type', $descr, '$data')";
		$err = $this->ExecuteProc ($sql);
	} else {
		$err = 1;
	}
	return $err;
}

function EditTaskFile($taskid, $fileid, $descr)
{
	$sid = $this->GetSessionID();
	$descr = $this->MakeStringValue($descr);
	$sql = "call EditTaskFile($sid, $taskid, $fileid, $descr)";
	return $this->ExecuteProc ($sql);
}

function DeleteTaskFile($taskid, $fileid)
{
	$sid = $this->GetSessionID();
	$descr = $this->MakeStringValue($descr);
	$sql = "call DeleteTaskFile($sid, $taskid, $fileid)";
	return $this->ExecuteProc ($sql);
}

function CompleteTask($taskid, $branch, $notes)
{
	$sid = $this->GetSessionID();
	$branch = $this->MakeStringValue($branch);
	$notes = $this->MakeStringValue($notes);
	$sql = "call CompleteTask($sid, $taskid, $branch, $notes)";
	return $this->ExecuteProc ($sql);
}

function ApproveTask($taskid)
{
	$sid = $this->GetSessionID();
	$sql = "call ApproveTask($sid, $taskid, null)";
	return $this->ExecuteProc ($sql);
}

function ReleaseTask($taskid)
{
	$sid = $this->GetSessionID();
	$sql = "call ReleaseTask($sid, $taskid)";
	return $this->ExecuteProc ($sql);
}

function UnreleaseTask($taskid)
{
	$sid = $this->GetSessionID();
	$sql = "call UnreleaseTask($sid, $taskid)";
	return $this->ExecuteProc ($sql);
}

function DisapproveTask($taskid, $notes)
{
	$sid = $this->GetSessionID();
	$notes = $this->MakeStringValue($notes);
	$sql = "call DisapproveTask($sid, $taskid, $notes)";
	return $this->ExecuteProc ($sql);
}

function MessageSeen($msgid)
{
	$sid = $this->GetSessionID();
	$sql = "call TaskMessageSeen($sid, $msgid)";
	return $this->ExecuteProc ($sql);
}

function DeleteTask($taskid)
{
	$sid = $this->GetSessionID();
	$sql = "call DeleteTask($sid, $taskid)";
	return $this->ExecuteProc ($sql);
}

function UndeleteTask($taskid)
{
	$sid = $this->GetSessionID();
	$sql = "call UndeleteTask($sid, $taskid)";
	return $this->ExecuteProc ($sql);
}

function TaskIsPaid($taskid){

	$sid = $this->GetSessionID();
	$sql = "call TaskIsPaid($sid, $taskid)";
	return $this->ExecuteProc ($sql);	
}

function TaskNotPaid($taskid){

	$sid = $this->GetSessionID();
	$sql = "call TaskNotPaid($sid, $taskid)";
	return $this->ExecuteProc ($sql);	
}

function TaskNotBilled($taskid){

	$sid = $this->GetSessionID();
	$sql = "call TaskNotBilled($sid, $taskid)";
	return $this->ExecuteProc ($sql);	
}
function TaskIsBilled($taskid){

	$sid = $this->GetSessionID();
	$sql = "call TaskIsBilled($sid, $taskid)";
	return $this->ExecuteProc ($sql);	
}


}
?>
