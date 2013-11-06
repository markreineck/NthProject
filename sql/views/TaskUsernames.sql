/*
Depricate views
*/
drop view tasksubmittedby;
drop view taskapproveby;
drop view taskassignedto;
drop view taskusernames;

create or replace view taskusernames as 
select t.taskid, t.assignment, t.userid, initials, firstname, lastname, concat(firstname,' ',lastname) name
from taskassignments t, users u
where t.userid=u.userid;

create or replace view taskassignedto as 
select taskid, userid, initials, firstname, lastname, concat(firstname,' ',lastname) name
from taskusernames where assignment='T';

create or replace view taskapproveby as 
select taskid, userid, initials, firstname, lastname, concat(firstname,' ',lastname) name
from taskusernames where assignment='A';

create or replace view tasksubmittedby as 
select taskid, userid, initials, firstname, lastname, concat(firstname,' ',lastname) name
from taskusernames where assignment='S';
