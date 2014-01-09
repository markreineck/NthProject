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
		$nameidx = 'Name'.$x;
		$name =  $_POST[$nameidx];

		if (isset($_POST['ID'.$x])) {
			$id = $_POST['ID'.$x];
			$orgtbl->SetKey(array('statusid' => $id));

			if ($_POST['Delete'.$x] || !$name)
				$err = $orgtbl->Delete($id);
			else if ($this->DataChanged($nameidx))
				$err = $orgtbl->Update($id, $name);
			
		 } else if ($name) {
			$err = $orgtbl->Add($name);
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
