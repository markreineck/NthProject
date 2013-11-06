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
