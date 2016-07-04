<?php
include 'TaskBaseController.php';

abstract class TaskController extends TaskBaseController {

public function __construct($url)
{
	parent::__construct($url);
	$db = $this->LoadModel(array('DatabaseDB', 'TaskDB'));
	$c = $this->Cookie('ProjectCookie');
	$db->ValidateUserSession($this->Cookie());
}
}
?>
