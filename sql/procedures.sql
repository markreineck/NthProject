-- --------------------------------------------------------

--
-- Procedures
--

delimiter $$

/* * * * * * * * * * * * * * * * * * * * * * * * * * *
 * User Session Validation
 * * * * * * * * * * * * * * * * * * * * * * * * * * */
drop procedure if exists ValidateUser$$
create procedure ValidateUser (
	in	i_ses	bigint unsigned,
	out	o_usrid	int unsigned
) begin

declare v_ok tinyint;

select 0 into @err;

select s.userid into o_usrid
from usersession s
where s.sessionid=i_ses and expireson>now();

if o_usrid is null then
	select -102 into @err;
end if;

end$$

/* * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Super User Rights Validation
 * * * * * * * * * * * * * * * * * * * * * * * * * * */
drop procedure if exists ValidateOrgSuperUser$$
create procedure ValidateOrgSuperUser (
	in	i_ses	bigint unsigned,
	in	i_org	int unsigned,
	out	o_usrid	int unsigned
) begin

declare v_ok tinyint;

select 0 into @err;

select u.superuser, u.userid into v_ok, o_usrid
from users u, usersession s, subscription x
where s.sessionid=i_ses and s.userid=u.userid and (u.orgid=i_org or u.orgid=x.orgid) limit 1;

if v_ok is null or v_ok<1 then
	select -110 into @err;
end if;

end$$

drop procedure if exists ValidateProjectSuperUser$$
create procedure ValidateProjectSuperUser (
	in	i_ses	bigint unsigned,
	in	i_prj	int unsigned,
	out	o_usrid	int unsigned
) begin

declare v_org int unsigned;
declare v_priv int;

select orgid into v_org from projects where prjid=i_prj;

if found_rows() < 1 then
	select -115 into @err;
else
	call ValidateOrgSuperUser (i_ses, v_org, o_usrid);
	if @err != 0 then
		select count(*) into v_priv
		from projectusers p, usersession s
		where prjid=i_prj and sessionid=i_ses and s.userid=p.userid and p.superuser is not null;

		if v_priv > 0 then
			select 0 into @err;
		end if;
	end if;
end if;

end$$

drop procedure if exists ValidateAreaSuperUser$$
create procedure ValidateAreaSuperUser (
	in	i_ses	bigint unsigned,
	in	i_area	int unsigned,
	out	o_usrid	int unsigned
) begin

declare v_prj int unsigned;

select prjid into v_prj from projectareas where areaid=i_area;

if found_rows() > 0 then
	call ValidateProjectSuperUser (i_ses, v_prj, o_usrid);
else
	select -112 into @err;
end if;

end$$

drop procedure if exists ValidateTaskSuperUser$$
create procedure ValidateTaskSuperUser (
	in	i_ses	bigint unsigned,
	in	i_task	int unsigned,
	out	o_usrid	int unsigned
) begin

declare v_prj int unsigned;

select a.prjid into v_prj
from projectareas a, tasks t
where a.areaid=t.areaid and t.taskid=i_task;

if found_rows() > 0 then
	call ValidateProjectSuperUser (i_ses, v_prj, o_usrid);
else
	select -114 into @err;
end if;

end$$

drop procedure if exists ValidateMilestoneSuperUser$$
create procedure ValidateMilestoneSuperUser (
	in	i_ses	bigint unsigned,
	in	i_msid	int unsigned,
	out	o_usrid	int unsigned
) begin

declare v_prj int unsigned;

select prjid into v_prj from milestones where milestoneid=i_msid;

if found_rows() > 0 then
	call ValidateProjectSuperUser (i_ses, v_prj, o_usrid);
else
	select -113 into @err;
end if;

end$$

/* * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Task Rights Validation
 * * * * * * * * * * * * * * * * * * * * * * * * * * */
drop procedure if exists ValidateAddTaskRights$$
create procedure ValidateAddTaskRights (
	in	i_ses	bigint unsigned,
	in	i_area	int unsigned,
	out	o_usrid	int unsigned
) begin

declare v_org int unsigned;
declare v_prj int unsigned;
declare v_priv int;

select 0 into @err;

select p.orgid, p.prjid into v_org, v_prj from projects p, projectareas a
where p.prjid=a.prjid and a.areaid=i_area;

if found_rows() > 0 then
	call ValidateOrgSuperUser (i_ses, v_org, o_usrid);
end if;

if @err != 0 and o_usrid > 0 then
	select count(*) into v_priv from projectusers
	where prjid=v_prj and userid=o_usrid and (superuser is not null or submit is not null);

	if v_priv > 0 then
		select 0 into @err;
	end if;
end if;

end$$

drop procedure if exists ValidateTaskEditRights$$
create procedure ValidateTaskEditRights (
	in	i_ses	bigint unsigned,
	in	i_task	bigint unsigned,
	out	o_usrid	int unsigned
) begin

declare v_org int unsigned;
declare v_prj int unsigned;
declare v_priv int;

select p.orgid, p.prjid into v_org, v_prj
from projects p, projectareas a, tasks t
where p.prjid=a.prjid and a.areaid=t.areaid and t.taskid=i_task;

if found_rows() > 0 then
	call ValidateOrgSuperUser (i_ses, v_org, o_usrid);
end if;

if @err != 0 and o_usrid > 0 then
	select count(*) into v_priv from tasks
	where taskid=i_task and (submittedby=o_usrid or approvedby=o_usrid);

	if v_priv > 0 then
		select 0 into @err;
	end if;
end if;

if @err != 0 and o_usrid > 0 then
	select count(*) into v_priv from projectusers
	where prjid=v_prj and userid=o_usrid and (superuser is not null or edit is not null);

	if v_priv > 0 then
		select 0 into @err;
	end if;
end if;

end$$

drop procedure if exists ValidateTaskAssignRights$$
create procedure ValidateTaskAssignRights (
	in	i_ses	bigint unsigned,
	in	i_task	bigint unsigned,
	out	o_usrid	int unsigned
) begin

declare v_org int unsigned;
declare v_prj int unsigned;
declare v_priv int;

select p.orgid, p.prjid into v_org, v_prj
from projects p, projectareas a, tasks t
where p.prjid=a.prjid and a.areaid=t.areaid and t.taskid=i_task;

if found_rows() > 0 then
	call ValidateOrgSuperUser (i_ses, v_org, o_usrid);
end if;

if @err != 0 and o_usrid > 0 then
	select count(*) into v_priv from projectusers
	where prjid=v_prj and userid=o_usrid and (superuser is not null or assign is not null);

	if v_priv > 0 then
		select 0 into @err;
	end if;
end if;

end$$

drop procedure if exists ValidateTaskApproveRights$$
create procedure ValidateTaskApproveRights (
	in	i_ses	bigint unsigned,
	in	i_task	bigint unsigned,
	out	o_usrid	int unsigned
) begin

declare v_org int unsigned;
declare v_prj int unsigned;
declare v_priv int;

select p.orgid, p.prjid into v_org, v_prj
from projects p, projectareas a, tasks t
where p.prjid=a.prjid and a.areaid=t.areaid and t.taskid=i_task;

if found_rows() > 0 then
	call ValidateOrgSuperUser (i_ses, v_org, o_usrid);
end if;

if @err != 0 and o_usrid > 0 then
	select count(*) into v_priv from projectusers
	where prjid=v_prj and userid=o_usrid and (superuser is not null or approval is not null);

	if v_priv > 0 then
		select 0 into @err;
	end if;
