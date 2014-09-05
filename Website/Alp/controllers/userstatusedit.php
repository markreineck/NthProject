<?php
class userstatusedit extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::AlpFramework($url);
}

function Start()
{
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'SuperDB'));

	if ($this->IsPosted('StatusName')) {

		$err = 0;
		$name = $this->PostedString('StatusName');
		$pay = $this->PostedString('PayType');
		if ($this->IsPosted('StatusID')) {
			$id = $this->PostedDigit('StatusID');
			$err = $db->UpdateUserStatus($id, $pay, $name);
		} else {
			$err = $db->CreateUserStatus($pay, $name);
		}
		if (!$err)
			$this->PutData('NextPage', 'userstatuses');
	}
	$this->LoadView('home');
}

}
?>