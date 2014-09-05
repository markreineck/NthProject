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

	if ($this->IsPosted('DefaultPrj'))
		$c->SetDefaultProject($this->PostedDigit('DefaultPrj'));

	if ($this->IsPosted('RowCnt')) {
		$max = $this->PostedDigit('RowCnt');
		for ($x=0; $x<$max; $x++) {
			if ($this->PostedDigit('chk'.$x] > 0) {
				$db->MessageSeen($this->PostedDigit('msgid'.$x));
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
