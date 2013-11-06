<?php
abstract class OrgController extends AlpFramework {

public function OrgController($url)
{
	parent::AlpFramework($url);
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'OrgDB'));
	$db->ValidateUserSession($this->Cookie());
}
}
?>
