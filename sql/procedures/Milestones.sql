DROP PROCEDURE IF EXISTS CreateMilestone$$

create procedure CreateMilestone (
	i_session	bigint unsigned,
	i_prjid		int unsigned,
	i_name		varchar(80), 
	i_target	date,
	i_descr		text
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1600 into @err;
end;

call ValidateProjectSuperUser(i_session, i_prjid, v_me);
if @err = 0 then
	insert into milestones(prjid, edited, editedby, name, targetdate, descr) 
	values (i_prjid, now(), v_me, i_name, i_target, i_descr);

	if row_count() < 1 then
		select -1601 into @err;
	end if;
end if;

end$$



DROP PROCEDURE IF EXISTS UpdateMilestone$$

create procedure UpdateMilestone (
	i_session	bigint unsigned,
	i_msid		int unsigned,
	i_name		varchar(80), 
	i_target	date,
	i_comp		date,
	i_descr		text
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1610 into @err;
end;

call ValidateMilestoneSuperUser(i_session, i_msid, v_me);
if @err = 0 then
	update milestones set edited=now(), editedby=v_me, name=i_name, targetdate=i_target, completion=i_comp, descr=i_descr
	where milestoneid=i_msid;

	if row_count() < 1 then
		select -1611 into @err;
	end if;
end if;

end$$



DROP PROCEDURE IF EXISTS MilestoneComplete$$

create procedure MilestoneComplete (
	i_session	bigint unsigned,
	i_msid		int unsigned,
	i_date		date
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1620 into @err;
end;

call ValidateMilestoneSuperUser(i_session, i_msid, v_me);
if @err = 0 then
	update milestones set 
	edited=now(), completion=i_date, completedby=v_me
	where milestoneid=i_msid;

	if row_count() < 1 then
		select -1621 into @err;
	end if;
end if;

end$$



DROP PROCEDURE IF EXISTS DeleteMilestone$$

create procedure DeleteMilestone (
	i_session	bigint unsigned,
	i_msid		int unsigned
) begin

declare v_me int unsigned;

declare exit handler for sqlexception begin
	rollback;
	select -1630 into @err;
end;

call ValidateMilestoneSuperUser(i_session, i_msid, v_me);
if @err = 0 then
	update task set startmilestone=null where startmilestone=-i_msid;
	update task set endmilestone=null where endmilestone=-i_msid;

	delete from milestones where milestoneid=i_msid;

	if row_count() < 1 then
		select -1631 into @err;
	end if;
end if;

end$$