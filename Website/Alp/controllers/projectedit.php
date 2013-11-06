<?php
include 'ProjectController.php';

class projectedit extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
	if (isset($this->PostData['ProjectID'])) {
		$db = $this->Database();

		$err = 0;
		$prjid = $this->PostData['ProjectID'];

		if ($this->DataChanged(array('Organization', 'PrjName', 'Priority', 'Status', 'TimeRpt', 'Notes'))) {
			$orgid = $this->PostData['Organization'];
			$name = $this->PostData['PrjName'];
			$priority = $this->PostData['Priority'];
			$status = $this->PostData['Status'];
			$timerpt = $this->PostData['TimeRpt'];
			$notes = $this->PostData['Notes'];	

			$err = $db->UpdateProject($prjid, $orgid, $name, $priority, $status, $timerpt, $notes);
		}
		if (!$err && $this->DataChanged(array('StartDate', 'TargetDate', 'CompletionDate'))) {
			$start = $this->PostData['StartDate'];
			$target = $this->PostData['TargetDate'];
			$comp = $this->PostData['CompletionDate'];

			$err = $db->UpdateProjectDates($prjid, $start, $target, $comp);
		}

		if (!$err) {
			$this->RedirectTo('projects');
		}
	} else {
		$prjid = $this->GetData['id'];
	}

	$this->PutData ('PageHeading', array('projectlinks'));
	$this->PutData ('NextPage', 'projectedit');
	$this->PutData ('PrjID', $prjid);
	$this->LoadView('home');
}
}
?>