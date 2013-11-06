<?php
include 'ProjectController.php';

class projectareadel extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
	if (isset($this->PostData['SourceArea'])) {
		$srcarea = $this->PostData['SourceArea'];
		$destarea = $this->PostData['DestArea'];
		$prjid = $this->PostData['ProjectID'];

		$db = $this->Database();

		$err = 0;

		if ($destarea) {
			$err = $db->MergeProjectAreas($srcarea, $destarea);
		} else {
			$err = $db->DeleteProjectArea($srcarea);
		}

		if (!$err) {
			$this->RedirectTo('projectareas');
		}
	} else {
		$prjid = $this->GetData['prj'];
		$areaid = $this->GetData['area'];
	}

	if (!$prjid && !$areaid) {
		$this->RedirectTo('projectareas');
	} else {
		$this->PutData ('AreaID', $areaid);
		$this->PutData ('PrjID', $prjid);
		$this->LoadView('home');
	}
}
}
?>