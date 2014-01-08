DROP PROCEDURE IF EXISTS SetDefaultTaskStatus$$

create procedure SetDefaultTaskStatus (
	i_ses	bigint unsigned,
	i_id	int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	select -1460 into @err;
end;

call ValidateOrgSuperUser(i_ses, 0, v_me);

if @err = 0 then
update subscription set defaulttaskstatus=i_id;

if row_count() < 1 then
	select -1461 into @err;
end if;
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
declare v_cnt int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1450 into @err;
end;

call ValidateUserMaintRights(i_ses, 0, v_me);

select count(*) into v_cnt from users where status=i_id;
if v_cnt > 0 then
	select -1452 into @err;
else

	delete from userstatus where statusid=i_id;

	if row_count() < 1 then
		select -1451 into @err;
	end if;
end if;

end$$

DROP PROCEDURE IF EXISTS CreateOrgType$$

create procedure CreateOrgType (
	i_ses	bigint unsigned,
	i_name	varchar(40)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1460 into @err;
end;

call ValidateUserMaintRights(i_ses, 0, v_me);

insert into orgstatus (name) values (i_name);

if row_count() < 1 then
	select -1461 into @err;
end if;

end$$


DROP PROCEDURE IF EXISTS UpdateOrgType$$

create procedure UpdateOrgType (
	i_ses	bigint unsigned,
	i_id	int unsigned,
	i_name	varchar(40)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1470 into @err;
end;

call ValidateUserMaintRights(i_ses, 0, v_me);

update orgstatus set name=i_name where statusid=i_id;

if row_count() < 1 then
	select -1471 into @err;
end if;

end$$


DROP PROCEDURE IF EXISTS DeleteOrgType$$

create procedure DeleteOrgType (
	i_ses	bigint unsigned,
	i_id	int unsigned
) begin

declare v_me int unsigned;
declare v_cnt int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1480 into @err;
end;

call ValidateUserMaintRights(i_ses, 0, v_me);

select count(*) into v_cnt from organizations where status=i_id;
if v_cnt > 0 then
	select -1482 into @err;
else

	delete from orgstatus where statusid=i_id;

	if row_count() < 1 then
		select -1481 into @err;
	end if;
end if;

end$$
