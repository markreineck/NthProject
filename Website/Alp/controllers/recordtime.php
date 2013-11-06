<?php
include 'TimeController.php';

class recordtime extends TimeController implements AlpController {

public function __construct($url)
{
	parent::TimeController($url);
}

public function Start()
{
	if (isset($this->PostData['Hours1'])) {
		$db = $this->Database();

		$userid = $this->PostData['UserID'];
		$prjid = $this->PostData['Project'];
		$date = $this->PostData['Date'];
		$time1 = $this->PostData['Hours1'];
		$time2 = $this->PostData['Hours2'];
		$hour = $this->PostData['Time1'];
		$min = $this->PostData['Time2'];
		$ampm = $this->PostData['Time3'];
		$note = $this->PostData['Comment'];

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