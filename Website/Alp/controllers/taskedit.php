<?php
include 'TaskController.php';

class taskedit extends TaskController implements AlpController {

public function __construct($url)
{
	parent::TaskController($url);
}

function Start()
{
	$db = $this->Database();

	if (isset($this->PostData['TaskCnt'])) {
		$cnt = $this->PostData['TaskCnt'];
		for ($x=0; $x<$cnt; $x++) {
			$taskid = $this->PostData['TaskID'.$x];
			$areaidx = 'Area'.$x;
			$priidx = 'Priority'.$x;
			$assnidx = 'AssnTo'.$x;
			$statidx = 'Status'.$x;
			$startidx = 'Start'.$x;
			$finidx = 'Finish'.$x;

			if ($this->DataChanged($assnidx))
				$db->EditTaskAssignment($taskid, 'T', $this->PostData[$assnidx]);
		
			if ($this->UserSetting('Milestones')) {
				if ($this->DataChanged(array($startidx,$finidx)))
					$db->EditTaskMilestones($taskid, 
						$this->PostData[$startidx], 
						$this->PostData[$finidx]);
			} else if ($this->UserSetting('TaskDates')) {
				if ($this->DataChanged($finidx))
					$db->EditTaskDates($taskid, 
						$this->PostData[$startidx], 
						$this->PostData[$finidx]);
			}

			if ($this->DataChanged(array($areaidx,$priidx,$statidx)))
				$db->EditTask($taskid, 
					$this->PostData[$areaidx], 
					$this->PostData[$statidx], 
					$this->PostData[$priidx]);
		}
		$this->RedirectTo($this->Cookie()->GetLastTaskPage());
	}

	$this->PutData ('OKMsg', $okmsg);
	$this->PutData ('PrjID', $this->GetData['pid']);
	$this->PutData ('AreaID', $this->GetData['aid']);
	$this->LoadView('home');
}
}
?>