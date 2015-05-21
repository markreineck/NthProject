<?php
class TaskFilter {
var $project, $milestone, $assignedto, $status, $paidstatus, $startdate, $enddate;
}

class TaskListDB extends TaskDB {

function TaskListDB($framework)
{
	$this->TaskDB($framework);
}

/**********************************************************************
 *	Task List Functions
 **********************************************************************/
function NameField()
{
	switch ($this->GetNameMode()) {
		case 'I':
			return 'initials';
		case 'F':
			return 'firstname';
		case 'L':
			return 'lastname';
	}
	return 'name';
}

function ProjectWhere($cookie)
{
	$where = '';
	if ($cookie->GetDefaultProject() > 0)
		$where .= ' and p.prjid='.$cookie->GetDefaultProject();
	return $where;
}

function ProjectAreaWhere($cookie)
{
	$where = '';
	if ($cookie->GetDefaultArea() > 0)
		$where .= ' and t.areaid='.$cookie->GetDefaultArea();
	return $where;
}

function MilestoneWhere($cookie)
{
	$where = '';
	if ($cookie->GetDefaultMilestone() > 0)
		$where .= ' and t.endmilestone='.$cookie->GetDefaultMilestone();
	return $where;
}

function AssignedToWhere($cookie)
{
	$uid = $cookie->GetDefaultUser();
	$where = '';
	if ($uid > 0)
		$where .= ' and t.assignedto='.$uid;
	else if ($uid == -2)
		$where .= ' and t.assignedto is null';

	if (!$this->IsGlobalSupervisor()) {
		$org = $this->IsSupervisor();
		if ($org)
			$where .= ' and p.orgid=' . $org;
		else
			$where .= ' and (t.assignedto='.$this->GetUserID().' or u.prjid is not null)';
	}
	return $where;
}

function PaidAssignedToWhere($cookie)
{
	if ($this->IsGlobalSupervisor()) {
		$where = '';
		$uid = $cookie->GetDefaultUser();
		if ($uid > 0)
			$where .= ' and t.assignedto='.$uid;
		else if ($uid == -2)
			$where .= ' and t.assignedto is null';
	} else {
		$where .= ' and t.assignedto='.$this->GetUserID();
	}
	return $where;
}

function TaskStatusWhere($cookie)
{
//  and (t.startmilestone is null or sm.completion is not null)
	$where = '';
	switch ($cookie->GetDefaultTaskStatus()) {
		case -2:	// All active
			$where .= ' and s.hold is null and t.complete is null and t.approved is null and t.removed is null';
			break;
		case -3:	// All held
			$where .= ' and s.hold is not null and t.removed is null and t.complete is null';
			break;
		case -4:	// Completed
			$where .= ' and t.complete is not null and t.approved is null and t.removed is null';
			break;
		case -5:	// Approved
			$where .= ' and t.approved is not null and t.released is null and t.removed is null';
			break;
		case -6:	// Released
			$where .= ' and t.released is not null and t.removed is null';
			break;
		case -7:	// Cancelled
			$where .= ' and t.removed is not null';
			break;
		default:
			if ($cookie->GetDefaultTaskStatus() > 0)
				$where .= ' and t.status='.$cookie->GetDefaultTaskStatus();
	}
	return $where;
}

function TaskDateField($cookie)
{
	switch ($cookie->GetDefaultTaskStatus()) {
		case -4:	// Completed
			$field = 't.complete';
			break;
		case -5:	// Approved
			$field = 't.approved';
			break;
		case -6:	// Released
			$field = 't.released';
			break;
		case -7:	// Cancelled
			$field = 't.removed';
			break;
		default:
			$field = 'ifnull(t.needby, e.targetdate)';
	}
	return $field;
}

function TaskPaidWhere($cookie)
{
	$where = '';
	switch ($cookie->GetDefaultPaymentStatus()) {
		case 'U':
			$where .= ' and t.paid is null';
			break;
		case 'P':
			$where .= ' and t.paid is not null';
			break;
	}
	return $where;
}

function TaskApprovedOnWhere($cookie)
{
	$daterange = new DateRange($cookie->GetDefaultDateRange());
	$start = $this->MakeDateValue($daterange->StartDate());

	if ($start) {
		$where = "t.approved>=$start";
		if ($daterange->EndDate()) {
			$end = $this->MakeNextDayValue($daterange->EndDate());
			$where .= " and t.approved<$end";
		}
	} else
		$where = 'date_add(t.approved, interval 1 month)>curdate()';
	return $where;
}

function TaskReleasedOnWhere($cookie)
{
	$daterange = new DateRange($cookie->GetDefaultDateRange());
	$start = $this->MakeDateValue($daterange->StartDate());
	$end = $this->MakeNextDayValue($daterange->EndDate());

	if ($start && $end)
		$where = "t.released>=$start and t.released<$end";
	else
		$where = 'date_add(t.released, interval 1 month)>curdate()';
	return $where;
}

function TaskBilledWhere($cookie)
{
	$where = '';
	switch ($cookie->GetDefaultPaymentStatus()) {
		case 'U':
			$where .= ' and t.billed is null';
			break;
		case 'P':
			$where .= ' and t.billed is not null';
			break;
	}
	return $where;
}

function KeyWordWhere()
{
	$where = '';
	if (isset($_POST['Keyword'])) {
		$word = $_POST['Keyword'];
		$where .= " and (t.name like '%$word%' or t.taskid in (select taskid from tasknotes where message like '%$word%'))";
	}
	return $where;
}

function ProjectListWhere($cookie)
{
	$where = '';
	$where .= $this->ProjectWhere($cookie);
	$where .= $this->ProjectAreaWhere($cookie);
	$where .= $this->AssignedToWhere($cookie);
	$where .= $this->TaskStatusWhere($cookie);
	$where .= $this->MilestoneWhere($cookie);
	$where .= $this->KeyWordWhere();
	return $where;
}

function ListApproveFixedPriceTasks($cookie)
{
	$namefield = $this->NameField();

	$where = '';
	$where .= $this->ProjectWhere($cookie);
	$where .= $this->PaidAssignedToWhere($cookie);
	$where .= $this->TaskPaidWhere($cookie);
	$uid = $this->GetUserID();
	$sql = "select p.orgid, t.taskid, p.name as project, a.name as area, t.name task, at.$namefield assignedto, p.prjid, t.complete, t.approved, t.cost, t.paid
from tasks t
inner join projectareas a on a.areaid=t.areaid
inner join projects p on p.prjid=a.prjid
left outer join usernames at on t.assignedto=at.userid
where t.removed is null and t.cost is not null and t.approved is not null and p.completed is null and p.status='A' $where
order by t.complete";
	return $this->SelectAll($sql);
}

function ListTasksForRelease($cookie)
{
	$namefield = $this->NameField();

	$where = $this->MilestoneWhere($cookie);
	if ($cookie->GetDefaultUser() > 0)
		$where .= ' and t.assignedto=' . $cookie->GetDefaultUser();

	$sql = "select t.taskid, a.name as area, t.name task, e.name milestone,
at.name assignedto, ab.name approvedby, t.complete, t.approved, t.released
from tasks t
inner join projectareas a on a.areaid=t.areaid
left outer join milestones e on t.endmilestone=e.milestoneid
left outer join usernames at on t.assignedto=at.userid
left outer join usernames ab on t.approvedby=ab.userid
where t.removed is null and t.approved is not null and t.released is null and a.prjid=" . $cookie->GetDefaultProject() . " $where
order by t.complete";
	return $this->SelectAll($sql);
}

function ListPaidTasks($cookie)
{
	if (!$this->IsGlobalSupervisor())
		return NULL;

	$namefield = $this->NameField();

	$where = '';
	$where .= $this->ProjectWhere($cookie);
	$where .= $this->PaidAssignedToWhere($cookie);
//	$where .= $this->TaskPaidWhere($cookie);
	$uid = $this->GetUserID();
	$sql = "select p.orgid, t.taskid, p.name as project, a.name as area, t.name task, at.$namefield assignedto, p.prjid, t.complete, t.approved, t.cost, t.paid
from tasks t
inner join projectareas a on a.areaid=t.areaid
inner join projects p on p.prjid=a.prjid
left outer join usernames at on t.assignedto=at.userid
where t.cost is not null and t.paid is not null $where
order by t.paid, at.name, p.name";
	return $this->SelectAll($sql);
}

function ListMyPaidTasks($cookie)
{
	$namefield = $this->NameField();

	$where = ' and t.assignedto='.$this->GetUserID();
	$where .= $this->ProjectWhere($cookie);
//	$where .= $this->AssignedToWhere($cookie);
	$uid = $this->GetUserID();
	$sql = "select p.orgid, t.taskid, p.name as project, a.name as area, t.name task, at.$namefield assignedto, p.prjid, t.complete, t.approved, t.cost, t.paid
from tasks t
inner join projectareas a on a.areaid=t.areaid
inner join projects p on p.prjid=a.prjid
left outer join usernames at on t.assignedto=at.userid
where t.cost is not null and t.paid is not null $where
order by t.paid, p.name";
	return $this->SelectAll($sql);
}

function ListApproveBilledTasks($cookie)
{
	if (!$this->IsGlobalSupervisor())
		return NULL;

	$namefield = $this->NameField();

	$where = '';
	$where .= $this->ProjectWhere($cookie);
	$where .= $this->AssignedToWhere($cookie);
	$where .= $this->TaskBilledWhere($cookie);
	$uid = $this->GetUserID();
	$sql = "select p.orgid, t.taskid, p.name as project, a.name as area, t.name task, at.$namefield assignedto, p.prjid, t.complete, t.approved, t.cost, t.billed
from tasks t
inner join projectareas a on a.areaid=t.areaid
inner join projects p on p.prjid=a.prjid
left outer join usernames at on t.assignedto=at.userid
where t.cost is not null and t.approved is not null $where
order by t.complete";
	return $this->SelectAll($sql);
}


function ListBilledTasks($cookie)
{
	if (!$this->IsGlobalSupervisor())
		return NULL;

	$namefield = $this->NameField();

	$where = '';
	$where .= $this->ProjectWhere($cookie);
	$where .= $this->AssignedToWhere($cookie);
//	$where .= $this->TaskBilledWhere($cookie);
	$uid = $this->GetUserID();
	$sql = "select p.orgid, t.taskid, p.name as project, a.name as area, t.name task, at.$namefield assignedto, p.prjid, t.complete, t.approved, t.cost, t.billed
from tasks t
inner join projectareas a on a.areaid=t.areaid
inner join projects p on p.prjid=a.prjid
left outer join usernames at on t.assignedto=at.userid
where t.cost is not null and t.billed is not null $where and p.completed is null and p.status='A' $where
order by t.billed";
	return $this->SelectAll($sql);
}

function ListTasksOrdered($orderby, $cookie)
{
	$uid = $this->GetUserID();
	$namefield = $this->NameField();
	$datefld = $this->TaskDateField($cookie);
/*
	$sql = "select p.orgid, t.taskid, t.priority, s.name status, p.name as project, a.name as area, t.name task, at.name assignedto, at.initials assignedinitials, p.prjid, u.edit, u.assign, u.superuser, t.complete, t.approved, t.cost,
date_format(ifnull(t.needby, e.targetdate), '%b %e, %Y') needby, 
date_format(t.submittedon, '%b %e, %Y') submitted, 
date_format(t.removed, '%b %e, %Y') removedon
from tasks t
inner join projectareas a on a.areaid=t.areaid
inner join projects p on p.prjid=a.prjid
left outer join milestones sm on t.startmilestone=sm.milestoneid
left outer join milestones e on t.endmilestone=e.milestoneid
left outer join usernames at on t.assignedto=at.userid
left outer join taskstatus s on t.status=s.statusid
left outer join (select prjid, superuser, edit, assign from projectusers where userid=$uid) u on p.prjid=u.prjid
where $where and p.completed is null and p.status='A' ".$this->ProjectListWhere($cookie)."
order by $orderby, t.priority";
*/
	$sql = "select p.orgid, t.taskid, t.priority, s.name status, p.name as project, a.name as area, t.name task, at.$namefield assignedto, p.prjid, u.edit, u.assign, u.superuser, t.complete, t.approved, t.cost,
date_format($datefld, '%b %e, %Y') needby, 
date_format(t.submittedon, '%b %e, %Y') submitted, 
date_format(t.removed, '%b %e, %Y') removedon
from tasks t
inner join projectareas a on a.areaid=t.areaid
inner join projects p on p.prjid=a.prjid
left outer join milestones sm on t.startmilestone=sm.milestoneid
left outer join milestones e on t.endmilestone=e.milestoneid
left outer join usernames at on t.assignedto=at.userid
left outer join taskstatus s on t.status=s.statusid
left outer join (select prjid, superuser, edit, assign from projectusers where userid=$uid) u on p.prjid=u.prjid
where p.completed is null and p.status='A'".$this->ProjectListWhere($cookie)."
order by $orderby, t.priority";

	return $this->SelectAll($sql);
}

function ListTasksByPriority($cookie)
{
//	return $this->ListTasksOrdered('t.complete is null and t.removed is null', "t.priority, p.priority, p.name, ifnull(t.needby,'2199-12-31')", $cookie);
	return $this->ListTasksOrdered("t.priority, p.priority, p.name, ifnull(t.needby,'2199-12-31')", $cookie);
}

function ListDeletedTasks($cookie)
{
//	return $this->ListTasksOrdered('t.removed is not null', 't.removed, p.name', $cookie);
	return $this->ListTasksOrdered('t.removed, p.name', $cookie);
}

function ListTasksByMilestone($cookie)
{
	if ($cookie->GetDefaultMilestone() > 0) {
		$where = 'removed is null';
		$where .= $this->AssignedToWhere($cookie);
		$where .= $this->MilestoneWhere($cookie);
		$where .= $this->KeyWordWhere();

		$uid = $this->GetUserID();
		$sql = "select p.orgid, t.taskid, t.priority, p.name as project, a.name as area, t.name task, at.name assignedto, at.initials assignedinitials, p.prjid, u.edit, u.assign, u.superuser, t.complete, t.approved, t.approvedby, t.cost,
date_format(ifnull(t.needby, e.targetdate), '%b %e, %Y') needby, 
date_format(t.submittedon, '%b %e, %Y') submitted, 
if(t.released is not null,'Released',
if(t.approved is not null,'Approved',
if(t.complete is not null,'Complete',s.name))) status,
if(t.released is not null,-10,
if(t.approved is not null,-9,
if(t.complete is not null,-8,t.status))) sortorder
from tasks t
inner join projectareas a on a.areaid=t.areaid
inner join projects p on p.prjid=a.prjid
left outer join milestones e on t.endmilestone=e.milestoneid
left outer join usernames at on t.assignedto=at.userid
left outer join taskstatus s on t.status=s.statusid
left outer join (select prjid, superuser, edit, assign from projectusers where userid=$uid) u on p.prjid=u.prjid
where $where
order by sortorder, t.priority";

		return $this->SelectAll($sql);
	} else {
		return NULL;
	}
}

function ListTasksWithCost($cookie)
{
//	return $this->ListTasksOrdered('t.cost is not null', 'complete', $cookie);
	$uid = $this->GetUserID();
	$where = '';
	$where .= $this->ProjectWhere($cookie);
	$where .= $this->PaidAssignedToWhere($cookie);
	$where .= $this->TaskStatusWhere($cookie);
	
	$sql = "select p.orgid, t.taskid, t.priority, s.name status, p.name as project, a.name as area, t.name task, at.name assignedto, at.initials assignedinitials, p.prjid, r.edit, r.assign, r.superuser, t.cost, t.approvedby, 
date_format(t.complete, '%b %e, %Y') completedon,
date_format(t.approved, '%b %e, %Y') approved,
date_format(ifnull(t.needby, e.targetdate), '%b %e, %Y') needby
from tasks t
inner join projectareas a on a.areaid=t.areaid
inner join projects p on p.prjid=a.prjid
left outer join usernames at on t.assignedto=at.userid
left outer join taskstatus s on t.status=s.statusid
left outer join milestones sm on t.startmilestone=sm.milestoneid
left outer join milestones e on t.endmilestone=e.milestoneid
left outer join (select prjid, superuser, edit, assign from projectusers where userid=$uid) r on p.prjid=r.prjid
where t.removed is null and t.cost is not null and p.completed is null and p.status='A'$where
order by t.complete";

	return $this->SelectAll($sql);
}

function ListTasksByTargetDate($cookie)
{
	return $this->ListTasksOrdered("ifnull(ifnull(t.needby, e.targetdate),'2199-13-31'), t.priority", $cookie);
//	return $this->ListTasksOrdered('t.complete is null and t.removed is null', "ifnull(ifnull(t.needby, e.targetdate),'2199-13-31'), t.priority", $cookie);
}

function ListTasksByPerson($cookie)
{
	return $this->ListTasksOrdered('assignedto', $cookie);
//	return $this->ListTasksOrdered('t.complete is null and t.removed is null', 'assignedto', $cookie);
}

function ListTasksByCreatedOn($cookie)
{
//	return $this->ListTasksOrdered('t.complete is null and t.removed is null', 'submittedon desc', $cookie);
	return $this->ListTasksOrdered('submittedon desc', $cookie);
}

function ListTasksByCompletedOn($cookie)
{
	$where = '';
	$where .= $this->ProjectWhere($cookie);
	$where .= $this->AssignedToWhere($cookie);
//	$where .= $this->TaskStatusWhere($cookie);
	$where .= $this->KeyWordWhere();

	$uid = $this->GetUserID();
	$sql = "select p.orgid, t.taskid, t.priority, p.name as project, a.name as area, t.name task, at.name assignedto, at.initials assignedinitials, p.prjid, u.edit, u.assign, u.superuser, t.complete, t.approved, t.approvedby
from tasks t
inner join projectareas a on a.areaid=t.areaid
inner join projects p on p.prjid=a.prjid
left outer join usernames at on t.assignedto=at.userid
left outer join taskstatus s on t.status=s.statusid
left outer join (select prjid, superuser, edit, assign from projectusers where userid=$uid) u on p.prjid=u.prjid
where t.removed is null and t.complete is not null and p.completed is null and p.status='A' and t.approved is null $where
order by t.complete desc";
	return $this->SelectAll($sql);
}

function ListTasksByApprovedOn($cookie)
{
	$uid = $this->GetUserID();
	$where = $this->TaskApprovedOnWhere($cookie);
	$where .= $this->ProjectWhere($cookie);
	$where .= $this->AssignedToWhere($cookie);
	
	$sql = "select p.orgid, t.taskid, t.priority, p.name as project, a.name as area, t.name task, at.name assignedto, at.initials assignedinitials, p.prjid, u.edit, u.assign, u.superuser, t.complete, t.approved, t.approvedby
from tasks t
inner join projectareas a on a.areaid=t.areaid
inner join projects p on p.prjid=a.prjid
left outer join usernames at on t.assignedto=at.userid
left outer join (select prjid, superuser, edit, assign from projectusers where userid=$uid) u on p.prjid=u.prjid
where $where and t.removed is null
order by t.approved desc";
	return $this->SelectAll($sql);
}

function ListTasksByReleasedOn($cookie)
{
	$uid = $this->GetUserID();
	$sql = "select p.orgid, t.taskid, t.priority, p.name as project, a.name as area, t.name task, at.name assignedto, at.initials assignedinitials, p.prjid, r.edit, r.assign, r.superuser, t.complete, t.approved, t.approvedby, t.released, rb.name releasedby
from tasks t
inner join projectareas a on a.areaid=t.areaid
inner join projects p on p.prjid=a.prjid
left outer join usernames at on t.assignedto=at.userid
left outer join usernames rb on t.releasedby=rb.userid
left outer join (select prjid, superuser, edit, assign from projectusers where userid=$uid) r on p.prjid=r.prjid
where ".$this->TaskReleasedOnWhere($cookie).$this->ProjectListWhere($cookie)." and t.removed is null
order by t.approved desc";
	return $this->SelectAll($sql);
}

function ListTasksByProject($where, $cookie)
{
	$userid = $this->GetUserID();

	$sql = "select pa.prjid, p.name project, pa.areaid, pa.name areaname, t.taskid, t.priority, at.name assigned, DATE_FORMAT(em.targetdate, '%b %e, %Y') needed, t.assignedto, t.submittedby, t.approvedby approveby, t.name, u.approval, u.superuser, s.name status, t.complete, t.approved, t.approvedby
from tasks t
inner join projectareas pa on pa.areaid=t.areaid
inner join projects p on pa.prjid=p.prjid
left outer join taskstatus s on t.status=s.statusid
left outer join milestones sm on t.startmilestone=sm.milestoneid
left outer join milestones em on t.endmilestone=em.milestoneid
left outer join usernames at on t.assignedto=at.userid
left outer join projectusers u on u.prjid=p.prjid and u.userid=$userid
where p.completed is null and p.status='A'" . $this->ProjectListWhere($cookie);

	$sql .= '
order by p.priority, p.name, pa.name, t.priority, t.needby';

	return $this->SelectAll($sql);
}

function ListActiveTasksByProject($cookie)
{
	return $this->ListTasksByProject('t.complete is null', $cookie);
}

function ListCompletedTasksByProject($cookie)
{
	return $this->ListTasksByProject('t.complete is not null and t.approved is null', $cookie);
}

function ListApprovedTasksByProject($cookie)
{
	return $this->ListTasksByProject($this->TaskApprovedOnWhere($cookie), $cookie);
}

function SearchTasks($cookie)
{
	$uid = $this->GetUserID();
	$sql = "select t.taskid, t.priority, t.status, a.areaid, t.name, t.assignedto, t.submittedby, t.approvedby, a.prjid, u.edit, u.assign, u.superuser, UNIX_TIMESTAMP(t.complete) complete, UNIX_TIMESTAMP(t.approved) approved, t.cost, UNIX_TIMESTAMP(t.needby) needby, t.startmilestone, t.endmilestone, UNIX_TIMESTAMP(t.submittedon) submittedon,  UNIX_TIMESTAMP(t.released) released
from tasks t
inner join projectareas a on a.areaid=t.areaid
inner join projects p on a.prjid=p.prjid
left outer join taskstatus s on t.status=s.statusid
left outer join (select prjid, superuser, edit, assign from projectusers where userid=$uid) u on p.prjid=u.prjid
where t.removed is null and p.completed is null and p.status='A' ".$this->ProjectListWhere($cookie);

	return $this->SelectAll($sql);
}

}
?>
