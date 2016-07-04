<?php
abstract class ProjectController extends AlpFramework {

public function __construct($url)
{
	parent::__construct($url);
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'ProjectDB'));
	$db->ValidateUserSession($this->Cookie());
	$this->PutData ('MenuID', 'Projects');
}
}
?>