end if;

end$$

drop procedure if exists ValidateTaskReleaseRights$$
create procedure ValidateTaskReleaseRights (
	in	i_ses	bigint unsigned,
	in	i_task	bigint unsigned,
	out	o_usrid	int unsigned
) begin

declare v_org int unsigned;
declare v_prj int unsigned;
declare v_priv int;

select p.orgid, p.prjid into v_org, v_prj
from projects p, projectareas a, tasks t
where p.prjid=a.prjid and a.areaid=t.areaid and t.taskid=i_task;

if found_rows() > 0 then
	call ValidateOrgSuperUser (i_ses, v_org, o_usrid);
end if;

if @err != 0 and o_usrid > 0 then
	select count(*) into v_priv from projectusers
	where prjid=v_prj and userid=o_usrid and (superuser is not null or publish is not null);

	if v_priv > 0 then
		select 0 into @err;
	end if;
end if;

end$$

drop procedure if exists ValidateAnyTaskRights$$
create procedure ValidateAnyTaskRights (
	in	i_ses	bigint unsigned,
	in	i_task	bigint unsigned,
	out	o_usrid	int unsigned
) begin

declare v_org int unsigned;
declare v_prj int unsigned;
declare v_priv int;

select p.orgid, p.prjid into v_org, v_prj
from projects p, projectareas a, tasks t
where p.prjid=a.prjid and a.areaid=t.areaid and t.taskid=i_task;

if found_rows() > 0 then
	call ValidateOrgSuperUser (i_ses, v_org, o_usrid);
end if;

if @err != 0 and o_usrid > 0 then
	select count(*) into v_priv from tasks
	where taskid=i_task and (submittedby=o_usrid or approvedby=o_usrid or assignedto=o_usrid);

	if v_priv > 0 then
		select 0 into @err;
	end if;
end if;

if @err != 0 and o_usrid > 0 then
	select count(*) into v_priv from projectusers
	where prjid=v_prj and userid=o_usrid;

	if v_priv > 0 then
		select 0 into @err;
	end if;
end if;

end$$

/* * * * * * * * * * * * * * * * * * * * * * * * * * *
 * User Maintenance Rights Validation
 * * * * * * * * * * * * * * * * * * * * * * * * * * */
drop procedure if exists ValidateUserMaintRights$$
create procedure ValidateUserMaintRights (
	in	i_ses	bigint unsigned,
	in	i_org	int unsigned,
	out	o_usrid	int unsigned
) begin

declare v_ok tinyint;

select 0 into @err;

select u.usermaint, u.userid into v_ok, o_usrid
from users u, usersession s
where s.sessionid=i_ses and s.userid=u.userid and (u.orgid=i_org or u.orgid=1);
/*
if v_ok is null or v_ok<1 then
	select -100 into @err;
end if;
*/
end$$

drop procedure if exists ValidateUserMaintOverUser$$
create procedure ValidateUserMaintOverUser (
	in	i_ses	bigint unsigned,
	in	i_user	int unsigned,
	out	o_usrid	int unsigned
) begin

declare v_org int unsigned;

select orgid into v_org from users where userid=i_user;
if found_rows() > 0 then
	call ValidateUserMaintRights(v_org, o_usrid, o_usrid);
else
	select -101 into @err;
end if;

end$$

DROP PROCEDURE IF EXISTS CreateTaskStatus$$

