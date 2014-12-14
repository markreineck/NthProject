delimiter $$

DROP PROCEDURE IF EXISTS CreateSubscription$$

create procedure CreateSubscription (
	i_name	varchar(80),
	i_first	varchar(20),
	i_last	varchar(20),
	i_init	varchar(4),
	i_email	varchar(100),
	i_pwd	varchar(64),
	i_salt	varchar(64),
	i_qstn	varchar(80),
	i_ans	varchar(80)
) begin

declare v_orgid int unsigned;
declare v_userid int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -2300 into @err;
end;

select count(*) into v_orgid from subscription;
if v_orgid > 0 then
	select -2305 into @err;
else
	start transaction;

	select 0 into @err;

	insert into subscription (name) values (i_name);

	if row_count() < 1 then
		select -2301 into @err;
	else
		select last_insert_id() into @subid;
	end if;

	if @err = 0 then
		insert into organizations (edited, status, name)
		values (now(), 1, i_name);

		if row_count() < 1 then
			select -2302 into @err;
		else
			select last_insert_id() into v_orgid;
		end if;
	end if;

	if @err = 0 then
		update subscription set orgid=v_orgid where subscr=@subid;

		if row_count() < 1 then
			select -2303 into @err;
		end if;
	end if;

	if @err = 0 then
		insert into users (
			edited, nameedited, privedited, prefedited, orgid, superuser, usermaint, status,
			email, password, salt, secqstn, secans, firstname, lastname, initials)
		select
			now(), now(), now(), now(), v_orgid, 1, 1, min(statusid),
			i_email, i_pwd, i_salt, i_qstn, i_ans, i_first, i_last, i_init
		from userstatus;

		if row_count() < 1 then
			select -2304 into @err;
		else
			select last_insert_id() into v_userid;
		end if;
	end if;

	if @err = 0 then
		update users set nameeditedby=v_userid, priveditedby=v_userid, editedby=v_userid
		where userid=v_userid;

		update organizations set editedby=v_userid where orgid=v_orgid;
	end if;

	if @err = 0 then
		commit;
	else
		rollback;
	end if;
end if;

end$$
