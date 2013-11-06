
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
