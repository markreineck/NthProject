<?php
include 'TaskListController.php';

class releasetasks extends TaskListController implements AlpController {

public function __construct($url)
{
	parent::TaskListController($url);
}

function Start()
{
	$db = $this->Model();
	$form = $this->Forms();

	$relcnt = 0;
	if (isset($_POST['TaskCnt'])) {
		$N = $_POST['TaskCnt']; 
		for ($i=0; $i < $N; $i++) {
			$idx = 'Release'.$i;
			if ($this->DataChanged($idx)) {
				$tid = $_POST['TaskID'.$i];
				if ($_POST[$idx])
					$db->ReleaseTask($tid);
				else
					$db->UnreleaseTask($tid);
				if (!$db->HasError())
					$relcnt++;
			}
		}
	}

	$this->Ajax()->SetFunction('GetTasksForRelease');
	$this->Ajax()->SetFields(array("DefaultPrj","DefaultUser","DefaultMilestone"));

	if ($relcnt > 0) {
		$msg = $relcnt . ' tasks were released';
		$form->NewData();
	} else {
		$msg = '';
	}
	$this->PutData ('OKMsg', $msg);
	$this->PutData ('PageHeading', array('projectlist', 'milestone', 'assigntolist'));
	$this->LoadView('home');
}
}
?>