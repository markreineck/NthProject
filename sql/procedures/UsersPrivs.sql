
DROP PROCEDURE IF EXISTS SetGlobalRights$$

create procedure SetGlobalRights (
	i_session	bigint unsigned,
	i_userid	int unsigned,
	i_superuser tinyint,
	i_usermaint tinyint
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1300 into @err;
end;

call ValidateUserMaintOverUser(i_session, i_userid, v_me);

if @err = 0 then
	update users set superuser=i_superuser, usermaint=i_usermaint 
		where userid=i_userid;
	if row_count() < 1 then
		select -1301 into @err;
	else
		if i_superuser is not null then
			delete from projectusers where userid=i_userid;
		end if;
	end if;
end if;

end$$


DROP PROCEDURE IF EXISTS AddUserToProject $$

create procedure AddUserToProject (
	i_session	bigint unsigned,
	i_userid	int unsigned,
	i_prjid		int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1310 into @err;
end;

call ValidateProjectSuperUser(i_session, i_prjid, v_me);

if @err = 0 then
	insert into projectusers (edited, editedby, userid, prjid) 
	values (now(), v_me, i_userid, i_prjid);
	if row_count() < 1 then
		select -1311 into @err;
	end if;
end if;
end$$


DROP PROCEDURE IF EXISTS RemoveUserFromProject $$

create procedure RemoveUserFromProject (
	i_session	bigint unsigned,
	i_userid	int unsigned,
	i_prjid		int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1320 into @err;
end;

call ValidateProjectSuperUser(i_session, i_prjid, v_me);

if @err = 0 then
	delete from projectusers where userid=i_userid and prjid=i_prjid;
	if row_count() < 1 then
		select -1321 into @err;
	end if;
end if;
end$$


DROP PROCEDURE IF EXISTS UpdateProjectUser$$

create procedure UpdateProjectUser (
	i_session	bigint unsigned,
	i_userid	int unsigned,
	i_prjid		int unsigned,
	i_submit	tinyint unsigned,
	i_approve	tinyint unsigned,
	i_assign	tinyint unsigned,
	i_assignto	tinyint unsigned,
	i_edit		tinyint unsigned,
	i_release	tinyint unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1330 into @err;
end;

call ValidateProjectSuperUser(i_session, i_prjid, v_me);

if @err = 0 then
	update projectusers set 
	edited=now(), editedby=v_me, superuser=null, approval=i_approve, 
	submit=i_submit, assign=i_assign, assigned=i_assignto, edit=i_edit, publish=i_release
	where prjid=i_prjid and userid=i_userid;
	if row_count() < 1 then
		select -1331 into @err;
	end if;
end if;
end$$


DROP PROCEDURE IF EXISTS ProjectSuperUser$$

create procedure ProjectSuperUser (
	i_session	bigint unsigned,
	i_userid	int unsigned,
	i_prjid		int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1340 into @err;
end;

call ValidateProjectSuperUser(i_session, i_prjid, v_me);

if @err = 0 then
	update projectusers set 
	edited=now(), editedby=v_me, superuser=1, 
	approval=null, submit=null, assign=null, assigned=null, edit=null
	where prjid=i_prjid and userid=i_userid;
	if row_count() < 1 then
		select -1341 into @err;
	end if;
end if;
end$$