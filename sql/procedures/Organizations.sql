
DROP PROCEDURE IF EXISTS CreateCompany$$

create procedure CreateCompany (
	i_session	bigint unsigned,
	i_status	int unsigned,
	i_name		varchar(80)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1200 into @err;
end;

call ValidateOrgSuperUser(i_session, 0, v_me);
if @err = 0 then
	insert into organizations (edited, editedby, status, name)
	values (now(), v_me, i_status, i_name);

	if row_count() < 1 then
		select -1201 into @err;
	else
		select last_insert_id() into @orgid;
	end if;
end if;

end$$



DROP PROCEDURE IF EXISTS UpdateOrganization$$

create procedure UpdateOrganization (
	i_session	bigint unsigned,
	i_orgid		int unsigned,
	i_status	int unsigned,
	i_name		varchar(80)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1210 into @err;
end;

call ValidateOrgSuperUser(i_session, 1, v_me);
if @err = 0 then
	if i_status = 0 then
		select null into i_status;
	end if;

	update organizations set 
	edited=now(), editedby=v_me, status=i_status, name=i_name
	where orgid=i_orgid;

	if row_count() < 1 then
		select -1211 into @err;
	end if;
end if;

end$$


drop procedure if exists UpdateOrgField$$
create procedure UpdateOrgField (
	i_session	bigint unsigned,
	i_orgid		int unsigned,
	i_fieldid	int unsigned, 
	i_value		varchar(80)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1220 into @err;
end;

call ValidateOrgSuperUser(i_session, i_orgid, v_me);

if @err = 0 then
	start transaction;
	delete from organizationfields where orgid=i_orgid and fieldid=i_fieldid;

	if i_value is not null then
		insert into organizationfields (orgid, fieldid, edited, editedby, value)
		values (i_orgid, i_fieldid, now(), v_me, i_value);

		if row_count() < 1 then
			rollback;
			select -1221 into @err;
		end if;
	end if;
	commit;
end if;
end$$

Must add a check that if you are deleting the owner org then another org has been designated the new owner
DROP PROCEDURE IF EXISTS DeleteOrganization$$

create procedure DeleteOrganization (
	i_session	bigint unsigned,
	i_orgid		int unsigned
) begin

declare v_me int unsigned;
declare v_cnt int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1230 into @err;
end;

call ValidateOrgSuperUser(i_session, 1, v_me);

if @err = 0 then
	select count(*) into v_cnt from users where orgid=i_orgid;
	if v_cnt > 0 then
		select -1233 into @err;
	end if;
end if;

if @err = 0 then
	select count(*) into v_cnt from projects where orgid=i_orgid;
	if v_cnt > 0 then
		select -1232 into @err;
	end if;
end if;

if @err = 0 then
	delete from organizations where orgid=i_orgid;

	if row_count() < 1 then
		select -1231 into @err;
	end if;
end if;

end$$
