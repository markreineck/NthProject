<?php
class jsonserver extends AlpFramework implements AlpController {

var $SessionID;

public function __construct($url)
{
	parent::AlpFramework($url);
}

function ShowJSONData ($data)
{
$json = new Services_JSON();

	settype($errcode,'string');
	$newdata = array('ErrCode' => 0, 'ErrMsg' => '', 'SessionID' => $this->SessionID, 'Data' => $data);
	echo $json->encode(array('Result' => $newdata));
}

function ShowJSONDBError ($db)
{
	$this->ShowJSONError ($db->ErrorCode(), $db->ErrorMsg());
}

function ShowJSONError ($errcode, $errmsg)
{
$json = new Services_JSON();

	settype($errcode,'string');
	$newdata = array('ErrCode' => $errcode, 'ErrMsg' => $errmsg, 'SessionID' => $this->SessionID);
	echo $json->encode(array('Result' => $newdata));
}

function ShowJSONTestData ()
{
	ShowJSONData ('Test');
}

function ValidateUserSession()
{
	$this->SessionID = $this->Database()->ReadUserSession(@$_REQUEST['SessionID']);
	if (!$this->SessionID) {
		$this->ShowJSONError (1, 'You are not logged in');
		exit;
	}
	return $this->SessionID;
}

function Start()
{
	$this->LoadLibrary('JSON');

	switch ($this->Controller(1)) {
		case 'Test':
			$this->ShowJSONData ('JSON Test');
			break;

		case 'StartSession':
			$db = $this->LoadModel(array('DatabaseDB', 'LoginDB'));
			$username = $_REQUEST['UserName'];
			$password = $_REQUEST['Password'];

			$data = $db->LoginToDB ($username, $password);
			if ($data && isset($data->sessionid) && $data->sessionid)
				$this->ShowJSONData ($data);
			else
				$this->ShowJSONDBError ($db);
		break;

		case 'GetProjectList':
			$db = $this->LoadModel(array('DatabaseDB', 'ProjectDB'));
			$this->ValidateUserSession();
			$data->Projects = $db->ListProjects(0, 'A', 0);
			$sql = "select a.areaid, a.prjid, a.name, a.price, a.completed, a.due 
from projectareas a order by name";
			$data->ProjectAreas = $db->SelectAll($sql);
			$sql = "select prjid, milestoneid, name, completion, targetdate from milestones";
			$data->Milestones = $db->SelectAll($sql);
			$sql = "select p.prjid, p.userid, ifnull(p.superuser,0) superuser, ifnull(p.approval,0) approval, ifnull(p.submit,0) submit, ifnull(p.assigned,0) assigned, ifnull(p.assign,0) assign, ifnull(p.edit,0) edit, ifnull(p.publish,0) publish, u.firstname, u.lastname 
from projectusers p, users u where p.userid=u.userid
union all
select prjid, userid, 1, 1, 1, 1, 1, 1, 1, u.firstname, u.lastname from projects p, users u where u.superuser is not null";
			$data->Users = $db->SelectAll($sql);
			$sql = "select statusid, ifnull(hold,0) hold, name from taskstatus";
			$data->TaskStatus = $db->SelectAll($sql);
			$this->ShowJSONData ($data);
			break;

/*
	case 'ReadProject':
		$db = $this->LoadModel(array('DatabaseDB', 'ProjectDB'));
		$data = $db->ReadProject(10);
		$this->ShowJSONData ($data);
		break;
*/
		case 'GetTaskLists':
			$db = $this->LoadModel(array('DatabaseDB', 'TaskDB'));
			$this->ValidateUserSession();
			$prjid = @$_GET['PrjID'];
			$data['Milestones'] = $db->ListProjectMilestones($prjid);
			$data['Areas'] = $db->ListProjectAreas($prjid);
			$data['AssignTo'] = $db->GetAssignToList($prjid);
			$data['ApproveBy'] = $db->GetApproveByList($prjid);
			$data['Defaults'] = $db->ReadProjectDefaults($prjid);
			$this->ShowJSONData ($data);
			break;

		default:
			echo 'AJAX Error';
	}
}

}
?>