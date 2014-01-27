<?php
abstract class TimeController extends AlpFramework {

public function TimeController($url)
{
	parent::AlpFramework($url);
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'TimeDB'));
	$db->ValidateUserSession($this->Cookie());
	$this->LoadLibrary('checkfilters');
}
}
?>
