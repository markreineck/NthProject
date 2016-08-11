delimiter $$

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
		i_assnto, v_me, ifnull(i_apprby,v_me),
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

/*
	delete from taskfiles where taskid=i_taskid;
	delete from taskmessages where noteid in
		(select noteid from tasknotes where taskid=i_taskid);
	delete from tasknotes where taskid=i_taskid;
	delete from tasks where taskid=i_taskid;
*/
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

