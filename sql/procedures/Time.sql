delimiter $$

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

