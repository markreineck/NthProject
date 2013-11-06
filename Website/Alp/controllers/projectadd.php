<?php
include 'ProjectController.php';

class projectadd extends ProjectController implements AlpController {

public function __construct($url)
{
	parent::ProjectController($url);
}

function Start()
{
$c = $this->Cookie();

if (isset($this->PostData['Organization'])) {
	$db = $this->Database();

	$orgid = $this->PostData['Organization'];
	$name = $this->PostData['PrjName'];
	$priority = $this->PostData['Priority'];
	$status = $this->PostData['Status'];
	$start = $this->PostData['StartDate'];
	$target = $this->PostData['TargetDate'];
	$timerpt = $this->PostData['TimeRpt'];
	$notes = $this->PostData['Notes'];

	if (!$db->CreateProject($orgid, $name, $start, $target, $priority, $status, $timerpt, $notes)) {
		$prjid = $db->SelectMysqlVariable('prjid');
	
		if ($prjid > 0) {
			$c->SetDefaultProject($prjid);
			$this->RedirectTo('projectareas');
		} else {
			$err = 1;
		}
	}
} else {
	$orgid = (isset($this->GetData['o'])) ? $this->GetData['o'] : $c->GetDefaultCompany();
}

$this->PutData ('NextPage', 'projectedit');
$this->PutData ('PrjID', 0);
$this->PutData ('OrgID', $orgid);
$this->LoadView('home');
}
}
?>