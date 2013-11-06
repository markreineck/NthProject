<?php
include 'ProjectController.php';

class projectareaedit extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
if (isset($this->PostData['ProjectID'])) {
	$db = $this->Database();

	$err = 0;
	$areaid = $this->PostData['AreaID'];
	$prjid = $this->PostData['ProjectID'];
	$name = $this->PostData['Name'];
	$resp = $this->PostData['Responsible'];
	$due = $this->PostData['TargetDate'];
	$price = $this->PostData['Price'];

	if ($areaid > 0) {
		if ($this->DataChanged(array('Name', 'Responsible', 'TargetDate', 'Price'))) {
			$start = $this->PostData['StartDate'];
			$target = $this->PostData['TargetDate'];
			$comp = $this->PostData['CompletionDate'];
	
			$err = $db->UpdateProjectArea($areaid, $resp, $name, $due, $price);
		}
	} else if (!empty($name)) {
		$err = $db->CreateProjectArea($prjid, $resp, $name, $due, $price);
	}

	if (!$err) {
		$this->RedirectTo('projectareas');
	}
} else {
	$prjid = $this->GetData['pid'];
	$areaid = $this->GetData['aid'];
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