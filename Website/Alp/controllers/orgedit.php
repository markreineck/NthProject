<?php
include 'OrgController.php';

class orgedit extends OrgController implements AlpController {

public function __construct($url)
{
	parent::OrgController($url);
//	$form = $this->DBForm('orgedit');
}

function Post()
{
	$orgtbl = $this->DBTable();

	$orgtbl->SetPostedKey();
	if ($this->PostData['SaveBtn'] == 'Delete') {
		$err = $orgtbl->DoDelete();
	} else if ($orgtbl->DataChanged()) {
		$err = $orgtbl->DoUpdateOrCreate();
	}

	if (!$err)
		$this->RedirectTo('companies');
	else
		$this->ShowOrg();
}

function Start()
{
	$orgtbl = $this->DBTable();
	if (isset($this->GetData['id']))
		$orgid = $this->GetData['id'];
	else
		$orgid = 0;

	$orgtbl->SetKey(array('orgid' => $orgid));
	$this->ShowOrg();
}

function ShowOrg()
{
	$this->LoadView('home');
}

}
?>