<?php
include 'BaseController.php';

abstract class OrgController extends BaseController {

public function __construct($url)
{
	parent::__construct($url, array('DatabaseDB', 'OrgDB'));
	$this->DBTable('Organization');
}

}
?>
