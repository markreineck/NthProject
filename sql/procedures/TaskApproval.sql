
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
