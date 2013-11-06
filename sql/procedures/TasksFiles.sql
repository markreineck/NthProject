
DROP PROCEDURE IF EXISTS AddTaskFile$$

create procedure AddTaskFile (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned,
	i_name		varchar(100),
	i_type		varchar(10),
	i_descr		varchar(200),
	i_file		longblob
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1900 into @err;
end;

call ValidateTaskEditRights(i_session, i_taskid, v_me);

if @err = 0 then
	insert into taskfiles (taskid, uploadedon, uploadedby, filename, imagetype, descr, contents)
	values (i_taskid, now(), v_me, i_name, i_type, i_descr, i_file);

	if row_count() < 1 then
		select -1901 into @err;
	end if;
end if;

end$$


DROP PROCEDURE IF EXISTS EditTaskFile$$

create procedure EditTaskFile (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned,
	i_fileid	int unsigned,
	i_descr		varchar(200)
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1910 into @err;
end;

call ValidateAnyTaskRights(i_session, i_taskid, v_me);

if @err = 0 then
	update taskfiles set descr=i_descr where fileid=i_fileid and taskid=i_taskid;

	if row_count() < 1 then
		select -1911 into @err;
	end if;
end if;

end$$


DROP PROCEDURE IF EXISTS DeleteTaskFile$$

create procedure DeleteTaskFile (
	i_session	bigint unsigned,
	i_taskid	bigint unsigned,
	i_fileid	int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1920 into @err;
end;

call ValidateAnyTaskRights(i_session, i_taskid, v_me);

if @err = 0 then
	delete from taskfiles where fileid=i_fileid and taskid=i_taskid;

	if row_count() < 1 then
		select -1921 into @err;
	end if;
end if;

end$$
