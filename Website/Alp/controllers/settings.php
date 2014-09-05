<?php
I dont think this is used anymore

class settings extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::AlpFramework($url);
}

function Start()
{
$c = $this->Cookie('ProjectCookie');
$db = $this->LoadModel(array('DatabaseDB', 'SuperDB'));

if (isset($this->PostData['SubscrName'])) {
	if ($this->DataChanged(array('Milestones', 'Organizations', 'Time', 'FixedPrice', 'SubscrName'))) {

		$db->UpdateSubscription(
			$this->PostData['SubscrName'],
			$this->PostData['Time'],
			$this->PostData['Milestones'],
			$this->PostData['Organizations'],
			$this->PostData['FixedPrice']);
	}
}

$this->LoadView('home');
}
}
?>