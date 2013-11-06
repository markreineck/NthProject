<?php
include 'TaskListController.php';

class home extends TaskListController {

public function __construct($url)
{
	parent::AlpFramework($url);
}

function Start()
{
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'TaskDB'));
	$db->ValidateUserSession($this->Cookie());

	if (isset($this->PostData['DefaultPrj']))
		$c->SetDefaultProject($this->PostData['DefaultPrj']);

	if (isset($this->PostData['RowCnt'])) {
		$max = $this->PostData['RowCnt'];
		for ($x=0; $x<$max; $x++) {
			if ($this->PostData['chk'.$x] > 0) {
				$db->MessageSeen($this->PostData['msgid'.$x]);
			}
		}
	} else {
		$this->LoadLibrary('sendmessage');
		SendContactMessage($this);
	}

	$this->PutData ('PageHeading', array('projectlist', 'userlist'));
	$this->PutData ('NextPage', 'messages');
	$this->LoadView('home');
}
}
?>
