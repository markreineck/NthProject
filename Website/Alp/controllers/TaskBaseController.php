<?php
abstract class TaskBaseController extends AlpFramework {

public function TaskBaseController($url)
{
	parent::AlpFramework($url);
}

function TaskInfoLink($taskid, $descr)
{
	echo "<a href=\"taskinfo?tid=$taskid\">$descr</a>";
}

public function ApproveTasks()
{
	if (isset($_GET['apprid'])) {
		$taskid = $_GET['apprid'];
		$db = $this->Model();
		$err = $db->ApproveTask($taskid);
		if ($err == 0) {
			$email = $this->LoadClass(array('EmailClass', 'TaskEmailClass'));
			$email->SendTaskApproved($taskid); //, $data, 'S', $title, 'Task approved');
		}
	}
}
}
?>
