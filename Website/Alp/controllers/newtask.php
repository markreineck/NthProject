<?php
include 'TaskController.php';

class newtask extends TaskController implements AlpController {

public function __construct($url)
{
	parent::TaskController($url);
}

function Post()
{
	$db = $this->Database();
	$c = $this->Cookie();

	if (isset($this->PostData['DefaultPrj'])) {
		$c->SetDefaultProject($this->PostData['DefaultPrj']);

		$prjid = $this->PostData['DefaultPrj'];
		$area = $this->PostData['Area'];
		$name = $this->PostData['Description'];
		$status = $this->PostData['Status'];
		$priority = $this->PostData['Priority'];
		$startms = $this->PostData['StartMS'];
		$endms = $this->PostData['EndMS'];
		$starton = $this->PostData['StartOn'];
		$doby = $this->PostData['DoBy'];
		$assnto = $this->PostData['AssignTo'];
		$apprby = $this->PostData['ApproveBy'];
		$descr = $this->PostData['Notes'];
		$cost = $this->PostData['Cost'];

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
	$db = $this->Database();
	$c = $this->Cookie();

	$aid = (isset($this->PostData['areaid'])) ? $this->PostData['areaid'] : 0;
	if ($aid > 0) {
		$pid = $db->ReadAreaProject($aid);
	} else {
		$pid = (isset($this->PostData['prjid'])) ? $this->PostData['prjid'] : $c->GetDefaultProject();
	}

	$this->PutData ('AreaID', $aid);
	$this->PutData ('PrjID', $pid);
	$this->LoadView('home');
}
}
?>