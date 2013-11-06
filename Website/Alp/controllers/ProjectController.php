<?php
abstract class ProjectController extends AlpFramework {

public function ProjectController($url)
{
	parent::AlpFramework($url);
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'ProjectDB'));
	$db->ValidateUserSession($this->Cookie());
}
}
?>
