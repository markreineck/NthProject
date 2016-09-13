/*
alter table users add mobileid int unsigned comment 'Unencrypted mobile id number toi identify the device';
alter table users add mobilekey varchar(64) comment 'Mobile encryption key';
alter table users add mobilecode varchar(64) comment 'Mobile device security code';
*/
alter table subscription add defaulttaskstatus int unsigned;

-- 4/20/2014
alter table users drop timezone;
alter table users add payrate int comment 'Hourly pay rate in cents';

-- 8/31/2016
alter table tasks areaid null;
alter table tasks add branch varchar(40);
alter table tasks drop foreign key tasks_area;