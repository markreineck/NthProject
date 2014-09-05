delimiter $$

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
			edited, editedby, dateedited, dateeditedby, defedited, defeditedby, 
			orgid, name, started, targetdate, priority, status, timerpt, notes
		) values (
			now(), v_me, now(), v_me, now(), v_me, 
			i_orgid, i_name, i_started, i_target, i_priority, i_status, i_timerpt, i_notes
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