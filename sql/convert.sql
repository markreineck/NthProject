delete from tasknotes;
delete from taskassignments;
delete from tasks;
delete from projectareas;
delete from projects;

insert into projects (orgid, prjid, name, started, priority, status)
select 1, prjid, name, started, priority, status
from projectstmp where prjid in (85,98)

insert into projectareas (areaid, prjid, name)
select areaid, prjid, name
from projectareastmp where prjid in (85,98)

update tasktmp set status=1 where status='queued';
update tasktmp set status=3 where status='needinfo';
update tasktmp set status=2 where status='active';
update tasktmp set status=3 where status='Need Info';
update tasktmp set status=4 where status='Hold';
select distinct status from tasktmp;

insert into tasks (taskid, prjid, areaid, status, edited, datesedited, priority, name, complete, approved)
select t.taskid, a.prjid, t.areaid, t.status, submittedon, submittedon, t.priority, t.name, t.complete, t.approved
from tasktmp t, projectareastmp a where a.areaid=t.areaid and a.prjid in (85,98)

insert into taskassignments (taskid, assignment, edited, userid)
select t.taskid, 'S', now(), t.submittedby
from tasktmp t, projectareastmp a where a.areaid=t.areaid and a.prjid in (85,98)
and submittedby is not null

insert into taskassignments (taskid, assignment, edited, editedby, userid)
select t.taskid, 'A', now(), t.approvedby, t.approvedby
from tasktmp t, projectareastmp a where a.areaid=t.areaid and a.prjid in (85,98)
and approvedby in (1,136)

insert into taskassignments (taskid, assignment, edited, editedby, userid)
select t.taskid, 'T', now(), t.assignedto, t.assignedto
from tasktmp t, projectareastmp a where a.areaid=t.areaid and a.prjid in (85,98)
and assignedto is not null

insert into tasknotes (taskid, msgtype, senton, message)
select t.taskid, 1, t.submittedon, t.descr
from tasktmp t, projectareastmp a where a.areaid=t.areaid and a.prjid in (85,98)
and t.descr is not null