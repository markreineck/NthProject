<?php
abstract class BaseController extends AlpFramework {

public function __construct($url, $models=NULL)
{
	parent::__construct($url);
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel($models);
	$db->ValidateUserSession($this->Cookie());
}
}
?>
