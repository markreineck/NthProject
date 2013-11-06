
drop procedure if exists ActivateMobile$$
create procedure ActivateMobile (
	i_userid	int unsigned,
	i_mobileid	int unsigned,
	i_mobcode	varchar(64)
) begin

declare v_id varchar(64);
declare v_code varchar(64);
declare v_key varchar(64);

declare exit handler for sqlexception begin
	rollback;
	select -2200 into @err;
end;

select mobileid, mobilekey, mobilecode int v_id, v_code, v_key 
from users WHERE userid=i_userid;

if v_id is not null or v_code is not null then
	select -2202 into @err;
elseif v_key is null then
	select -2203 into @err;
else
	UPDATE users SET mobileid=i_mobileid, mobilecode=i_mobcode
	WHERE userid=i_userid and mobilekey is not null and mobileid is null and mobilecode is null;

	if row_count() < 1 then
		select -2201 into @err;
	end if;
end if;

end$$


drop procedure if exists SaveMyMobileKey$$
create procedure SaveMyMobileKey (
	i_session	bigint unsigned,
	i_key		varchar(64)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -2210 into @err;
end;

call ValidateUser(i_session, v_me);

if @err = 0 then
	UPDATE users SET mobilekey=i_key, mobileid=null, mobilecode=null
	WHERE userid=v_me;

	if row_count() < 1 then
		select -2211 into @err;
	end if;
end if;

end$$

drop procedure if exists DeactivateMyMobile$$
create procedure DeactivateMyMobile (
	i_session	bigint unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -2220 into @err;
end;

call ValidateUserMaintOverUser(i_session, i_userid, v_me);

if @err = 0 then
	UPDATE users SET mobilekey=null, mobileid=null, mobilecode=null
	WHERE userid=v_me;

	if row_count() < 1 then
		select -2221 into @err;
	end if;
end if;

end$$

drop procedure if exists DeactivateMobile$$
create procedure DeactivateMobile (
	i_session	bigint unsigned,
	i_userid	int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -2220 into @err;
end;

call ValidateUserMaintOverUser(i_session, i_userid, v_me);

if @err = 0 then
	UPDATE users SET mobilekey=null, mobileid=null, mobilecode=null
	WHERE userid=i_userid;

	if row_count() < 1 then
		select -2221 into @err;
	end if;
end if;

end$$