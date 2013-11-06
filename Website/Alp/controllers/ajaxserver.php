<?php
class ajaxserver extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::AlpFramework($url);
}

function Start()
{
	switch ($this->Controller(1)) {
		case 'Test':
			echo 'AJAX Test';
			break;

		case 'GetProjectTaskNames':
			$prjid = @$_REQUEST['id'];
			$uid = @$_REQUEST['u'];
			echo '<option>Any</option>';
			if ($prjid > 0) {
				$db = $this->Database();

				$sql = "select t.taskid, concat(a.name,': ',t.name)
	from tasks t, projectareas a
	where a.areaid=t.areaid and removed is null and approved is null and a.prjid=$prjid order by a.name, t.name";
				$db->FillList($sql);
			}
			break;

		case 'GetProjectAreaSelectList':
			echo '<option></option>';
			$prjid = @$_REQUEST['DefaultPrj'];
			if ($prjid > 0) {
				$db = $this->Database();
				$sql = "select areaid, name from projectareas where prjid=$prjid order by name";
				$db->FillList($sql);
			}
			break;

		case 'GetMilestoneSelectList':
			echo '<option></option>';
			$prjid = @$_REQUEST['DefaultPrj'];
			if ($prjid > 0) {
				$db = $this->Database();
				$sql = "select milestoneid, name from milestones where prjid=$prjid and completion is null order by name";
				$db->FillList($sql);
			}
			break;

		case 'GetProjectList':
			$sid = @$_GET['sid'];
			if ($sid) {
				$db = $this->LoadModel(array('DatabaseDB', 'ProjectDB'));
				$sid = $db->ReadUserSession($sid);
				$org = (isset($_GET['DefaultOrg'])) ? $_GET['DefaultOrg'] : 0;
				$this->PutData ('Company', $org);
				$this->PutData ('ProjectStatus', $_GET['DefaultStatus']);
				$this->LoadView('widgets/showprojectlist');
			}
			break;

		case 'CompleteMilestone':
			$sid = @$_GET['sid'];
			if ($sid) {
				$db = $this->LoadModel(array('DatabaseDB', 'ProjectDB'));
				$sid = $db->ReadUserSession($sid);
				$db->CompleteMilestone($_GET['msid']);
				$this->PutData ('prjid', $_GET['DefaultPrj']);
				$this->LoadView('widgets/showmilestones');
			}
			break;

		case 'GetMilestoneList':
			$sid = @$_GET['sid'];
			if ($sid) {
				$db = $this->LoadModel(array('DatabaseDB', 'ProjectDB'));
				$sid = $db->ReadUserSession($sid);
				$this->PutData ('prjid', $_GET['DefaultPrj']);
				$this->LoadView('widgets/showmilestones');
			}
			break;

		case 'GetProjectAreaList':
			$sid = @$_GET['sid'];
			if ($sid) {
				$db = $this->LoadModel(array('DatabaseDB', 'ProjectDB'));
				$sid = $db->ReadUserSession($sid);
				$this->PutData ('ProjectID', $_GET['DefaultPrj']);
				$this->LoadView('widgets/showprojectareas');
			}
			break;

		case 'GetProjectDefaultsList':
			$sid = @$_GET['sid'];
			if ($sid) {
				$db = $this->LoadModel(array('DatabaseDB', 'ProjectDB'));
				$sid = $db->ReadUserSession($sid);
				$this->PutData ('orgid', $_GET['DefaultOrg']);
				$this->LoadView('widgets/showprojectdefaults');
			}
			break;

		case 'GetUserList':
			$sid = @$_GET['sid'];
			if ($sid) {
				$db = $this->LoadModel(array('DatabaseDB', 'UserDB'));
				$sid = $db->ReadUserSession($sid);
				$org = (isset($_GET['DefaultOrg'])) ? $_GET['DefaultOrg'] : 0;
				$this->PutData ('Company', $org);
				$this->PutData ('UserStatus', $_GET['DefaultUserType']);
				$this->LoadView('widgets/showuserlist');
			}
			break;

		default:
			echo 'AJAX Error';
	}
}
}
?>