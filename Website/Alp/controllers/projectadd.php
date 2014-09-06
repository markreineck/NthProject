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

if ($this->IsPosted('Organization')) {
	$db = $this->Model();

	$orgid = $this->PostedDigit('Organization');
	$name = $this->PostedString('PrjName');
	$priority = $this->PostedDigit('Priority');
	$status = $this->PostedDigit('Status');
	$start = $this->PostedString('StartDate');
	$target = $this->PostedString('TargetDate');
	$timerpt = $this->PostedDigit('TimeRpt');
	$notes = $this->PostedString('Notes');

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
	$orgid = ($this->IsGet('o')) ? $this->GetNumber('o') : $c->GetDefaultCompany();
}

$this->PutData ('NextPage', 'projectedit');
$this->PutData ('PrjID', 0);
$this->PutData ('OrgID', $orgid);
$this->LoadView('home');
}
}
?>