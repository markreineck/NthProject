--
-- Table structure for table contractors
--

CREATE TABLE IF NOT EXISTS subscription (
	subscr int unsigned NOT NULL AUTO_INCREMENT,
	orgid int unsigned,
	name varchar(80),
	defaulttaskstatus int unsigned,
	PRIMARY KEY (subscr)
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table fielddef
--

CREATE TABLE IF NOT EXISTS fielddef (
	fieldid int unsigned NOT NULL AUTO_INCREMENT,
	datatype varchar(4) NOT NULL COMMENT 'S=string, Y=boolean',
	fieldtype varchar(1) NOT NULL COMMENT 'U=User, O=Organization, P=Project, T=Task',
	maxlen tinyint unsigned,
	required tinyint unsigned,
	name varchar(40) NOT NULL,
	PRIMARY KEY (fieldid),
	UNIQUE KEY fielddef_name (name)
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table orgstatus
--

CREATE TABLE IF NOT EXISTS orgstatus (
	statusid int unsigned NOT NULL AUTO_INCREMENT,
	name varchar(40) NOT NULL,
	PRIMARY KEY (statusid),
	UNIQUE KEY orgstatus_name (name)
) ENGINE=InnoDB;


-- --------------------------------------------------------

--
-- Table structure for table userstatus
--

CREATE TABLE IF NOT EXISTS userstatus (
	statusid int unsigned NOT NULL AUTO_INCREMENT,
	paytype varchar(1) COMMENT 'H=hourly, P=per project, null=not paid',
	name varchar(40) NOT NULL,
	PRIMARY KEY (statusid),
	UNIQUE KEY userstatus_name (name)
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table taskstatus
--

CREATE TABLE IF NOT EXISTS taskstatus (
	statusid int unsigned NOT NULL AUTO_INCREMENT,
	hold tinyint,
	name varchar(40) NOT NULL,
	PRIMARY KEY (statusid),
	UNIQUE KEY orgstatus_name (name)
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table organizations
--

CREATE TABLE IF NOT EXISTS organizations (
	orgid int unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary ID for every organization entry',
	edited datetime NOT NULL,
	editedby int unsigned,
	status int unsigned,
	name varchar(80) NOT NULL COMMENT 'name of the organization',
	PRIMARY KEY (orgid),
	UNIQUE KEY org_name (name)
) ENGINE=InnoDB;

ALTER TABLE organizations
	ADD CONSTRAINT organizations_status FOREIGN KEY (status) REFERENCES orgstatus (statusid);

ALTER TABLE subscription
	ADD CONSTRAINT subscription_owner FOREIGN KEY (orgid) REFERENCES organizations (orgid);

-- --------------------------------------------------------

--
-- Table structure for table users
--

CREATE TABLE IF NOT EXISTS users (
	userid int unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary ID of each user entry',
	orgid int unsigned NOT NULL COMMENT 'organization where the users project belongs',
	nameedited datetime NOT NULL,
	nameeditedby int unsigned,
	privedited datetime NOT NULL,
	priveditedby int unsigned,
	prefedited datetime NOT NULL,
	prefeditedby int unsigned,
	edited datetime NOT NULL,
	editedby int unsigned,
	initials varchar(4) NOT NULL COMMENT 'initials of the name of the user',
	firstname varchar(20) NOT NULL COMMENT 'firstname of the user',
	lastname varchar(20) NOT NULL COMMENT 'lastname of the user',
	email varchar(100) COMMENT 'username of the user in logging in',
	superuser tinyint unsigned COMMENT 'the administrators',
	usermaint tinyint unsigned COMMENT 'organization where the administrator project belongs',
	status int unsigned COMMENT 'status of the user, null=inactive',
	namemode tinyint,
	notify tinyint,
	defuser tinyint,
	defprj int unsigned,
	payrate tinyint,
	password varchar(64) COMMENT 'username of the user in logging in',
	salt varchar(64)  COMMENT 'username of the user in logging in',
	secqstn varchar(80) NOT NULL,
	secans varchar(80) NOT NULL,
	PRIMARY KEY (userid),
	UNIQUE KEY users_uniquename (orgid,lastname,firstname),
	UNIQUE KEY users_initials (orgid,initials),
	UNIQUE KEY users_email (email)
) ENGINE=InnoDB;

ALTER TABLE users
	ADD CONSTRAINT users_editby FOREIGN KEY (editedby) REFERENCES users (userid),
	ADD CONSTRAINT users_nameeditby FOREIGN KEY (nameeditedby) REFERENCES users (userid),
	ADD CONSTRAINT users_privseditby FOREIGN KEY (priveditedby) REFERENCES users (userid),
	ADD CONSTRAINT users_prefeditedby FOREIGN KEY (prefeditedby) REFERENCES users (userid),
	ADD CONSTRAINT users_status FOREIGN KEY (status) REFERENCES userstatus (statusid),
	ADD CONSTRAINT users_org FOREIGN KEY (orgid) REFERENCES organizations (orgid);

ALTER TABLE organizations
	ADD CONSTRAINT organizations_editby FOREIGN KEY (editedby) REFERENCES users (userid);

-- --------------------------------------------------------

--
-- Table structure for table organizationfields
--

CREATE TABLE IF NOT EXISTS organizationfields (
	orgid int unsigned COMMENT 'primary ID for every organization entry',
	fieldid int unsigned NOT NULL,
	edited datetime NOT NULL,
	editedby int unsigned,
	value varchar(80) NOT NULL,
	PRIMARY KEY (orgid, fieldid)
) ENGINE=InnoDB;

ALTER TABLE organizationfields
	ADD CONSTRAINT organizationfields_editby FOREIGN KEY (editedby) REFERENCES users (userid),
	ADD CONSTRAINT organizationfields_org FOREIGN KEY (orgid) REFERENCES organizations (orgid),
	ADD CONSTRAINT organizationfields_field FOREIGN KEY (fieldid) REFERENCES fielddef (fieldid);

-- --------------------------------------------------------

--
-- Table structure for table userfields
--

CREATE TABLE IF NOT EXISTS userfields (
	userid int unsigned COMMENT 'primary ID for every user entry',
	fieldid int unsigned NOT NULL,
	edited datetime NOT NULL,
	editedby int unsigned,
	value varchar(80) NOT NULL,
	PRIMARY KEY (userid, fieldid)
) ENGINE=InnoDB;

ALTER TABLE userfields
	ADD CONSTRAINT userfields_editby FOREIGN KEY (editedby) REFERENCES users (userid),
	ADD CONSTRAINT userfields_org FOREIGN KEY (userid) REFERENCES users (userid),
	ADD CONSTRAINT userfields_field FOREIGN KEY (fieldid) REFERENCES fielddef (fieldid);

-- --------------------------------------------------------

--
-- Table structure for table session
--

CREATE TABLE IF NOT EXISTS usersession (
	sessionid bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary ID of every session entry',
	userid int unsigned NOT NULL,
	orgid int unsigned NOT NULL,
	loggedin timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'time the employee logs in',
	expireson datetime NOT NULL COMMENT 'time the employee logs out',
	PRIMARY KEY (sessionid)
) ENGINE=InnoDB;

ALTER TABLE usersession
	ADD CONSTRAINT session_organization FOREIGN KEY (orgid) REFERENCES organizations (orgid),
	ADD CONSTRAINT session_user FOREIGN KEY (userid) REFERENCES users (userid);


	-- --------------------------------------------------------

--
-- Table structure for table projects
--

CREATE TABLE IF NOT EXISTS projects (
	prjid int unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary id for every project entry',
	orgid int unsigned NOT NULL COMMENT 'foreign key for the organization table orgid field',
	edited datetime NOT NULL,
	editedby int unsigned,
	dateedited datetime NOT NULL,
	dateeditedby int unsigned,
	defedited datetime NOT NULL,
	defeditedby int unsigned,
	name varchar(80) NOT NULL COMMENT 'name of the project',
	started date COMMENT 'date when the project started',
	completed date COMMENT 'date of completion of the project',
	targetdate date,
	priority tinyint unsigned NOT NULL COMMENT 'priority rating of the project',
	status char(1) NOT NULL COMMENT 'A=active, I=inactive',
	defpriority tinyint unsigned COMMENT 'default priority of the project',
	defassignedto int unsigned COMMENT 'default person assigned to the project',
	defapprovedby int unsigned COMMENT 'default person that approves the project',
	billed date COMMENT 'date of billing',
	contact int unsigned COMMENT 'contact person of the project',
	timerpt tinyint COMMENT '1=by project, 2=by area, 3= by task',
	notes text,
	PRIMARY KEY (prjid),
	UNIQUE KEY projects_name (orgid,name)
) ENGINE=InnoDB;

ALTER TABLE projects
	ADD CONSTRAINT projects_editby FOREIGN KEY (editedby) REFERENCES users (userid),
	ADD CONSTRAINT projects_dateeditedby FOREIGN KEY (dateeditedby) REFERENCES users (userid),
	ADD CONSTRAINT projects_defeditedby FOREIGN KEY (defeditedby) REFERENCES users (userid),
	ADD CONSTRAINT projects_contact FOREIGN KEY (contact) REFERENCES users (userid),
	ADD CONSTRAINT projects_assignto FOREIGN KEY (defassignedto) REFERENCES users (userid),
	ADD CONSTRAINT projects_approveby FOREIGN KEY (defapprovedby) REFERENCES users (userid),
	ADD CONSTRAINT projects_org FOREIGN KEY (orgid) REFERENCES organizations (orgid);


-- --------------------------------------------------------

--
-- Table structure for table projectareas
--

CREATE TABLE IF NOT EXISTS projectareas (
	areaid int unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary ID for every project area entry',
	prjid int unsigned NOT NULL COMMENT 'foreign key for access in the projects table of the prjid field',
	edited datetime NOT NULL,
	editedby int unsigned,
	name varchar(80) NOT NULL,
	responsible int unsigned,
	due date COMMENT 'date of deadline for completion for every project',
	completed date COMMENT 'date of completion for every project',
	price int unsigned COMMENT 'estimated price for the project working on',
	paid date COMMENT 'date of billing for every projects',
	PRIMARY KEY (areaid),
	UNIQUE KEY projectareas_index (prjid,name)
) ENGINE=InnoDB;

ALTER TABLE projectareas
	ADD CONSTRAINT projectareas_editby FOREIGN KEY (editedby) REFERENCES users (userid),
	ADD CONSTRAINT projectareas_responsible FOREIGN KEY (responsible) REFERENCES users (userid),
	ADD CONSTRAINT projectareas_prjid FOREIGN KEY (prjid) REFERENCES projects (prjid);

-- --------------------------------------------------------

--
-- Table structure for table projectusers
--

CREATE TABLE IF NOT EXISTS projectusers (
	userid int unsigned NOT NULL COMMENT 'primary ID of project user entry',
	prjid int unsigned NOT NULL COMMENT 'foreign key access to the project table prjid field',
	edited datetime NOT NULL,
	editedby int unsigned,
	superuser tinyint unsigned,
	approval tinyint unsigned,
	submit tinyint unsigned,
	assigned tinyint unsigned,
	assign tinyint unsigned,
	edit tinyint unsigned,
	publish tinyint unsigned,
	PRIMARY KEY (userid,prjid)
) ENGINE=InnoDB;

ALTER TABLE projectusers
	ADD CONSTRAINT projectusers_editby FOREIGN KEY (editedby) REFERENCES users (userid),
	ADD CONSTRAINT projectusers_project FOREIGN KEY (prjid) REFERENCES projects (prjid),
	ADD CONSTRAINT projectusers_user FOREIGN KEY (userid) REFERENCES users (userid);

-- --------------------------------------------------------

--
-- Table structure for table projectfiles
--

CREATE TABLE IF NOT EXISTS projectfiles (
	fileid bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary ID for every task file',
	prjid int unsigned NOT NULL COMMENT 'foreign key access to the project table prjid field',
	uploadedon datetime NOT NULL,
	uploadedby int unsigned NOT NULL,
	filename varchar(100) NOT NULL,
	imagetype varchar(10) NOT NULL,
	descr varchar(200) NOT NULL,
	contents longblob NOT NULL,
	PRIMARY KEY (fileid)
) ENGINE=InnoDB;

ALTER TABLE projectfiles
	ADD CONSTRAINT projectfiles_project FOREIGN KEY (prjid) REFERENCES projects (prjid),
	ADD CONSTRAINT projectfiles_user FOREIGN KEY (uploadedby) REFERENCES users (userid);



-- --------------------------------------------------------

--
-- Table structure for table projectfiles
--

CREATE TABLE IF NOT EXISTS projectlinks (
	linkid bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary ID for every task file',
	prjid int unsigned NOT NULL COMMENT 'foreign key access to the project table prjid field',
	linkname varchar(100) NOT NULL,
	url varchar(100) NOT NULL,
	PRIMARY KEY (linkid)
) ENGINE=InnoDB;

ALTER TABLE projectlinks
	ADD CONSTRAINT projectlinks_project FOREIGN KEY (prjid) REFERENCES projects (prjid);


-- --------------------------------------------------------

--
-- Table structure for table milestones
--

CREATE TABLE IF NOT EXISTS milestones (
	milestoneid int unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key for each milestone entry',
	prjid int unsigned NOT NULL COMMENT 'foreign key for the projects_prjid table',
	edited datetime NOT NULL,
	editedby int unsigned,
	completion date,
	targetdate date,
	name varchar(80) NOT NULL,
	completedby int unsigned,
	descr text,
	PRIMARY KEY (milestoneid),
	UNIQUE KEY milestone_name (prjid,name)
) ENGINE=InnoDB;

ALTER TABLE milestones
	ADD CONSTRAINT milestones_completedby FOREIGN KEY (completedby) REFERENCES users (userid),
	ADD CONSTRAINT milestones_editby FOREIGN KEY (editedby) REFERENCES users (userid),
	ADD CONSTRAINT milestones_project FOREIGN KEY (prjid) REFERENCES projects (prjid);





-- --------------------------------------------------------

--
-- Table structure for table tasks
--

CREATE TABLE IF NOT EXISTS tasks (
	taskid bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary ID for every task entry',
	prjid int unsigned NOT NULL COMMENT 'foreign key for the projects_prjid table',
	areaid int unsigned NOT NULL COMMENT 'foreign key access to the projectareas table areaid field',
	status int unsigned NOT NULL,
	edited datetime NOT NULL,
	editedby int unsigned,
	datesedited datetime NOT NULL,
	dateseditedby int unsigned,
	costedited datetime,
	costeditedby int unsigned,
	removed datetime,
	removedby int unsigned,
	assignedto int unsigned,
	submittedby int unsigned,
	approvedby int unsigned,
	releasedby int unsigned,
	priority tinyint unsigned NOT NULL COMMENT 'priority of the task',
	name varchar(80) NOT NULL,
	submittedon datetime,
	startafter date,
	needby date,
	startmilestone int unsigned COMMENT 'milestone started',
	endmilestone int unsigned COMMENT 'milestone ended',
	complete date COMMENT 'date the task is completed',
	approved date COMMENT 'date the task is approved',
	released date COMMENT 'date the task is released',
	cost int unsigned,
	paid date,
	PRIMARY KEY (taskid)
) ENGINE=InnoDB;

ALTER TABLE tasks
	ADD CONSTRAINT tasks_editby FOREIGN KEY (editedby) REFERENCES users (userid),
	ADD CONSTRAINT tasks_dateseditby FOREIGN KEY (dateseditedby) REFERENCES users (userid),
	ADD CONSTRAINT tasks_assignedto FOREIGN KEY (assignedto) REFERENCES users (userid),
	ADD CONSTRAINT tasks_submittedby FOREIGN KEY (submittedby) REFERENCES users (userid),
	ADD CONSTRAINT tasks_approvedby FOREIGN KEY (approvedby) REFERENCES users (userid),
	ADD CONSTRAINT tasks_removedby FOREIGN KEY (removedby) REFERENCES users (userid),
	ADD CONSTRAINT tasks_releasedby FOREIGN KEY (releasedby) REFERENCES users (userid),
	ADD CONSTRAINT tasks_status FOREIGN KEY (status) REFERENCES taskstatus (statusid),
	ADD CONSTRAINT tasks_start FOREIGN KEY (startmilestone) REFERENCES milestones (milestoneid),
	ADD CONSTRAINT tasks_end FOREIGN KEY (endmilestone) REFERENCES milestones (milestoneid),
	ADD CONSTRAINT tasks_project FOREIGN KEY (prjid) REFERENCES projects (prjid),
	ADD CONSTRAINT tasks_area FOREIGN KEY (areaid) REFERENCES projectareas (areaid);

-- --------------------------------------------------------

--
-- Table structure for table tasknotes
--

CREATE TABLE IF NOT EXISTS tasknotes (
	noteid bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary ID of every task notes entry',
	taskid bigint unsigned NOT NULL COMMENT 'foreign key access to the task table taskid field',
	fromid int unsigned COMMENT 'from whom the task notes came from',
	msgtype tinyint COMMENT '1=note, 2=message, 3=assigned, 4=complete, 5=rejected, 6=approved, 7=assign to, 8=approve by, 9=released, 10=unreleased, 11=created, 12=deleted, 13=undeleted',
	senton datetime NOT NULL,
	targetuser int unsigned COMMENT 'the user who is the subject of an edit',
	subject varchar(100),
	message text COMMENT 'the instruction for a certain task',
	PRIMARY KEY (noteid)
) ENGINE=InnoDB;

ALTER TABLE tasknotes
	ADD CONSTRAINT tasknotes_task FOREIGN KEY (taskid) REFERENCES tasks (taskid),
	ADD CONSTRAINT tasknotes_from FOREIGN KEY (fromid) REFERENCES users (userid),
	ADD CONSTRAINT tasknotes_user FOREIGN KEY (targetuser) REFERENCES users (userid);

-- --------------------------------------------------------

--
-- Table structure for table taskmessages
--

CREATE TABLE IF NOT EXISTS taskmessages (
	noteid bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary ID of every task notes entry',
	toid int unsigned COMMENT 'to whom the task notes assigned',
	seenon datetime,
	PRIMARY KEY (noteid, toid)
) ENGINE=InnoDB;

ALTER TABLE taskmessages
	ADD CONSTRAINT taskmessages_note FOREIGN KEY (noteid) REFERENCES tasknotes (noteid),
	ADD CONSTRAINT taskmessages_to FOREIGN KEY (toid) REFERENCES users (userid);

-- --------------------------------------------------------

--
-- Table structure for table taskfiles
--

CREATE TABLE IF NOT EXISTS taskfiles (
	fileid bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary ID for every task file',
	taskid bigint unsigned NOT NULL,
	uploadedon datetime NOT NULL,
	uploadedby int unsigned NOT NULL,
	filename varchar(100) NOT NULL,
	imagetype varchar(10) NOT NULL,
	descr varchar(200) NOT NULL,
	contents longblob NOT NULL,
	PRIMARY KEY (fileid)
) ENGINE=InnoDB;

ALTER TABLE taskfiles
	ADD CONSTRAINT taskfiles_task FOREIGN KEY (taskid) REFERENCES tasks (taskid),
	ADD CONSTRAINT taskfiles_user FOREIGN KEY (uploadedby) REFERENCES users (userid);



-- --------------------------------------------------------

--
-- Table structure for table taskpages
--
/*
CREATE TABLE IF NOT EXISTS taskcomponents (
	taskid bigint unsigned NOT NULL,
	edited datetime NOT NULL,
	editedby int unsigned,
	subject varchar(120) NOT NULL,
	PRIMARY KEY (taskid,subject)
) ENGINE=InnoDB;

ALTER TABLE taskcomponents
	ADD CONSTRAINT taskcomponents_editby FOREIGN KEY (editedby) REFERENCES users (userid),
	ADD CONSTRAINT taskcomponents_task FOREIGN KEY (taskid) REFERENCES tasks (taskid);
*/
-- --------------------------------------------------------

--
-- Table structure for table time
--

CREATE TABLE IF NOT EXISTS usertime (
	timeid int unsigned NOT NULL AUTO_INCREMENT,
	userid int unsigned NOT NULL COMMENT 'Foreign key access of users table userid field',
	starton datetime NOT NULL,
	endon datetime,
	incomplete varchar(1) COMMENT 'the time not completed',
	adjustment decimal(5,1),
	adjustedby int unsigned,
	addby int unsigned,
	reason varchar(40) COMMENT 'reason for adjustment',
	prjid int unsigned,
	areaid int unsigned,
	taskid bigint unsigned,
	comment text,
	PRIMARY KEY (timeid)
) ENGINE=InnoDB;

ALTER TABLE usertime
	ADD CONSTRAINT usertime_addby FOREIGN KEY (addby) REFERENCES users (userid),
	ADD CONSTRAINT usertime_adjustedby FOREIGN KEY (adjustedby) REFERENCES users (userid),
	ADD CONSTRAINT usertime_project FOREIGN KEY (prjid) REFERENCES projects (prjid),
	ADD CONSTRAINT usertime_area FOREIGN KEY (areaid) REFERENCES projectareas (areaid),
	ADD CONSTRAINT usertime_task FOREIGN KEY (taskid) REFERENCES tasks (taskid),
	ADD CONSTRAINT usertime_user FOREIGN KEY (userid) REFERENCES users (userid);

