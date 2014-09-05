<?php
include 'TimeController.php';

class recordtime extends TimeController implements AlpController {

public function __construct($url)
{
	parent::TimeController($url);
}

public function Start()
{
	if ($this->IsPosted('Hours1')) {
		$db = $this->Model();

		$userid = $this->PostedDigit('UserID');
		$prjid = $this->PostedDigit('Project');
		$date = $this->PostedString('Date');
		$time1 = $this->PostedDigit('Hours1');
		$time2 = $this->PostedDigit('Hours2');
		$hour = $this->PostedDigit('Time1');
		$min = $this->PostedDigit('Time2');
		$ampm = $this->PostedString('Time3');
		$note = $this->PostedString('Comment');

		$date = str_replace('-','/',str_replace("'","",$date));
		$date .= " $hour:$min $ampm";
		$start = strtotime($date);

		$end = $start + (360 * (($time1 * 10) + $time2));

		$err = $db->RecordTime($userid, $prjid, $start, $end, $note);

		if (!$err) {
			$this->PutData ('okmsg', 'Time was saved');
		}
	}

	$this->LoadView('home');
}
}
?>