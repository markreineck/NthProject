<?php
abstract class UserController extends AlpFramework {

public function UserController($url)
{
	parent::AlpFramework($url);
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'UserDB'));
	$db->ValidateUserSession($this->Cookie());
}
}
?>
