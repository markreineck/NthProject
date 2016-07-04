<?php
class taskstatusdel extends AlpFramework implements AlpController {

function Start()
{
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'SuperDB'));

	$err = 0;
	if (isset($_POST['StatusID'])) {
		$oldid = $_POST['StatusID'];
		$newid = $_POST['NewStatus'];
		$this->PutData('StatusID', $oldid);
		$err = $db->DeleteTaskStatus($oldid, $newid);
		if ($err) {
			$cnt = $db->CountTasksWithStatus($oldid);
			$this->PutData('TaskCnt', $cnt);
		} else
			$this->PutData('NextPage', 'taskstatuses');
	} else if (isset($_GET['id'])) {
		$id = $_GET['id'];
		$this->PutData('StatusID', $id);
		$cnt = $db->CountTasksWithStatus($id);
		if ($cnt < 1) {
			$err = $db->DeleteTaskStatus($id, 0);
		} else {
			$this->PutData('TaskCnt', $cnt);
			$err = 1;
		}
		if (!$err)
			$this->PutData('NextPage', 'taskstatuses');
	}

	$this->PutData ('MenuID', 'Supervisor');
	$this->LoadView('template2015');
}

}
?>