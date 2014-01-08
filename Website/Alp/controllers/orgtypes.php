<?php
class orgtypes extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::AlpFramework($url);
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'OrgDB'));
	$db->ValidateUserSession($this->Cookie());
	$this->DBTable('OrganizationType');
}

function Post()
{
	$orgtbl = $this->DBTable();
	$cnt = $_POST['RowCnt'];
	for ($x=0; $x<$cnt; $x++) {
		$name =  $_POST['Name'.$x];
		if (isset($_POST['ID'.$x])) {
			$id = $_POST['ID'.$x];
			$orgtbl->SetKey(array('statusid' => $id));

			if ($_POST['Delete'.$x])
				$err = $orgtbl->DoDelete($id);
			else
				$err = $orgtbl->DoUpdate($id, $name);
			
		 } else {
			$err = $orgtbl->DoCreate($name);
		}
	}
}

function Start()
{
	$this->PutData ('ListName', 'Organization Status');
	$this->LoadView('home');
}
}
?>
