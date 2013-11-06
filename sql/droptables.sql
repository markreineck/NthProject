update organizations set editedby=null;
update users set editedby=null, nameeditedby=null, priveditedby=null, prefeditedby=null;
ALTER TABLE users drop FOREIGN KEY users_editby;
ALTER TABLE users drop FOREIGN KEY users_nameeditby;
ALTER TABLE users drop FOREIGN KEY users_privseditby;
ALTER TABLE users drop FOREIGN KEY users_prefeditedby;
ALTER TABLE organizations drop FOREIGN KEY organizations_editby;

drop TABLE IF EXISTS usertime;
drop TABLE IF EXISTS taskfiles;
drop TABLE IF EXISTS tasknotes;
drop TABLE IF EXISTS taskpages;
drop TABLE IF EXISTS tasks;
drop TABLE IF EXISTS projectareas;
drop TABLE IF EXISTS projectusers;
drop TABLE IF EXISTS milestones;
drop TABLE IF EXISTS projects;
drop TABLE IF EXISTS userfields;
drop TABLE IF EXISTS usersession;
drop TABLE IF EXISTS organizationfields;
drop TABLE IF EXISTS users;
drop TABLE IF EXISTS organizations;
drop TABLE IF EXISTS fielddef;
drop TABLE IF EXISTS orgstatus;
drop TABLE IF EXISTS userstatus;
drop TABLE IF EXISTS subscription;


