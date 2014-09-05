<?php
include 'TaskController.php';

class taskedit extends TaskController implements AlpController {

public function __construct($url)
{
	parent::TaskController($url);
}

function Start()
{
	$db = $this->Model();

	if ($this->IsPosted('TaskCnt')) {
		$cnt = $this->PostedDigit('TaskCnt');
		for ($x=0; $x<$cnt; $x++) {
			$taskid = $this->PostedDigit('TaskID'.$x);
			$areaidx = 'Area'.$x;
			$priidx = 'Priority'.$x;
			$assnidx = 'AssnTo'.$x;
			$statidx = 'Status'.$x;
			$startidx = 'Start'.$x;
			$finidx = 'Finish'.$x;

			if ($this->DataChanged($assnidx))
				$db->EditTaskAssignment($taskid, 'T', $this->PostedDigit($assnidx));
		
			if ($this->UserSetting('Milestones')) {
				if ($this->DataChanged(array($startidx,$finidx)))
					$db->EditTaskMilestones($taskid, 
						$this->PostedDigit($startidx), 
						$this->PostedDigit($finidx));
			} else if ($this->UserSetting('TaskDates')) {
				if ($this->DataChanged($finidx))
					$db->EditTaskDates($taskid, 
						$this->PostedString($startidx), 
						$this->PostedString($finidx));
			}

			if ($this->DataChanged(array($areaidx,$priidx,$statidx)))
				$db->EditTask($taskid, 
					$this->PostedDigit($areaidx), 
					$this->PostedDigit($statidx), 
					$this->PostedDigit($priidx));
		}
		$this->RedirectTo($this->Cookie()->GetLastTaskPage());
	}

	$this->PutData ('OKMsg', $okmsg);
	$this->PutData ('PrjID', $this->GetNumber('pid'));
	$this->PutData ('AreaID', $this->GetNumber('aid'));
	$this->LoadView('home');
}
}
?>