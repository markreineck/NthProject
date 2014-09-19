<?php
include 'ProjectController.php';

class projectedit extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
	if ($this->IsPosted('ProjectID')) {
		$db = $this->Model();

		$err = 0;
		$prjid = $this->PostedDigit('ProjectID');

		if ($this->DataChanged(array('Organization', 'PrjName', 'Priority', 'Status', 'TimeRpt', 'Notes'))) {
			$orgid = $this->PostedDigit('Organization');
			$name = $this->PostedString('PrjName');
			$priority = $this->PostedDigit('Priority');
			$status = $this->PostedString('Status');
			$timerpt = $this->PostedDigit('TimeRpt');
			$notes = $this->PostedHtml('Notes');	

			$err = $db->UpdateProject($prjid, $orgid, $name, $priority, $status, $timerpt, $notes);
		}
		if (!$err && $this->DataChanged(array('StartDate', 'TargetDate', 'CompletionDate'))) {
			$start = $this->PostedString('StartDate');
			$target = $this->PostedString('TargetDate');
			$comp = $this->PostedString('CompletionDate');

			$err = $db->UpdateProjectDates($prjid, $start, $target, $comp);
		}

		if (!$err) {
			$this->RedirectTo('projects');
		}
	} else {
		$prjid = $this->GetNumber('id');
	}

	$this->PutData ('PageHeading', array('projectlinks'));
	$this->PutData ('NextPage', 'projectedit');
	$this->PutData ('PrjID', $prjid);
	$this->LoadView('home');
}
}
?>