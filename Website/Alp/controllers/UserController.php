<?php
include 'BaseController.php';

abstract class UserController extends BaseController {

public function __construct($url)
{
	parent::__construct($url, array('DatabaseDB', 'UserDB'));
	$this->PutData ('MenuID', 'Supervisor');
}

}
?>
