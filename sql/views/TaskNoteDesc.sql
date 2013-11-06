create or replace view taskhistory as 
select n.noteid, n.taskid, n.senton, n.fromid, n.msgtype, n.message, 
u.name fromname, u.email, n.subject, n.targetuser,
CASE msgtype 
WHEN 3 THEN 'Task assigned to you'
WHEN 4 THEN 'Task completed'
WHEN 5 THEN 'Task Rejected'
WHEN 6 THEN 'Task approved'
WHEN 7 THEN 'Task reassigned'
WHEN 8 THEN 'Task approver changed'
WHEN 9 THEN 'Task owner changed'
ELSE subject
END msgtypedesc
from tasknotes n
left outer join usernames u on n.fromid=u.userid;

create or replace view tasknotedesc as 
select n.noteid, n.taskid, t.areaid, n.senton, m.seenon, n.fromid, m.toid, n.msgtype, n.msgtypedesc, 
t.name, n.message, u.name fromname, u.email, n.subject
from taskhistory n
left outer join taskmessages m on n.noteid=m.noteid
inner join tasks t on n.taskid=t.taskid
left outer join usernames u on n.fromid=u.userid;


