<?php
include 'BaseController.php';

abstract class TimeController extends BaseController  {

public function __construct($url)
{
	parent::__construct($url, array('DatabaseDB', 'TimeDB'));
	$this->LoadLibrary('checkfilters');
}

}
?>
