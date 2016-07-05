<?php
$this->URLBase = 'http://projects.nth-generation.com/';
$this->DatabaseDriver = 'MySqliClass';
$this->DebugMode = 0;
$this->PageNotFound = 'index';
$this->CSSFiles = array('style','menu','content','forms');
$this->CSSPath = 'css/';
//$this->ControllerFile = 'global';
$this->DateFormat = 'M d, Y';

$this->UserSettings = array(
	'SubscriptionName' => 'Nth Generation',
	'ApproveTasks' => true,
	'ReleaseTasks' => true,
	'Milestones' => true,
	'Time' => true,
	'Organizations' => true,
	'TaskDates' => true,
	'TaskCost' => true
);
?>
