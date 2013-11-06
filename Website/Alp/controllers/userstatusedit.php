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

	if (isset($this->PostData['StatusName'])) {

		$err = 0;
		$name = $this->PostData['StatusName'];
		$pay = $this->PostData['PayType'];
		if (isset($this->PostData['StatusID'])) {
			$id = $this->PostData['StatusID'];
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