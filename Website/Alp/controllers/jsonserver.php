<?php
class jsonserver extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::AlpFramework($url);
}

function ShowJSONData ($data)
{
$json = new Services_JSON();

	settype($errcode,'string');
	$newdata = array('ErrCode' => 0, 'ErrMsg' => '', 'Data' => $data);
	$newdata = array('Result' => $newdata);
	echo $json->encode($newdata);
}

function ShowJSONError ($errcode, $errmsg)
{
$json = new Services_JSON();

	settype($errcode,'string');
	$newdata = array('ErrCode' => $errcode, 'ErrMsg' => $errmsg);
	$newdata = array('Result' => $newdata);
	echo $json->encode($newdata);
}

function ShowJSONTestData ()
{
	ShowJSONData ('Test');
}

function Start()
{
	$this->LoadLibrary('JSON');

	$db = $this->LoadModel(array('DatabaseDB', 'TaskDB'));
	$sid = $db->ReadUserSession(@$_GET['SessionID']);
	if (!$sid) {
		$this->ShowJSONError (1, 'The user is not logged in');
	} else {

		switch ($this->Controller(1)) {
			case 'Test':
				$this->ShowJSONData ('JSON Test');
				break;
/*
	case 'ProjectList':
		$db = $this->LoadModel(array('DatabaseDB', 'ProjectDB'));
		$data = $db->ReadProjectList();
		$this->ShowJSONData ($data);
		break;

	case 'ReadProject':
		$db = $this->LoadModel(array('DatabaseDB', 'ProjectDB'));
		$data = $db->ReadProject(10);
		$this->ShowJSONData ($data);
		break;
*/
			case 'GetTaskLists':
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

}
?>