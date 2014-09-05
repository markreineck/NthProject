<?php
include 'ProjectController.php';

class milestoneedit extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
	if ($this->IsPosted('ProjectID')) {
		$db = $this->Model();

		$err = 0;
		$mid = $this->PostedDigit('MilestoneID');
		$prjid = $this->PostedDigit('ProjectID');
		$name = $this->PostedString('Name');
		$due = $this->PostedString('TargetDate');
		$descr = $this->PostedString('Descr');
		$comp = $this->PostedString('CompletionDate');

		if ($mid > 0) {
			if ($this->DataChanged(array('Name', 'TargetDate', 'CompletionDate', 'Descr'))) {
				$comp = $this->PostedString('CompletionDate');
	
				$err = $db->UpdateMilestone($mid, $name, $due, $comp, $descr);				
			}
		} else if (!empty($name)) {
			$err = $db->CreateMilestone($prjid, $name, $due, $descr);
		}

		if (!$err) {
			$this->RedirectTo('milestones');
		}
	} else {
		$prjid = $this->GetNumber('pid');
		$mid = $this->GetNumber('mid');
	}

	if (!$prjid && !$mid) {
		$this->RedirectTo('milestones');
	} else {
		$this->PutData ('MilestoneID', $mid);
		$this->PutData ('PrjID', $prjid);
		$this->LoadView('home');
	}
}
}
?>
