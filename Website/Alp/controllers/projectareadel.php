<?php
include 'ProjectController.php';

class projectareadel extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
	if ($this->IsPosted('SourceArea')) {
		$srcarea = $this->PostedDigit('SourceArea');
		$destarea = $this->PostedDigit('DestArea');
		$prjid = $this->PostedDigit('ProjectID');

		$db = $this->Model();

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
		$prjid = $this->GetNumber('prj');
		$areaid = $this->GetNumber('area');
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