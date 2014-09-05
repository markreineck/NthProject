<?php
class jsonserver extends AlpFramework implements AlpController {

var $SessionID;

public function __construct($url)
{
	parent::AlpFramework($url);
}

function ShowJSONTask ($db, $taskid)
{
$json = new Services_JSON();

	$newdata = array('ErrCode' => $db->ErrorCode(), 'ErrMsg' => $db->ErrorMsg(), 'SesID' => $this->SessionID);
	$newdata['Data'] = $db->ReadTaskUnix($taskid);
	echo $json->encode(array('Result' => $newdata));
}

function ShowJSONData ($data)
{
$json = new Services_JSON();

	settype($errcode,'string');
	$newdata = array('ErrCode' => 0, 'ErrMsg' => '', 'Data' => $data);
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
	$newdata = array('ErrCode' => $errcode, 'ErrMsg' => $errmsg);
	echo $json->encode(array('Result' => $newdata));
}

function ShowJSONTestData ()
{
	ShowJSONData ('Test');
}

function ValidateUserSession()
{
	$this->SessionID = $this->Model()->ReadUserSession(@$_REQUEST['SesID']);
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
			if ($data && isset($data->sessionid) && $data->sessionid) {
				$options->SubscriptionName = $this->UserSetting('SubscriptionName');
				$options->ApproveTasks = ($this->UserSetting('ApproveTasks')) ? 1 : 0;
				$options->ReleaseTasks = ($this->UserSetting('ReleaseTasks')) ? 1 : 0;
				$options->Milestones = ($this->UserSetting('Milestones')) ? 1 : 0;
				$options->Time = ($this->UserSetting('Time')) ? 1 : 0;
				$options->Organizations = ($this->UserSetting('Organizations')) ? 1 : 0;
				$options->TaskDates = ($this->UserSetting('TaskDates')) ? 1 : 0;
				$options->TaskCost = ($this->UserSetting('TaskCost')) ? 1 : 0;

				$result->SessionID = $data->sessionid;
				$result->User = $data;
				$result->Options = $options;

				$result->LastUpdate = $db->Select(
"select UNIX_TIMESTAMP(max(edited)) from (
select max(edited) edited from projects
union all
select max(edited) edited from projectareas
union all
select max(edited) edited from milestones
union all
select max(edited) edited from projectusers
union all
select max(edited) edited from users) x");

				$this->ShowJSONData ($result);
			} else
				$this->ShowJSONDBError ($db);
		break;

		case 'GetLists':
			$db = $this->LoadModel(array('DatabaseDB', 'ProjectDB'));
			$this->ValidateUserSession();
			$data->TimeStamp = $db->Select('SELECT UNIX_TIMESTAMP( now( ) ) ');
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

		case 'FindTasks':
			$db = $this->LoadModel(array('DatabaseDB', 'TaskDB', 'TaskListDB'));
			$this->LoadLibrary('TaskFilter');

			$filter = new TaskListFilter();

			$sid = $this->ValidateUserSession();

			$filter->DefaultPrj = (isset($_REQUEST['Project'])) ? $_REQUEST['Project'] : 0;
			$filter->DefaultArea = (isset($_REQUEST['Area'])) ? $_REQUEST['Area'] : 0;
			$filter->DefaultMilestone = (isset($_REQUEST['Milestone'])) ? $_REQUEST['Milestone'] : -1;
			$filter->DefaultUser = (isset($_REQUEST['AssnTo'])) ? $_REQUEST['AssnTo'] : -1;
			$filter->DefaultTaskStatus = (isset($_REQUEST['TaskStatus'])) ? $_REQUEST['TaskStatus'] : '';

			$data = $db->SearchTasks($filter);
			$this->ShowJSONData ($data);
			break;

		case 'GetTaskNotes':
			$db = $this->LoadModel(array('DatabaseDB', 'TaskDB'));

			$taskid = $_REQUEST['TaskID'];
			$sid = $this->ValidateUserSession();

			$data = $db->ReadTaskComments($taskid);
			$this->ShowJSONData ($data);
			break;

		case 'CompleteTask':
			$db = $this->LoadModel(array('DatabaseDB', 'TaskDB'));

			$taskid = $_REQUEST['TaskID'];
			$msg = ($_REQUEST['Msg']) ? $_REQUEST['Msg'] : '';
			$this->ValidateUserSession();

			$data = $db->CompleteTask($taskid, $msg);
			if (!$data) {
				$email = $this->LoadClass(array('EmailClass', 'TaskEmailClass'));
				$email->SendTaskComplete($taskid);
			}
			$this->ShowJSONTask ($db, $taskid);
			break;

		case 'ApproveTask':
			$db = $this->LoadModel(array('DatabaseDB', 'TaskDB'));

			$taskid = $_REQUEST['TaskID'];
			$this->ValidateUserSession();

			if (!$data) {
				$email = $this->LoadClass(array('EmailClass', 'TaskEmailClass'));
				$email->SendTaskApproved($taskid);
			}
			$data = $db->ApproveTask($taskid);
			$this->ShowJSONTask ($db, $taskid);
			break;

		case 'RejectTask':
			$db = $this->LoadModel(array('DatabaseDB', 'TaskDB'));

			$taskid = $_REQUEST['TaskID'];
			$msg = ($_REQUEST['Msg']) ? $_REQUEST['Msg'] : $msg;
			$this->ValidateUserSession();

			$data = $db->DisapproveTask($taskid, '');
			if (!$data) {
				$email = $this->LoadClass(array('EmailClass', 'TaskEmailClass'));
				$email->SendTaskDisapproved($taskid);
			}
			$this->ShowJSONTask ($db, $taskid);
			break;

		case 'DeleteTask':
			$db = $this->LoadModel(array('DatabaseDB', 'TaskDB'));

			$taskid = $_REQUEST['TaskID'];
			$this->ValidateUserSession();

			$data = $db->DeleteTask($taskid);
			$this->ShowJSONTask ($db, $taskid);
			break;

		case 'UpdateTask':
			$db = $this->LoadModel(array('DatabaseDB', 'TaskDB'));

			$taskid = $_REQUEST['TaskID'];
			$area = $_REQUEST['AreaID'];
			$status = $_REQUEST['StatusID'];
			$priority = $_REQUEST['Priority'];
			$assnto = $_REQUEST['AssignedTo'];
			$name = $_REQUEST['Name'];
			$this->ValidateUserSession();
			$data = $db->EditTask($taskid, $area, $status, $priority, $name);
			$this->ShowJSONTask ($db, $taskid);
			break;

		case 'CreateTask':
			$db = $this->LoadModel(array('DatabaseDB', 'TaskDB'));

			$taskid = $_REQUEST['TaskID'];
			$prj = $_REQUEST['PrjID'];
			$area = $_REQUEST['AreaID'];
			$status = $_REQUEST['StatusID'];
			$priority = $_REQUEST['Priority'];
			$assnto = $_REQUEST['AssignedTo'];
			$name = $_REQUEST['Name'];
			$needby = $_REQUEST['NeedBy'];
			$descr = $_REQUEST['Descr'];
			$this->ValidateUserSession();
			$taskid = $db->CreateTask($prj, $area, $status, $priority, $name, '', '', '', $needby, $assnto, '', $descr, '');
			$this->ShowJSONTask ($db, $taskid);
			break;

		// Required for adding a task in web front end			
		case 'GetTaskLists':
			$db = $this->LoadModel(array('DatabaseDB', 'TaskDB'));
			$prjid = @$_GET['PrjID'];
			$data['Milestones'] = $db->ListProjectMilestones($prjid);
			$data['Areas'] = $db->ListProjectAreas($prjid);
			$data['AssignTo'] = $db->GetAssignToList($prjid);
			$data['ApproveBy'] = $db->GetApproveByList($prjid);
			$data['Defaults'] = $db->ReadProjectDefaults($prjid);
			$this->ShowJSONData ($data);
			break;

		case 'AddTaskNote':
			$db = $this->LoadModel(array('DatabaseDB', 'TaskDB'));

			$taskid = $_REQUEST['TaskID'];
			$descr = $_REQUEST['Msg'];
			$this->ValidateUserSession();
			$error = $db->AddTaskNote($taskid, $descr);
			if ($error) {
				$this->ShowJSONDBError ($db);
			} else {
				$data = $db->ReadTaskComments($taskid);
				$this->ShowJSONData ($data);
			}
			break;

		case 'AddTaskMessage':
			$db = $this->LoadModel(array('DatabaseDB', 'TaskDB'));

			$taskid = $_REQUEST['TaskID'];
			$to = $_REQUEST['To'];
			$subj = $_REQUEST['Subject'];
			$descr = $_REQUEST['Msg'];
			$this->ValidateUserSession();

			$email = $this->LoadClass('EmailClass');
			$email->From($db->GetUserEmail());
			$email->To($to);
			$email->Subject($subj);
			$email->Message($descr);
			$email->Send();

			$error = $db->AddTaskMessage($taskid, $to, $subj, $descr);
			if ($error) {
				$this->ShowJSONDBError ($db);
			} else {
				$data = $db->ReadTaskComments($taskid);
				$this->ShowJSONData ($data);
			}
			break;

        default:
			echo 'Unknown Web Service';
	}
}

}
?>