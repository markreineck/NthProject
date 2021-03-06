<?php
class taskstatusedit extends AlpFramework implements AlpController {

function Start()
{
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'SuperDB'));

	if ($this->IsPosted('StatusName')) {

		$err = 0;
		$name = $this->PostedString('StatusName');
		$hold = $this->PostedDigit('Hold');
		if ($this->IsPosted('StatusID')) {
			$id = $this->PostedDigit('StatusID');
			$err = $db->UpdateTaskStatus($id, $hold, $name);
		} else {
			$err = $db->CreateTaskStatus($hold, $name);
		}
		if (!$err)
			$this->PutData('NextPage', 'taskstatuses');
	}
	$this->PutData ('MenuID', 'Supervisor');
	$this->LoadView('template2015');
}

}
?>