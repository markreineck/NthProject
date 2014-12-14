
DROP PROCEDURE IF EXISTS PurgeTime$$

create procedure PurgeTime (
	i_session	bigint unsigned,
	i_date		date
) begin

declare v_me int unsigned;
declare v_days int;

declare exit handler for sqlexception begin
	rollback;
	select -2400 into @err;
end;

call ValidateOrgSuperUser(i_session, 0, v_me);

if @err = 0 then
	select datediff(curdate(), i_date) into v_days;

	if v_days < 365 then
		select -2401 into @err;
	else

		delete from usertime where endon<i_date;

		if row_count() < 1 then
			select -2402 into @err;
		end if;
	end if;
end if;

end$$
