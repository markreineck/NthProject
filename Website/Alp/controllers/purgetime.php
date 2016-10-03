<?php
include 'TimeController.php';

class purgetime extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::__construct($url);
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'SuperDB'));
	$db->ValidateUserSession($this->Cookie());
}

function Post()
{
	$okmsg = '';
	$db = $this->Model();

	if (isset($_POST['Confirm']) && $_POST['DeleteTimeRecords']) {
		if (!$db->PurgeTime($_POST['DeleteTimeRecords']))
			$okmsg = 'Time records prior to ' . $_POST['DeleteTimeRecords'] . ' have been purged.';
	}
	$this->PutData ('OKMsg', $okmsg);
	$this->Start();
}

function Start()
{
	$this->PutData ('MenuID', 'Supervisor');
	$this->LoadView('template2015');
}

}
?>