create procedure CreateTaskStatus (
	i_ses	bigint unsigned,
	i_hold	tinyint,
	i_name	varchar(80)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1400 into @err;
end;

call ValidateOrgSuperUser(i_ses, 0, v_me);

insert into taskstatus (hold, name)
values (i_hold, i_name);

if row_count() < 1 then
	select -1401 into @err;
end if;

end$$

DROP PROCEDURE IF EXISTS UpdateTaskStatus$$

create procedure UpdateTaskStatus (
	i_ses	bigint unsigned,
	i_id	int unsigned,
	i_hold	tinyint,
	i_name	varchar(80)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1410 into @err;
end;

call ValidateOrgSuperUser(i_ses, 0, v_me);

update taskstatus set name=i_name, hold=i_hold where statusid=i_id;

if row_count() < 1 then
	select -1411 into @err;
end if;

end$$

DROP PROCEDURE IF EXISTS DeleteTaskStatus$$

create procedure DeleteTaskStatus (
	i_ses	bigint unsigned,
	i_id	int unsigned,
	i_newid	int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1420 into @err;
end;

call ValidateOrgSuperUser(i_ses, 0, v_me);

if i_newid is not null then
	update tasks set status=i_newid where status=i_id;
end if;
delete from taskstatus where statusid=i_id;

if row_count() < 1 then
	select -1421 into @err;
end if;

end$$

DROP PROCEDURE IF EXISTS CreateUserStatus$$

create procedure CreateUserStatus (
	i_ses	bigint unsigned,
	i_pay	varchar(1),
	i_name	varchar(40)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1430 into @err;
end;

call ValidateUserMaintRights(i_ses, 0, v_me);

insert into userstatus (paytype, name)
values (i_pay, i_name);

if row_count() < 1 then
	select -1431 into @err;
end if;

end$$

DROP PROCEDURE IF EXISTS UpdateUserStatus$$

create procedure UpdateUserStatus (
	i_ses	bigint unsigned,
	i_id	int unsigned,
	i_pay	varchar(1),
	i_name	varchar(40)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1440 into @err;
end;

call ValidateUserMaintRights(i_ses, 0, v_me);

update userstatus set name=i_name, paytype=i_pay where statusid=i_id;

if row_count() < 1 then
	select -1441 into @err;
end if;

end$$

DROP PROCEDURE IF EXISTS DeleteUserStatus$$

create procedure DeleteUserStatus (
	i_ses	bigint unsigned,
	i_id	int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1450 into @err;
end;

call ValidateUserMaintRights(i_ses, 0, v_me);

delete from userstatus where statusid=i_id;

if row_count() < 1 then
	select -1451 into @err;
end if;

end$$

DROP PROCEDURE IF EXISTS CreateMilestone$$

create procedure CreateMilestone (
	i_session	bigint unsigned,
	i_prjid		int unsigned,
	i_name		varchar(80), 
	i_target	date,
	i_descr		text
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1600 into @err;
end;

call ValidateProjectSuperUser(i_session, i_prjid, v_me);
if @err = 0 then
	insert into milestones(prjid, edited, editedby, name, targetdate, descr) 
	values (i_prjid, now(), v_me, i_name, i_target, i_descr);

	if row_count() < 1 then
		select -1601 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS UpdateMilestone$$

create procedure UpdateMilestone (
	i_session	bigint unsigned,
	i_msid		int unsigned,
	i_name		varchar(80), 
	i_target	date,
	i_comp		date,
	i_descr		text
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1610 into @err;
end;

call ValidateMilestoneSuperUser(i_session, i_msid, v_me);
if @err = 0 then
	update milestones set edited=now(), editedby=v_me, name=i_name, targetdate=i_target, completion=i_comp, descr=i_descr
	where milestoneid=i_msid;

	if row_count() < 1 then
		select -1611 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS MilestoneComplete$$

create procedure MilestoneComplete (
	i_session	bigint unsigned,
	i_msid		int unsigned,
	i_date		date
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1620 into @err;
end;

call ValidateMilestoneSuperUser(i_session, i_msid, v_me);
if @err = 0 then
	update milestones set 
	edited=now(), completion=i_date, completedby=v_me
	where milestoneid=i_msid;

	if row_count() < 1 then
		select -1621 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS DeleteMilestone$$

create procedure DeleteMilestone (
	i_session	bigint unsigned,
	i_msid		int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1630 into @err;
end;

call ValidateMilestoneSuperUser(i_session, i_msid, v_me);
if @err = 0 then
	update task set startmilestone=null where startmilestone=-i_msid;
	update task set endmilestone=null where endmilestone=-i_msid;

	delete from milestones where milestoneid=i_msid;

	if row_count() < 1 then
		select -1631 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS CreateCompany$$

create procedure CreateCompany (
	i_session	bigint unsigned,
	i_status	int unsigned,
	i_name		varchar(80)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1200 into @err;
end;

call ValidateOrgSuperUser(i_session, 0, v_me);
if @err = 0 then
	insert into organizations (edited, editedby, status, name)
	values (now(), v_me, i_status, i_name);

	if row_count() < 1 then
		select -1201 into @err;
	else
		select last_insert_id() into @orgid;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS UpdateOrganization$$

create procedure UpdateOrganization (
	i_session	bigint unsigned,
	i_orgid		int unsigned,
	i_status	int unsigned,
	i_name		varchar(80)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1210 into @err;
end;

call ValidateOrgSuperUser(i_session, 1, v_me);
if @err = 0 then
	update organizations set 
	edited=now(), editedby=v_me, status=i_status, name=i_name
	where orgid=i_orgid;

	if row_count() < 1 then
		select -1211 into @err;
	end if;
end if;

end$$

drop procedure if exists UpdateOrgField$$
create procedure UpdateOrgField (
	i_session	bigint unsigned,
	i_orgid		int unsigned,
	i_fieldid	int unsigned, 
	i_value		varchar(80)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1220 into @err;
end;

call ValidateOrgSuperUser(i_session, i_orgid, v_me);

if @err = 0 then
	start transaction;
	delete from organizationfields where orgid=i_orgid and fieldid=i_fieldid;

	if i_value is not null then
		insert into organizationfields (orgid, fieldid, edited, editedby, value)
		values (i_orgid, i_fieldid, now(), v_me, i_value);

		if row_count() < 1 then
			rollback;
			select -1221 into @err;
		end if;
	end if;
	commit;
end if;
end$$

create procedure DeleteOrganization (
	i_session	bigint unsigned,
	i_orgid		int unsigned
) begin

declare v_me int unsigned;
declare v_cnt int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1230 into @err;
end;

call ValidateOrgSuperUser(i_session, 1, v_me);

if @err = 0 then
	select count(*) into v_cnt from users where orgid=i_orgid;
	if v_cnt > 0 then
		select -1233 into @err;
	end if;
end if;

if @err = 0 then
	select count(*) into v_cnt from projects where orgid=i_orgid;
	if v_cnt > 0 then
		select -1232 into @err;
	end if;
end if;

if @err = 0 then
	delete from organizations where orgid=i_orgid;

	if row_count() < 1 then
		select -1231 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS CreateProjectArea$$

create procedure CreateProjectArea (
	i_session	bigint unsigned,
	i_prjid		int unsigned,
	i_resp		int unsigned,
	i_name		varchar(80), 
	i_target	date,
	i_price		int unsigned
) begin

declare v_me int unsigned;
declare v_cnt int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1500 into @err;
end;

select count(*) into v_cnt from projectareas where prjid=i_prjid and name=i_name;
if v_cnt > 0 then
	select -1502 into @err;
else
	call ValidateProjectSuperUser(i_session, i_prjid, v_me);
	if @err = 0 then
		insert into projectareas (
			prjid, edited, editedby, name, due, responsible, price
		) values (
			i_prjid, now(), v_me, i_name, i_target, i_resp, i_price
		);

		if row_count() < 1 then
			select -1501 into @err;
		end if;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS UpdateProjectArea$$

create procedure UpdateProjectArea (
	i_session	bigint unsigned,
	i_areaid	int unsigned,
	i_resp		int unsigned,
	i_name		varchar(80), 
	i_target	date,
	i_price		int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1510 into @err;
end;

call ValidateAreaSuperUser(i_session, i_areaid, v_me);
if @err = 0 then
	update projectareas set 
	edited=now(), editedby=v_me, name=i_name, 
	due=i_target, responsible=i_resp, price=i_price
	where areaid=i_areaid;

	if row_count() < 1 then
		select -1511 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS ProjectAreaComplete$$

create procedure ProjectAreaComplete (
	i_session	bigint unsigned,
	i_areaid	int unsigned,
	i_date		date
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1520 into @err;
end;

call ValidateAreaSuperUser(i_session, i_areaid, v_me);
if @err = 0 then
	update projectareas set 
	edited=now(), editedby=v_me, complete=i_date
	where areaid=i_areaid;

	if row_count() < 1 then
		select -1521 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS DeleteProjectArea$$

create procedure DeleteProjectArea (
	i_session	bigint unsigned,
	i_areaid	int unsigned
) begin

declare v_me int unsigned;
declare v_cnt int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1530 into @err;
end;

call ValidateAreaSuperUser(i_session, i_areaid, v_me);

if @err = 0 then
	select count(*) into v_cnt from usertime where areaid=i_areaid;
	if v_cnt > 0 then
		select -1532 into @err;
	end if;
end if;
if @err = 0 then
	select count(*) into v_cnt from tasks where areaid=i_areaid and (paid is not null or cost is not null);
	if v_cnt > 0 then
		select -1534 into @err;
	end if;
end if;
if @err = 0 then
	select count(*) into v_cnt from usertime u, tasks t where u.taskid=t.taskid and t.areaid=i_areaid;
	if v_cnt > 0 then
		select -1533 into @err;
	end if;
end if;
if @err = 0 then

	delete from taskfiles where taskid in
		(select taskid from tasks where areaid=i_areaid);
	delete from taskmessages where noteid in
		(select n.noteid from tasks t, tasknotes n where t.taskid=n.taskid and t.areaid=i_areaid);
	delete from tasknotes where taskid in
		(select taskid from tasks where areaid=i_areaid);
	delete from tasks where areaid=i_areaid and paid is null;
	delete from projectareas where areaid=i_areaid;

	if row_count() < 1 then
		select -1531 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS MergeProjectAreas$$

create procedure MergeProjectAreas (
	i_session	bigint unsigned,
	i_srcarea	int unsigned,
	i_destarea	int unsigned
) begin

declare v_me int unsigned;
declare v_cnt int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1540 into @err;
end;

call ValidateAreaSuperUser(i_session, i_srcarea, v_me);

if @err = 0 then
	update tasks set areaid=i_destarea where areaid=i_srcarea;
	delete from projectareas where areaid=i_srcarea;

	if row_count() < 1 then
		select -1541 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS CreateProject$$

create procedure CreateProject (
	i_session	bigint unsigned,
	i_orgid		int unsigned,
	i_name		varchar(80), 
	i_started	date, 
	i_target	date,
	i_priority	tinyint unsigned,
	i_status	char(1), 
	i_timerpt	tinyint,
	i_notes		text
) begin

declare v_me int unsigned;
declare v_cnt int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1100 into @err;
end;

call ValidateOrgSuperUser(i_session, 0, v_me);
if @err = 0 then
	select count(*) into v_cnt from projects where orgid=i_orgid and name=i_name;

	if v_cnt > 0 then
		select -1102 into @err;
	else
		insert into projects (
			edited, editedby, dateedited, dateeditedby, orgid,
			name, started, targetdate, priority, status, timerpt, notes
		) values (
			now(), v_me, now(), v_me, i_orgid,
			i_name, i_started, i_target, i_priority, i_status, i_timerpt, i_notes
		);

		if row_count() < 1 then
			select -1101 into @err;
		else
			select last_insert_id() into @prjid;
		end if;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS UpdateProject$$

create procedure UpdateProject (
	i_session	bigint unsigned,
	i_prjid		int unsigned,
	i_orgid		int unsigned,
	i_name		varchar(80), 
	i_priority	tinyint unsigned,
	i_status	char(1), 
	i_timerpt	tinyint,
	i_notes		text
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1110 into @err;
end;

call ValidateProjectSuperUser(i_session, i_prjid, v_me);
if @err = 0 then
	update projects set 
	edited=now(), editedby=v_me, orgid=i_orgid, name=i_name, 
	priority=i_priority, status=i_status, timerpt=i_timerpt, notes=i_notes
	where prjid=i_prjid;

	if row_count() < 1 then
		select -1111 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS UpdateProjectDates$$

create procedure UpdateProjectDates (
	i_session	bigint unsigned,
	i_prjid		int unsigned,
	i_started	date, 
	i_target	date,
	i_comp		date
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1120 into @err;
end;

call ValidateProjectSuperUser(i_session, i_prjid, v_me);
if @err = 0 then
	update projects set 
	dateedited=now(), dateeditedby=v_me, 
	started=i_started, targetdate=i_target, completed=i_comp
	where prjid=i_prjid;

	if row_count() < 1 then
		select -1121 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS UpdateProjectDefaults$$

create procedure UpdateProjectDefaults (
	i_session	bigint unsigned,
	i_prjid		int unsigned,
	i_priority	tinyint unsigned, 
	i_assnto	int unsigned,
	i_apprby	int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1140 into @err;
end;

call ValidateProjectSuperUser(i_session, i_prjid, v_me);
if @err = 0 then
	update projects set 
	defedited=now(), defeditedby=v_me, 
	defpriority=i_priority, defassignedto=i_assnto, defapprovedby=i_apprby
	where prjid=i_prjid;

	if row_count() < 1 then
		select -1141 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS AddProjectLink$$

create procedure AddProjectLink (
	i_session	bigint unsigned,
	i_prjid		int unsigned,
	i_name		varchar(100), 
	i_url		varchar(100)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1150 into @err;
end;

if i_name is null or i_url is null then
	select -1151 into @err;
end if;

call ValidateProjectSuperUser(i_session, i_prjid, v_me);
if @err = 0 then
	insert into projectlinks (prjid, linkname, url) values (i_prjid, i_name, i_url);

	if row_count() < 1 then
		select -1152 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS UpdateProjectLink$$

create procedure UpdateProjectLink (
	i_session	bigint unsigned,
	i_linkid	int unsigned,
	i_name		varchar(100), 
	i_url		varchar(100)
) begin

declare v_me int unsigned;
declare v_prjid int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1160 into @err;
end;

select prjid into v_prjid from projectlinks where linkid=i_linkid;

call ValidateProjectSuperUser(i_session, v_prjid, v_me);
if @err = 0 then
	if i_name is null or i_url is null then
		delete from projectlinks where linkid=i_linkid;
	else
		update projectlinks set linkname=i_name, url=i_url
		where linkid=i_linkid;
	end if;

	if row_count() < 1 then
		select -1161 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS DeleteProject$$

create procedure DeleteProject (
	i_session	bigint unsigned,
	i_prjid		int unsigned
) begin

declare v_me int unsigned;
declare v_cnt int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1130 into @err;
end;

call ValidateOrgSuperUser(i_session, 0, v_me);

if @err = 0 then
	select count(*) into v_cnt from usertime where prjid=i_prjid;
	if v_cnt > 0 then
		select -1132 into @err;
	else

		delete from tasknotes where taskid in
			(select taskid from projectareas a, tasks t where a.prjid=i_prjid and t.areaid=a.areaid);
		delete from taskfiles where taskid in
			(select taskid from projectareas a, tasks t where a.prjid=i_prjid and t.areaid=a.areaid);
		delete from tasks where areaid in
			(select areaid from projectareas where prjid=i_prjid);
		delete from milestones where prjid=i_prjid;
		delete from projectlinks where prjid=i_prjid;
		delete from projectareas where prjid=i_prjid;
		delete from projectusers where prjid=i_prjid;
		delete from projects where prjid=i_prjid;

		if row_count() < 1 then
			select -1131 into @err;
		end if;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS CreateSubscription$$

create procedure CreateSubscription (
	i_name	varchar(80),
	i_first	varchar(20),
	i_last	varchar(20),
	i_init	varchar(4),
	i_email	varchar(100),
	i_pwd	varchar(64),
	i_salt	varchar(64),
	i_qstn	varchar(80),
	i_ans	varchar(80)
) begin

declare v_orgid int unsigned;
declare v_userid int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1700 into @err;
end;

select count(*) into v_orgid from subscription;
if v_orgid > 0 then
	select -1705 into @err;
else
	start transaction;

	select 0 into @err;

	insert into subscription (name) values (i_name);

	if row_count() < 1 then
		select -1701 into @err;
	else
		select last_insert_id() into @subid;
	end if;

	if @err = 0 then
		insert into organizations (edited, status, name)
		values (now(), 1, i_name);

		if row_count() < 1 then
			select -1702 into @err;
		else
			select last_insert_id() into v_orgid;
		end if;
	end if;

	if @err = 0 then
		update subscription set orgid=v_orgid where subscr=@subid;

		if row_count() < 1 then
			select -1703 into @err;
		end if;
	end if;

	if @err = 0 then
		insert into users (
			edited, nameedited, privedited, orgid, superuser, usermaint, status,
			email, password, salt, secqstn, secans, firstname, lastname, initials)
		select
			now(), now(), now(), v_orgid, 1, 1, min(statusid),
			i_email, i_pwd, i_salt, i_qstn, i_ans, i_first, i_last, i_init
		from userstatus;

		if row_count() < 1 then
			select -1704 into @err;
		else
			select last_insert_id() into v_userid;
		end if;
	end if;

	if @err = 0 then
		update users set nameeditedby=v_userid, priveditedby=v_userid, editedby=v_userid
		where userid=v_userid;

		update organizations set editedby=v_userid where orgid=v_orgid;
	end if;

	if @err = 0 then
		commit;
	else
		rollback;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS CompleteTask$$

create procedure CompleteTask (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned,
	i_descr		text
) begin

declare v_me int unsigned;
declare v_cnt int unsigned;
declare v_assnto int unsigned;
declare v_note bigint unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -2000 into @err;
end;

select assignedto into v_assnto from tasks where taskid=i_taskid;

call ValidateTaskSuperUser(i_session, i_taskid, v_me);

if @err != 0 and v_me > 0 and v_assnto = v_me then
	select 0 into @err;
end if;

if @err = 0 then
	start transaction;

	update tasks set complete=curdate(), assignedto=v_me where taskid=i_taskid;
	if row_count() < 1 then
		select -2001 into @err;
	end if;

	if @err = 0 then
		insert into tasknotes (taskid, senton, fromid, msgtype, message)
		values (i_taskid, now(), v_me, 4, i_descr);

		select last_insert_id() into v_note;

		insert into taskmessages (noteid, toid)
		select distinct v_note, u.userid
		from tasks a, users u 
		where a.taskid=i_taskid and u.userid!=v_me
		and (a.submittedby=u.userid or a.approvedby=u.userid)
		and (u.notifydone='P' or u.notifydone='B');
	end if;

	if @err = 0 then
		commit;
	else
		rollback;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS ApproveTask$$

create procedure ApproveTask (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned,
	i_descr		text
) begin

declare v_me int unsigned;
declare v_cnt int unsigned;
declare v_appr int unsigned;
declare v_note bigint unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -2010 into @err;
end;

select approvedby into v_appr from tasks where taskid=i_taskid;

call ValidateTaskApproveRights(i_session, i_taskid, v_me);

if @err = 0 then
	start transaction;

	update tasks set complete=curdate() where taskid=i_taskid and complete is null;
	update tasks set approved=curdate(), approvedby=v_me where taskid=i_taskid;
	if row_count() < 1 then
		select -2011 into @err;
	end if;

	if @err = 0 then
		insert into tasknotes (taskid, senton, fromid, msgtype, message)
		values (i_taskid, now(), v_me, 6, i_descr);

		select last_insert_id() into v_note;

		insert into taskmessages (noteid, toid)
		select distinct v_note, u.userid
		from tasks a, users u 
		where a.taskid=i_taskid and u.userid!=v_me
		and a.submittedby=u.userid and (u.notifyappr='P' or u.notifyappr='B');
	end if;

	if @err = 0 then
		commit;
	else
		rollback;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS DisapproveTask$$

create procedure DisapproveTask (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned,
	i_descr		text
) begin

declare v_me int unsigned;
declare v_cnt int unsigned;
declare v_note bigint unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -2020 into @err;
end;

call ValidateTaskApproveRights(i_session, i_taskid, v_me);

if @err = 0 then
	start transaction;

	update tasks set complete=null, approved=null where taskid=i_taskid;
	if row_count() < 1 then
		select -2021 into @err;
	end if;

	if @err = 0 then
		insert into tasknotes (taskid, senton, fromid, msgtype, message)
		values (i_taskid, now(), v_me, 5, i_descr);

		select last_insert_id() into v_note;

		insert into taskmessages (noteid, toid)
		select distinct v_note, u.userid
		from tasks a, users u 
		where a.taskid=i_taskid and u.userid!=v_me
		and a.assignedto=u.userid and (u.notifyrej='P' or u.notifyrej='B');

		commit;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS ReleaseTask$$

create procedure ReleaseTask (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned
) begin

declare v_me int unsigned;
declare v_cnt int unsigned;
declare v_appr int unsigned;
declare v_note bigint unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -2030 into @err;
end;

select approvedby into v_appr from tasks where taskid=i_taskid;

call ValidateTaskReleaseRights(i_session, i_taskid, v_me);

if @err = 0 then
	start transaction;

	update tasks set released=curdate(), releasedby=v_me where taskid=i_taskid;
	if row_count() < 1 then
		select -2031 into @err;
	end if;

	if @err = 0 then
		insert into tasknotes (taskid, senton, fromid, msgtype)
		values (i_taskid, now(), v_me, 9);

		commit;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS UnreleaseTask$$

create procedure UnreleaseTask (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -2040 into @err;
end;

call ValidateTaskReleaseRights(i_session, i_taskid, v_me);

if @err = 0 then
	start transaction;

	update tasks set released=null, releasedby=null where taskid=i_taskid;
	if row_count() < 1 then
		select -2041 into @err;
	end if;

	if @err = 0 then
		insert into tasknotes (taskid, senton, fromid, msgtype)
		values (i_taskid, now(), v_me, 10);

		commit;
	end if;

end if;

end$$

DROP PROCEDURE IF EXISTS CreateTask$$

create procedure CreateTask (
	i_session	bigint unsigned,
	i_prjid		int unsigned,
	i_areaid	int unsigned,
	i_status	int unsigned,
	i_priority	tinyint unsigned,
	i_name		varchar(80), 
	i_startms	int unsigned,
	i_endms		int unsigned,
	i_starton	date,
	i_endby		date,
	i_assnto	int unsigned,
	i_apprby	int unsigned,
	i_descr		text,
	i_cost		int
) begin

declare v_me int unsigned;
declare v_note bigint unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1700 into @err;
end;

call ValidateAddTaskRights(i_session, i_areaid, v_me);
if @err = 0 then
	start transaction;

	insert into tasks (
		prjid, areaid, status, edited, editedby, priority, name, submittedon,
		assignedto, submittedby, approvedby,
		startafter, needby, startmilestone, endmilestone, cost
	) values (
		i_prjid, i_areaid, i_status, now(), v_me, i_priority, i_name, now(),
		i_assnto, v_me, i_apprby,
		i_starton, i_endby, i_startms, i_endms, i_cost
	);

	if row_count() < 1 then
		select -1701 into @err;
	else
		select last_insert_id() into @taskid;
	end if;

	if @err = 0 and i_descr is not null then
		insert into tasknotes (taskid, senton, fromid, msgtype, message)
		values (@taskid, now(), v_me, 11, i_descr);

		if row_count() < 1 then
			select -1705 into @err;
		end if;
	end if;

	if @err = 0 then
		commit;
	else
		rollback;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS EditTask$$

create procedure EditTask (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned,
	i_areaid	int unsigned,
	i_status	int unsigned,
	i_priority	tinyint unsigned,
	i_name		varchar(80)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	select -1710 into @err;
end;

call ValidateTaskEditRights(i_session, i_taskid, v_me);

if @err = 0 then
	if i_name is null then
		update tasks set edited=now(), editedby=v_me,
		areaid=i_areaid, status=i_status, priority=i_priority
		where taskid=i_taskid;
	else
		update tasks set edited=now(), editedby=v_me,
		areaid=i_areaid, status=i_status, priority=i_priority, name=i_name
		where taskid=i_taskid;
	end if;

	if row_count() < 1 then
		select -1711 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS EditTaskDates$$

create procedure EditTaskDates (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned,
	i_start		date,
	i_finish	date
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	select -1720 into @err;
end;

call ValidateTaskEditRights(i_session, i_taskid, v_me);

if @err = 0 then
	update tasks set datesedited=now(), dateseditedby=v_me,
	startafter=i_start, needby=i_finish
	where taskid=i_taskid;

	if row_count() < 1 then
		select -1721 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS EditTaskMilestones$$

create procedure EditTaskMilestones (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned,
	i_start		int unsigned,
	i_finish	int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	select -1730 into @err;
end;

call ValidateTaskEditRights(i_session, i_taskid, v_me);

if @err = 0 then
	update tasks set datesedited=now(), dateseditedby=v_me,
	startmilestone=i_start, endmilestone=i_finish
	where taskid=i_taskid;

	if row_count() < 1 then
		select -1731 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS EditTaskAssignment$$

create procedure EditTaskAssignment (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned,
	i_role		varchar(1),
	i_userid	int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	select -1740 into @err;
end;

call ValidateAnyTaskRights(i_session, i_taskid, v_me);

if @err = 0 then
	if i_role='T' then
		update tasks set assignedto=i_userid where taskid=i_taskid;
		insert into tasknotes (taskid, senton, fromid, msgtype, targetuser)
		values (i_taskid, now(), v_me, 7, i_userid);
	elseif i_role='S' then
		update tasks set submittedby=i_userid where taskid=i_taskid;
		insert into tasknotes (taskid, senton, fromid, msgtype, targetuser)
		values (i_taskid, now(), v_me, 9, i_userid);
	elseif i_role='A' then
		update tasks set approvedby=i_userid where taskid=i_taskid;
		insert into tasknotes (taskid, senton, fromid, msgtype, targetuser)
		values (i_taskid, now(), v_me, 8, i_userid);
	end if;

end if;

end$$

DROP PROCEDURE IF EXISTS DeleteTask$$

create procedure DeleteTask (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned
) begin

declare v_me int unsigned;
declare v_cnt int unsigned;

declare exit handler for sqlexception begin
	select -1750 into @err;
end;

call ValidateTaskEditRights(i_session, i_taskid, v_me);

if @err = 0 then
	select count(*) into v_cnt from usertime where taskid=i_taskid;
	if v_cnt > 0 then
		select -1752 into @err;
	end if;
end if;

if @err = 0 then
	update tasks set removed=now(), removedby=v_me where taskid=i_taskid;
	if row_count() < 1 then
		select -1751 into @err;
	else
		insert into tasknotes (taskid, senton, fromid, msgtype)
		values (i_taskid, now(), v_me, 12);
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS UndeleteTask$$

create procedure UndeleteTask (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned
) begin

declare v_me int unsigned;
declare v_cnt int unsigned;

declare exit handler for sqlexception begin
	select -1770 into @err;
end;

call ValidateTaskEditRights(i_session, i_taskid, v_me);

if @err = 0 then
	select count(*) into v_cnt from usertime where taskid=i_taskid;
	if v_cnt > 0 then
		select -1772 into @err;
	end if;
end if;

if @err = 0 then
	update tasks set removed=null, removedby=null where taskid=i_taskid;
	if row_count() < 1 then
		select -1771 into @err;
	else
		insert into tasknotes (taskid, senton, fromid, msgtype)
		values (i_taskid, now(), v_me, 13);
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS EditTaskCost$$

create procedure EditTaskCost (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned,
	i_cost		int
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	select -1760 into @err;
end;

call ValidateTaskSuperUser(i_session, i_taskid, v_me);

if @err = 0 then
	update tasks set costedited=now(), costeditedby=v_me, cost=i_cost
	where taskid=i_taskid;

	if row_count() < 1 then
		select -1761 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS TaskNotPaid$$

create procedure TaskNotPaid (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	select -1780 into @err;
end;

call ValidateTaskSuperUser(i_session, i_taskid, v_me);

if @err = 0 then
	update tasks set paid=null where taskid=i_taskid;

	if row_count() < 1 then
		select -1781 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS TaskNotBilled$$

create procedure TaskNotBilled (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	select -1782 into @err;
end;

call ValidateTaskSuperUser(i_session, i_taskid, v_me);

if @err = 0 then
	update tasks set billed=null where taskid=i_taskid;

	if row_count() < 1 then
		select -1783 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS TaskIsBilled$$

create procedure TaskIsBilled (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	select -1784 into @err;
end;

call ValidateTaskSuperUser(i_session, i_taskid, v_me);

if @err = 0 then
	update tasks set billed=now() where taskid=i_taskid;

	if row_count() < 1 then
		select -1785 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS AddTaskFile$$

create procedure AddTaskFile (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned,
	i_name		varchar(100),
	i_type		varchar(10),
	i_descr		varchar(200),
	i_file		longblob
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1900 into @err;
end;

call ValidateTaskEditRights(i_session, i_taskid, v_me);

if @err = 0 then
	insert into taskfiles (taskid, uploadedon, uploadedby, filename, imagetype, descr, contents)
	values (i_taskid, now(), v_me, i_name, i_type, i_descr, i_file);

	if row_count() < 1 then
		select -1901 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS EditTaskFile$$

create procedure EditTaskFile (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned,
	i_fileid	int unsigned,
	i_descr		varchar(200)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1910 into @err;
end;

call ValidateAnyTaskRights(i_session, i_taskid, v_me);

if @err = 0 then
	update taskfiles set descr=i_descr where fileid=i_fileid and taskid=i_taskid;

	if row_count() < 1 then
		select -1911 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS DeleteTaskFile$$

create procedure DeleteTaskFile (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned,
	i_fileid	int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1920 into @err;
end;

call ValidateAnyTaskRights(i_session, i_taskid, v_me);

if @err = 0 then
	delete from taskfiles where fileid=i_fileid and taskid=i_taskid;

	if row_count() < 1 then
		select -1921 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS AddTaskNote$$

create procedure AddTaskNote (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned,
	i_to		int unsigned,
	i_subject	varchar(100),
	i_descr		text
) begin

declare v_me int unsigned;
declare v_note bigint unsigned;
declare v_send varchar(1);

declare exit handler for sqlexception begin
	rollback;
	select -1800 into @err;
end;

call ValidateAnyTaskRights(i_session, i_taskid, v_me);

if @err = 0 and i_descr is not null then
	start transaction;

	insert into tasknotes (taskid, senton, fromid, subject, message)
	values (i_taskid, now(), v_me, i_subject, i_descr);

	select last_insert_id() into v_note;
	if v_note > 0 then
		if i_to is not null then
			select notifymsg into v_send from users where userid=i_to;

			if v_send = 'P' or v_send = 'B' then
				insert into taskmessages (noteid, toid) values (v_note, i_to);

				if row_count() < 1 then
					select -1802 into @err;
				end if;
			end if;
		end if;
	else
		select -1801 into @err;
	end if;

	if @err = 0 then
		commit;
	else
		rollback;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS EditTaskNote$$

create procedure EditTaskNote (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned,
	i_noteid	int unsigned,
	i_descr		text
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1810 into @err;
end;

call ValidateAnyTaskRights(i_session, i_taskid, v_me);

if @err = 0 then
	if i_descr is null then
		delete from tasknotes where noteid=i_noteid and taskid=i_taskid;
	else
		update tasknotes set message=i_descr where noteid=i_noteid and taskid=i_taskid;
	end if;

	if row_count() < 1 then
		select -1811 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS DeleteTaskNote$$

create procedure DeleteTaskNote (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned,
	i_noteid	int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1820 into @err;
end;

call ValidateAnyTaskRights(i_session, i_taskid, v_me);

if @err = 0 then
	delete from taskmessages where noteid=i_noteid;
	delete from tasknotes where noteid=i_noteid;

	if row_count() < 1 then
		select -1821 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS TaskMessageSeen$$

create procedure TaskMessageSeen (
	i_session	bigint unsigned,
	i_noteid	bigint unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1830 into @err;
end;

call ValidateUser(i_session, v_me);

if @err = 0 then
	update taskmessages set seenon=now()
	where noteid=i_noteid and toid=v_me;
end if;

end$$

DROP PROCEDURE IF EXISTS ClockInToProject$$

create procedure ClockInToProject (
	i_session	bigint unsigned,
	i_prjid		int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -2110 into @err;
end;

call ValidateUser(i_session, v_me);

if @err = 0 then
	if i_prjid is null then
		select -2111 into @err;
	else
		update usertime set endon=now()
		where endon is null and userid in
		(select userid from usersession where sessionid=i_session);

		insert into usertime (userid, starton, prjid)
		select userid, now(), i_prjid
		from usersession where sessionid=i_session and expireson>now();

		if row_count() < 1 then
			select -2112 into @err;
		end if;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS ClockInToArea$$

create procedure ClockInToArea (
	i_session	bigint unsigned,
	i_area		int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -2120 into @err;
end;

call ValidateUser(i_session, v_me);

if @err = 0 then
	if i_area is null then
		select -2121 into @err;
	else
		update usertime set endon=now()
		where endon is null and userid in
		(select userid from usersession where sessionid=i_session);

		insert into usertime (userid, starton, areaid)
		select userid, now(), i_area
		from usersession where sessionid=i_session and expireson>now();

		if row_count() < 1 then
			select -2122 into @err;
		end if;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS ClockInToTask$$

create procedure ClockInToTask (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -2130 into @err;
end;

call ValidateUser(i_session, v_me);

if @err = 0 then
	if i_taskid is null then
		select -2131 into @err;
	else
		update usertime set endon=now()
		where endon is null and userid in
		(select userid from usersession where sessionid=i_session);

		insert into usertime (userid, starton, taskid)
		select userid, now(), i_taskid
		from usersession where sessionid=i_session and expireson>now();

		if row_count() < 1 then
			select -2132 into @err;
		end if;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS ClockOut$$

create procedure ClockOut (
	i_session	bigint unsigned,
	i_pwd		varchar(64),
	i_note		text
) begin

declare v_userid int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -2100 into @err;
end;

select u.userid into v_userid
from users u, usersession s
where s.sessionid=i_session and s.userid=u.userid and u.password=i_pwd;

if v_userid is null then
	select -2101 into @err;
else
	update usertime set endon=now(), comment=i_note
	where userid=v_userid and endon is null;
end if;

end$$

DROP PROCEDURE IF EXISTS RecordTime$$

create procedure RecordTime (
	i_session	bigint unsigned,
	i_userid	int unsigned,
	i_prjid		int unsigned,
	i_starton	datetime,
	i_endon		datetime,
	i_note		text
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -2140 into @err;
end;

call ValidateOrgSuperUser(i_session, 0, v_me);

if @err = 0 then
	if i_prjid is null or i_userid is null then
		select -2141 into @err;
	else
		insert into usertime (userid, starton, endon, prjid, comment)
		values (i_userid, i_starton, i_endon, i_prjid, i_note);

		if row_count() < 1 then
			select -2412 into @err;
		end if;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS AdjustTime$$

create procedure AdjustTime (
	i_session	bigint unsigned,
	i_timeid	int unsigned,
	i_adjust	decimal(3,1)
) begin

declare v_me int unsigned;
declare v_time decimal;

declare exit handler for sqlexception begin
	rollback;
	select -2150 into @err;
end;

call ValidateOrgSuperUser(i_session, 0, v_me);

if @err = 0 then
	select round(time_to_sec(timediff(ifnull(endon,now()),starton))/3600,1) into v_time
	from usertime where timeid=i_timeid;

	if v_time <  i_adjust then
		select v_time into i_adjust;
	end if;

	update usertime set adjustment=i_adjust, adjustedby=v_me
	where timeid=i_timeid;

	if row_count() < 1 then
		select -2151 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS AdjustTimeProject$$

create procedure AdjustTimeProject (
	i_session	bigint unsigned,
	i_timeid	int unsigned,
	i_prjid		int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -2160 into @err;
end;

call ValidateOrgSuperUser(i_session, 0, v_me);

if @err = 0 then

	update usertime set prjid=i_prjid where timeid=i_timeid;

	if row_count() < 1 then
		select -2161 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS AdminClockOut$$

create procedure AdminClockOut (
	i_session	bigint unsigned,
	i_timeid	int unsigned,
	i_adjust	time
) begin

declare v_me int unsigned;
declare v_end datetime;
declare v_start datetime;

declare exit handler for sqlexception begin
	rollback;
	select -2170 into @err;
end;

call ValidateOrgSuperUser(i_session, 0, v_me);

if @err = 0 then

	select starton, endon into v_start, v_end from usertime where timeid=i_timeid;

	if v_start is null then
		select -2171 into @err;
	else
		if v_end is null then
			update usertime set endon=addtime(starton, i_adjust), adjustment=null, adjustedby=v_me
			where timeid=i_timeid;
		else
			update usertime set adjustedby=v_me,
			adjustment=time_to_sec(timediff(timediff(endon,starton),i_adjust))/3600
			where timeid=i_timeid;
		end if;
	end if;

end if;

end$$

drop procedure if exists ResetUserAaccount$$
create procedure ResetUserAaccount (
	i_session	bigint unsigned,
	i_userid	int unsigned,
	i_password	varchar(64),
	i_salt		varchar(64)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1000 into @err;
end;

call ValidateUserMaintOverUser(i_session, i_userid, v_me);

if @err = 0 then
	UPDATE users SET password=i_password, salt=i_salt
	WHERE userid=i_userid;

	if row_count() < 1 then
		select -1001 into @err;
	end if;
end if;

end$$

drop procedure if exists ResetMyAaccount$$
create procedure ResetMyAaccount (
	i_session	bigint unsigned,
	i_password	varchar(64),
	i_salt		varchar(64)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1070 into @err;
end;

call ValidateUser(i_session, v_me);

if @err = 0 then
	UPDATE users SET password=i_password, salt=i_salt
	WHERE userid=v_me;

	if row_count() < 1 then
		select -1071 into @err;
	end if;
end if;

end$$

drop procedure if exists MyPreferences$$
create procedure MyPreferences (
	i_session	bigint unsigned,
	i_defuser	tinyint,
	i_new	varchar(1),
	i_done	varchar(1),
	i_appr	varchar(1),
	i_rej	varchar(1),
	i_msg	varchar(1)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1080 into @err;
end;

call ValidateUser(i_session, v_me);

if @err = 0 then
	UPDATE users SET defuser=i_defuser, notifynew=i_new, notifydone=i_done, notifyappr=i_appr, notifyrej=i_rej, notifymsg=i_msg
	WHERE userid=v_me;

	if row_count() < 1 then
		select -1081 into @err;
	end if;
end if;

end$$

drop procedure if exists DeactivateUserAccount$$
create procedure DeactivateUserAccount (
	i_session	bigint unsigned,
	i_userid	int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1010 into @err;
end;

call ValidateUserMaintOverUser(i_session, i_userid, v_me);

if @err = 0 then
	UPDATE users SET password=null, salt=null
	WHERE userid=i_userid;

	if row_count() < 1 then
		select -1011 into @err;
	end if;
end if;

end$$

drop procedure if exists CreateUser$$
create procedure CreateUser (
	i_session	bigint unsigned,
	i_orgid		int unsigned, 
	i_status	int unsigned, 
	i_firstname	varchar(20),
	i_lastname	varchar(20),
	i_initials	varchar(4),
	i_email		varchar(100)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1020 into @err;
end;

call ValidateUserMaintRights(i_session, i_orgid, v_me);

if @err = 0 then

	insert into users (
		edited, editedby, nameedited, nameeditedby, privedited, priveditedby,
		orgid, status, firstname, lastname, initials, email)
	values (
		now(), v_me, now(), v_me, now(), v_me, 
		i_orgid, i_status, i_firstname, i_lastname, i_initials, i_email
	);

	if row_count() < 1 then
		select -1021 into @err;
	else
		select last_insert_id() into @userid;
	end if;
end if;
end$$

drop procedure if exists UpdateUser$$
create procedure UpdateUser (
	i_session	bigint unsigned,
	i_userid	int unsigned,
	i_orgid		int unsigned, 
	i_status	int unsigned, 
	i_firstname	varchar(20), 
	i_lastname	varchar(20), 
	i_initials	varchar(20),
	i_email		varchar(100)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1030 into @err;
end;

call ValidateUserMaintOverUser(i_session, i_userid, v_me);

if @err = 0 then
	update users set nameedited=now(), nameeditedby=v_me, orgid=i_orgid, status=i_status,
	initials=i_initials, firstname=i_firstname, lastname=i_lastname, email=i_email
	where userid=i_userid;

	if row_count() < 1 then
		select -1031 into @err;
	end if;
end if;
end$$

drop procedure if exists UpdateMyUser$$
create procedure UpdateMyUser (
	i_session	bigint unsigned,
	i_firstname	varchar(20), 
	i_lastname	varchar(20), 
	i_initials	varchar(20),
	i_email		varchar(100)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1050 into @err;
end;

call ValidateUser(i_session, v_me);

if @err = 0 then
	update users set nameedited=now(), nameeditedby=v_me, 
	initials=i_initials, firstname=i_firstname, lastname=i_lastname, email=i_email
	where userid=v_me;

	if row_count() < 1 then
		select -1051 into @err;
	end if;
end if;
end$$

drop procedure if exists UpdateUserRate$$
create procedure UpdateUserRate (
	i_session	bigint unsigned,
	i_userid	int unsigned,
	i_payrate	smallint
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1040 into @err;
end;

call ValidateOrgSuperUser(i_session, 1, v_me);

if @err = 0 then
	update users set edited=now(), editedby=v_me, payrate=i_rate 
	where userid=i_userid;

	if row_count() < 1 then
		select -1041 into @err;
	end if;
end if;
end$$

drop procedure if exists UpdateUserField$$
create procedure UpdateUserField (
	i_session	bigint unsigned,
	i_userid	int unsigned,
	i_fieldid	int unsigned, 
	i_value		varchar(80)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1060 into @err;
end;

call ValidateUserMaintOverUser(i_session, i_userid, v_me);

if @err = 0 then
	start transaction;
	delete from userfields where userid=i_userid and fieldid=i_fieldid;

	if i_value is not null then
		insert into userfields (userid, fieldid, edited, editedby, value)
		values (i_userid, i_fieldid, now(), v_me, i_value);

		if row_count() < 1 then
			rollback;
			select -1061 into @err;
		end if;
	end if;
	commit;
end if;
end$$

/*
* UserSession.sql
* Sessions for logging in, renewing the session and validating session rights
*/

DROP procedure IF EXISTS CreateUserSession$$

CREATE procedure CreateUserSession (
	i_email	varchar(100),
	i_pwd	varchar(64)
) begin

declare v_userid int unsigned;

declare exit handler for sqlexception begin
	select 0 into @sessionid;
	select -200 into @err;
end;

select 0 into @err;

select userid into v_userid
from users where email=i_email and password=i_pwd;

if found_rows() > 0 then

	delete from usersession where userid=v_userid or expireson <= now();

	insert into usersession (userid, expireson, orgid) 
	select userid, date_add(now(),INTERVAL 4 hour), orgid
	from users
	where userid=v_userid;

	if row_count() < 1 then
		select -201 into @err;
	else
		select last_insert_id() into @sessionid;
	end if;
else
	select -202 into @err;
end if;

end$$

DROP procedure IF EXISTS CloseUserSession$$

CREATE procedure CloseUserSession (
    i_sid bigint unsigned
) begin

delete from usersession where session_id=i_sid;

end$$


/*
* RenewUserSession
* The main function of renewing the user session it to:
* a) make sure that the session has not expired
* b) bump the expiration date back so that the session does not expire.
*/
DROP procedure IF EXISTS RenewUserSession$$

CREATE procedure RenewUserSession (
    i_sid bigint unsigned
) begin

declare v_cnt int;

declare exit handler for sqlexception begin
	select 0 into @sessionid;
	select -210 into @err;
end;

select 0 into @err;

delete from usersession where expireson<now();

update usersession set expireson=date_add(now(),INTERVAL 2 hour) where sessionid=i_sid;

select count(*) into v_cnt from usersession where sessionid=i_sid;

if v_cnt < 1 then
	select -211 into @err;
end if;

end$$

DROP PROCEDURE IF EXISTS SetGlobalRights$$

create procedure SetGlobalRights (
	i_session	bigint unsigned,
	i_userid	int unsigned,
	i_superuser tinyint,
	i_usermaint tinyint
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1300 into @err;
end;

call ValidateUserMaintOverUser(i_session, i_userid, v_me);

if @err = 0 then
	update users set superuser=i_superuser, usermaint=i_usermaint 
		where userid=i_userid;
	if row_count() < 1 then
		select -1301 into @err;
	else
		if i_superuser is not null then
			delete from projectusers where userid=i_userid;
		end if;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS AddUserToProject $$

create procedure AddUserToProject (
	i_session	bigint unsigned,
	i_userid	int unsigned,
	i_prjid		int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1310 into @err;
end;

call ValidateProjectSuperUser(i_session, i_prjid, v_me);

if @err = 0 then
	insert into projectusers (edited, editedby, userid, prjid) 
	values (now(), v_me, i_userid, i_prjid);
	if row_count() < 1 then
		select -1311 into @err;
	end if;
end if;
end$$

DROP PROCEDURE IF EXISTS RemoveUserFromProject $$

create procedure RemoveUserFromProject (
	i_session	bigint unsigned,
	i_userid	int unsigned,
	i_prjid		int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1320 into @err;
end;

call ValidateProjectSuperUser(i_session, i_prjid, v_me);

if @err = 0 then
	delete from projectusers where userid=i_userid and prjid=i_prjid;
	if row_count() < 1 then
		select -1321 into @err;
	end if;
end if;
end$$


DROP PROCEDURE IF EXISTS UpdateProjectUser$$

create procedure UpdateProjectUser (
	i_session	bigint unsigned,
	i_userid	int unsigned,
	i_prjid		int unsigned,
	i_submit	tinyint unsigned,
	i_approve	tinyint unsigned,
	i_assign	tinyint unsigned,
	i_assignto	tinyint unsigned,
	i_edit		tinyint unsigned,
	i_release	tinyint unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1330 into @err;
end;

call ValidateProjectSuperUser(i_session, i_prjid, v_me);

if @err = 0 then
	update projectusers set 
	edited=now(), editedby=v_me, superuser=null, approval=i_approve, 
	submit=i_submit, assign=i_assign, assigned=i_assignto, edit=i_edit, publish=i_release
	where prjid=i_prjid and userid=i_userid;
	if row_count() < 1 then
		select -1331 into @err;
	end if;
end if;
end$$


DROP PROCEDURE IF EXISTS ProjectSuperUser$$

create procedure ProjectSuperUser (
	i_session	bigint unsigned,
	i_userid	int unsigned,
	i_prjid		int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1340 into @err;
end;

call ValidateProjectSuperUser(i_session, i_prjid, v_me);

if @err = 0 then
	update projectusers set 
	edited=now(), editedby=v_me, superuser=1, 
	approval=null, submit=null, assign=null, assigned=null, edit=null
	where prjid=i_prjid and userid=i_userid;
	if row_count() < 1 then
		select -1341 into @err;
	end if;
end if;
end$$
