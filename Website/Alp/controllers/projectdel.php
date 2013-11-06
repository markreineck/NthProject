<?php
include 'ProjectController.php';

class projectdel extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
	if (isset($this->PostData['ProjectID'])) {
		$db = $this->Database();

		$err = 0;
		$prjid = $this->PostData['ProjectID'];

		$err = $db->DeleteProject($prjid);

		if (!$err)
			$this->RedirectTo('projects');

	} else {
		$prjid = $this->GetData['prj'];
	}

	$this->PutData ('PrjID', $prjid);
	$this->LoadView('home');
}
}
?>