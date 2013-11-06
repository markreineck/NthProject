<?php
include 'TaskBaseController.php';

abstract class TaskController extends TaskBaseController {

public function TaskController($url)
{
	parent::TaskBaseController($url);
	$db = $this->LoadModel(array('DatabaseDB', 'TaskDB'));
	$c = $this->Cookie('ProjectCookie');
	$db->ValidateUserSession($this->Cookie());
}
}
?>
