<?php
include 'OrgController.php';

class orgedit extends OrgController implements AlpController {

public function __construct($url)
{
	parent::OrgController($url);
}

function Start()
{
	if (isset($this->PostData['OrgID']))
		$orgid = $this->PostData['OrgID'];
	else if (isset($this->GetData['id']))
		$orgid = $this->GetData['id'];
	else
		$orgid = 0;

	$form = $this->DBForm('orgedit', $orgid);

	if (isset($this->PostData['Name'])) {
		if ($this->PostData['SaveBtn'] == 'Delete') {
			$err = $form->DoDelete();
		} else if ($this->DataChanged(array('Name', 'OrgType'))) {
			if ($this->PostData['OrgID']) {
				$err = $form->DoUpdate();
			} else {
				$err = $form->DoCreate();
			}
	
		}
		if (!$err) {
			$this->RedirectTo('companies');
		}
	}

	$this->PutData ('OrgID', $orgid);
	$this->LoadView('home');
}

}
?>