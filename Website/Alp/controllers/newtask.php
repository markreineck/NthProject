<?php
include 'TaskController.php';

class newtask extends TaskController implements AlpController {

public function __construct($url)
{
	parent::TaskController($url);
}

function Post()
{
	$db = $this->Model();
	$c = $this->Cookie();

	if ($this->IsPosted('DefaultPrj')) {
		$c->SetDefaultProject($this->PostedDigit('DefaultPrj'));

		$prjid = $this->PostedDigit('DefaultPrj');
		$area = $this->PostedDigit('Area');
		$name = $this->PostedString('Description');
		$status = $this->PostedDigit('Status');
		$priority = $this->PostedDigit('Priority');
		$startms = $this->PostedDigit('StartMS');
		$endms = $this->PostedDigit('EndMS');
		$starton = $this->PostedString('StartOn');
		$doby = $this->PostedString('DoBy');
		$assnto = $this->PostedDigit('AssignTo');
		$apprby = $this->PostedDigit('ApproveBy');
		$descr = $this->PostedData('Notes');
		$cost = $this->PostedNumber('Cost');

		$taskid = $db->CreateTask($prjid, $area, $status, $priority, $name, $startms, $endms, $starton, $doby, $assnto, $apprby, $descr, $cost);

		if ($taskid) {
			$email = $this->LoadClass(array('EmailClass', 'TaskEmailClass'));
			$email->SendTaskAssigned($taskid);
			$this->RedirectTo('taskinfo?tid='.$taskid);
		}
	}
	$this->Start();
}

function Start()
{
	$db = $this->Model();
	$c = $this->Cookie();

	$aid = ($this->IsPosted('areaid')) ? $this->PostedDigit('areaid') : 0;
	if ($aid > 0) {
		$pid = $db->ReadAreaProject($aid);
	} else {
		$pid = ($this->IsPosted('prjid')) ? $this->PostedDigit('prjid') : $c->GetDefaultProject();
	}

	$this->PutData ('AreaID', $aid);
	$this->PutData ('PrjID', $pid);
	$this->LoadView('home');
}
}
?>