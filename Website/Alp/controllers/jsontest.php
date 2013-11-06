<?php
class jsontest extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::AlpFramework($url);
}

function Start()
{
	$this->LoadView('jsontest');
}
}
?>
