<?php
include 'ProjectController.php';

class projectareaadd extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
if ($this->IsPosted('ProjectID')) {
	$err = 0;
	$cnt = $this->PostedDigit('AreaCnt');
	$prjid = $this->PostedDigit('ProjectID');
	for ($x=0; $x<$cnt; $x++) {
		$name = $this->PostedString('Name'.$x);
		if (!empty($name)) {
			$db = $this->Model();
			$resp = $this->PostedDigit('Responsible'.$x);
			$due = '';
			$price = '';
			$err = $db->CreateProjectArea($prjid, $resp, $name, $due, $price);
		}
	}

	if (!$err) {
		$this->RedirectTo('projectareas');
	}
} else {
	$prjid = $this->GetNumber('pid');
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