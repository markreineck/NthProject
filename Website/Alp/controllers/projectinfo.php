<?php
include 'ProjectController.php';

class projectinfo extends ProjectController implements AlpController {

function Start()
{
	$c = $this->Cookie();

	$this->LoadLibrary('iconlinks');
	$this->LoadLibrary('taskicons');

	if ($this->IsPosted('DefaultPrj'))
		$c->SetDefaultProject($this->PostedNumber('DefaultPrj'));
	else if ($this->IsGet('id'))
		$c->SetDefaultProject($this->GetDigit('id'));

	$filters = array('projectlist');
	$this->PutData ('PageHeading', $filters);
	$this->LoadView('template2015');
}
}
?>
