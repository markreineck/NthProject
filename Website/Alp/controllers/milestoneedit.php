<?php
include 'ProjectController.php';

class milestoneedit extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
	if (isset($this->PostData['ProjectID'])) {
		$db = $this->Database();

		$err = 0;
		$mid = $this->PostData['MilestoneID'];
		$prjid = $this->PostData['ProjectID'];
		$name = $this->PostData['Name'];
		$due = $this->PostData['TargetDate'];
		$descr = $this->PostData['Descr'];
		$comp = $this->PostData['CompletionDate'];

		if ($mid > 0) {
			if ($this->DataChanged(array('Name', 'TargetDate', 'CompletionDate', 'Descr'))) {
				$comp = $this->PostData['CompletionDate'];
	
				$err = $db->UpdateMilestone($mid, $name, $due, $comp, $descr);				
			}
		} else if (!empty($name)) {
			$err = $db->CreateMilestone($prjid, $name, $due, $descr);
		}

		if (!$err) {
			$this->RedirectTo('milestones');
		}
	} else {
		$prjid = $this->GetData['pid'];
		$mid = $this->GetData['mid'];
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
