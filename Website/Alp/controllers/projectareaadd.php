<?php
include 'ProjectController.php';

class projectareaadd extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
if (isset($this->PostData['ProjectID'])) {
	$err = 0;
	$cnt = $this->PostData['AreaCnt'];
	$prjid = $this->PostData['ProjectID'];
	for ($x=0; $x<$cnt; $x++) {
		$name = $this->PostData['Name'.$x];
		if (!empty($name)) {
			$db = $this->Database();
			$resp = $this->PostData['Responsible'.$x];
			$err = $db->CreateProjectArea($prjid, $resp, $name, $due, $price);
		}
	}

	if (!$err) {
		$this->RedirectTo('projectareas');
	}
} else {
	$prjid = $this->GetData['pid'];
}

if (!$prjid && !$areaid) {
	$this->RedirectTo('projectareas');
} else {
	$this->PutData ('PrjID', $prjid);
	$this->LoadView('home');
}
}
}
?>