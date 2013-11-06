/*
* UserSession.sql
* Sessions for logging in, renewing the session and validating session rights
*/

DROP procedure IF EXISTS CreateUserSession$$

CREATE procedure CreateUserSession (
	i_email	varchar(100),
	i_pwd	varchar(64)
) begin

declare v_userid int unsigned;

declare exit handler for sqlexception begin
	select 0 into @sessionid;
	select -200 into @err;
end;

select 0 into @err;

select userid into v_userid
from users where email=i_email and password=i_pwd;

if found_rows() > 0 then

	delete from usersession where userid=v_userid or expireson <= now();

	insert into usersession (userid, expireson, orgid) 
	select userid, date_add(now(),INTERVAL 4 hour), orgid
	from users
	where userid=v_userid;

	if row_count() < 1 then
		select -201 into @err;
	else
		select last_insert_id() into @sessionid;
	end if;
else
	select -202 into @err;
end if;

end$$



DROP procedure IF EXISTS CloseUserSession$$

CREATE procedure CloseUserSession (
    i_sid bigint unsigned
) begin

delete from usersession where session_id=i_sid;

end$$


/*
* RenewUserSession
* The main function of renewing the user session it to:
* a) make sure that the session has not expired
* b) bump the expiration date back so that the session does not expire.
*/
DROP procedure IF EXISTS RenewUserSession$$

CREATE procedure RenewUserSession (
    i_sid bigint unsigned
) begin

declare v_cnt int;

declare exit handler for sqlexception begin
	select 0 into @sessionid;
	select -210 into @err;
end;

select 0 into @err;

delete from usersession where expireson<now();

update usersession set expireson=date_add(now(),INTERVAL 2 hour) where sessionid=i_sid;

select count(*) into v_cnt from usersession where sessionid=i_sid;

if v_cnt < 1 then
	select -211 into @err;
end if;

end$$

