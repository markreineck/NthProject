<?php
include 'ProjectController.php';

class projectdel extends ProjectController implements AlpController {

function Start()
{
	if ($this->IsPosted('ProjectID')) {
		$db = $this->Model();

		$err = 0;
		$prjid = $this->PostedDigit('ProjectID');

		$err = $db->DeleteProject($prjid);

		if (!$err)
			$this->RedirectTo('projects');

	} else {
		$prjid = $this->GetNumber('prj');
	}

	$this->PutData ('PrjID', $prjid);
	$this->LoadView('template2015');
}
}
?>