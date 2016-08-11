delimiter $$

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
	i_namemode	varchar(1),
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
	UPDATE users SET defuser=i_defuser, namemode=i_namemode,
		notifynew=i_new, notifydone=i_done, notifyappr=i_appr, notifyrej=i_rej, notifymsg=i_msg
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
	i_email		varchar(100),
	i_payrate	float
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1030 into @err;
end;

call ValidateUserMaintOverUser(i_session, i_userid, v_me);

if @err = 0 then
	update users set nameedited=now(), nameeditedby=v_me, orgid=i_orgid,
	initials=i_initials, firstname=i_firstname, lastname=i_lastname, 
	status=i_status, email=i_email, payrate=i_payrate 
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
DROP PROCEDURE IF EXISTS DeleteUser$$

create procedure DeleteUser(
	i_userid	int(11),
	i_assnto	int(11),
	i_submitby	int(11),
	i_approvby	int(11)
) begin 

declare v_cnt int;

	call ValidateUserMaintOverUser(i_userid);
	if @err = 0 then

		if i_assnto is not null then
			update task set assignedto=$assigned where assignedto=i_assnto;
		end if;
		if i_submitby is not null then
			update task set submittedby=$submitted where submittedby=i_submitby;
		end if;
		if i_approvby is not null then
			update task set approvedby=$approval where approvedby=i_approvby;
		end if;

		update projectareas set contractor=null where contractor=i_userid;
		update projects set defassignedto=null where defassignedto=i_userid;
		update projects set defapprovedby=null where defapprovedby=i_userid;
		update projects set contact=null where contact=i_userid;
		update task set submittedby=null where submittedby=i_userid;
		update task set assignedto=null where assignedto=i_userid;
		update task set approvedby=null where approvedby=i_userid;
		update taskfiles set uploadedby=null where uploadedby=i_userid;
		update time set adjustedby=null where adjustedby=i_userid;
		update time set addby=null where addby=i_userid;
		delete from projectusers where userid=i_userid;
		delete from usersession where person=i_userid;

		select count(*) into v_cnt from time where userid=i_userid;
		if v_cnt>0 then
			select -200 into @err;
			select 'The user that you are attempting to remove has time records.' into @errmsg;
		else
			delete from users where userid=i_userid;
		end if;
	end if;
end$$
*/