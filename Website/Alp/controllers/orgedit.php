<?php
include 'OrgController.php';

class orgedit extends OrgController implements AlpController {

public function __construct($url)
{
	parent::OrgController($url);
	$form = $this->DBForm('orgedit');
}

function Post()
{
	$form = $this->Forms();
	$form->SetPostedKey();
	if ($this->PostData['SaveBtn'] == 'Delete') {
		$err = $form->DoDelete();
	} else if ($this->DataChanged(array('Name', 'OrgType'))) {
		if ($form->HasKey()) {
			$err = $form->DoUpdate();
		} else {
			$err = $form->DoCreate();
		}
	}
	if (!$err)
		$this->RedirectTo('companies');
	else
		$this->ShowOrg();
}

function Start()
{
	if (isset($this->GetData['id']))
		$orgid = $this->GetData['id'];
	else
		$orgid = 0;
	$form = $this->Forms();
	$form->SetKey(array('orgid' => $orgid));
	$this->ShowOrg();
}

function ShowOrg()
{
	$this->LoadView('home');
}

}
?>