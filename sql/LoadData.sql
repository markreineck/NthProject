
insert into orgstatus values (0, 'Inactive');
insert into orgstatus values (1, 'Active');

insert into userstatus values (0, null, 'Inactive');
insert into userstatus values (1, null, 'Active');

insert into taskstatus values (1, null, 'Waiting');
insert into taskstatus values (2, null, 'Active');
insert into taskstatus values (3, 1, 'Need Info');
insert into taskstatus values (4, 1, 'Hold');

insert into fielddef values (1, 'S', 30, false, 'Contact');
insert into fielddef values (101, 'S', 10, false, 'Phone');

call CreateSubscription ('Nth Generation', 1, 1, 1, 
	'Mark', 'Reineck', 'MR', 'mark@nth-generation.com', 
	'nth', null, 'Pet''s name', 'Zeus');
