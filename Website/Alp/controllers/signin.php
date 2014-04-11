<?php
include 'TimeController.php';

class signin extends TimeController implements AlpController {

public function __construct($url)
{
	parent::TimeController($url);
}

function Post()
{
	$c = $this->Cookie();
	$db = $this->Database();

	if (isset($_POST['Task']) && $_POST['Task'] > 0) {
		$taskid = $_POST['Task'];
		$err = $db->ClockInToTask($taskid);

		if (!$err) {
			$c->SetDefaultProject($db->ReadTaskProject($taskid));
			$this->RedirectTo('signedin');
		}
	} else if (isset($_POST['Project']) && $_POST['Project'] > 0) {
		$prjid = $_POST['Project'];
		$err = $db->ClockInToProject($prjid);

		if (!$err) {
			$c->SetDefaultProject($prjid);
			$this->RedirectTo('signedin');
		}
	}

	$this->LoadView('home');
}

function Start()
{
	$this->LoadView('home');
}
}
?>