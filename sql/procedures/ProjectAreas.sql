
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