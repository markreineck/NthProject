<?php
class taskstatusedit extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::AlpFramework($url);
}

function Start()
{
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'SuperDB'));

	if (isset($this->PostData['StatusName'])) {

		$err = 0;
		$name = $this->PostData['StatusName'];
		$hold = $this->PostData['Hold'];
		if (isset($this->PostData['StatusID'])) {
			$id = $this->PostData['StatusID'];
			$err = $db->UpdateTaskStatus($id, $hold, $name);
		} else {
			$err = $db->CreateTaskStatus($hold, $name);
		}
		if (!$err)
			$this->PutData('NextPage', 'taskstatuses');
	}
	$this->LoadView('home');
}

}
?>