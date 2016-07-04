<?php
include 'ProjectController.php';

class projectareaedit extends ProjectController implements AlpController {

function Start()
{
	if ($this->IsPosted('ProjectID')) {
		$db = $this->Model();
	
		$err = 0;
		$areaid = $this->PostedDigit('AreaID');
		$prjid = $this->PostedDigit('ProjectID');
		$name = $this->PostedString('Name');
		$resp = $this->PostedDigit('Responsible');
		$due = $this->PostedString('TargetDate');
		$price = $this->PostedNumber('Price');
	
		if ($areaid > 0) {
			if ($this->DataChanged(array('Name', 'Responsible', 'TargetDate', 'Price'))) {
				$start = $this->PostedString('StartDate');
				$target = $this->PostedString('TargetDate');
				$comp = $this->PostedString('CompletionDate');
		
				$err = $db->UpdateProjectArea($areaid, $resp, $name, $due, $price);
			}
		} else if (!empty($name)) {
			$err = $db->CreateProjectArea($prjid, $resp, $name, $due, $price);
		}
	
		if (!$err) {
			$this->RedirectTo('projectareas');
		}
	} else {
		$prjid = $this->GetNumber('pid');
		$areaid = $this->GetNumber('aid');
	}
	
	if (!$prjid && !$areaid) {
		$this->RedirectTo('projectareas');
	} else {
		$this->PutData ('AreaID', $areaid);
		$this->PutData ('PrjID', $prjid);
		$this->LoadView('template2015');
	}
}
}
?>