<?php
include 'UserController.php';

class myuser extends UserController implements AlpController {

function Start()
{
	if ($this->IsPosted('UserID')) {
		if ($this->DataChanged(array('FirstName', 'LastName', 'Initials', 'Email'))) {
			$db = $this->Model();
			$err = 0;

			$first = $this->PostedString('FirstName');
			$last = $this->PostedString('LastName');
			$init = $this->PostedString('Initials');
			$email = $this->PostedString('Email');

			if (empty($init))
				$init = substr($first,0,1) . substr($last,0,1);

			$err = $db->UpdateMyUser($first, $last, $init, $email);

			if (!$err) {
				$maxfld = $this->PostedDigit('FieldCnt');
				for ($x=0; $x<$maxfld && !$err; $x++) {
					$fldid = 'Field'.$x;
					if ($this->DataChanged($fldid)) {
						$err = $db->UpdateUserField($db->GetUserID(), $this->PostedDigit('FieldID'.$x), $this->PostedString($fldid));
					}
				}
			}
			if (!$err) {
				$this->PutData('OKMsg', 'Your information has been saved.');
			}
		}
	} else {
		$userid = $this->GetNumber('userid');
	}

	$this->LoadView('template2015');
}
}
?>
