<?php
class userstatusdel extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::AlpFramework($url);
}

function Start()
{
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'SuperDB'));

	$err = 0;
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
		$cnt = $db->CountUsersWithType($id);

		if ($cnt < 1) {
			$err = $db->DeleteUserStatus($id);
		} else {
			$this->PutData('TaskCnt', $cnt);
			$db->SetError(-1,"This user status cannot be removed because it is assigned to $cnt users");
			$err = 1;
		}
		if (!$err)
			$this->PutData('NextPage', 'userstatuses');
	}

	$this->PutData ('MenuID', 'Supervisor');
	$this->LoadView('template2015');
}

}
?>