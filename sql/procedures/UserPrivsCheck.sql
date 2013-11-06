
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

