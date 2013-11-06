<?php
include 'UserController.php';

class myuser extends UserController implements AlpController {

public function __construct($url)
{
	parent::UserController($url);
}

function Start()
{
	if (isset($this->PostData['UserID'])) {
		if ($this->DataChanged(array('FirstName', 'LastName', 'Initials', 'Email'))) {
			$db = $this->Database();
			$err = 0;

			$first = $this->PostData['FirstName'];
			$last = $this->PostData['LastName'];
			$init = $this->PostData['Initials'];
			$email = $this->PostData['Email'];

			if (empty($init))
				$init = substr($first,0,1) . substr($last,0,1);

			$err = $db->UpdateMyUser($first, $last, $init, $email);

			if (!$err) {
				$maxfld = $this->PostData['FieldCnt'];
				for ($x=0; $x<$maxfld && !$err; $x++) {
					$fldid = 'Field'.$x;
					if ($this->DataChanged($fldid)) {
						$err = $db->UpdateUserField($db->GetUserID(), $this->PostData['FieldID'.$x], $this->PostData[$fldid]);
					}
				}
			}
			if (!$err) {
				$this->PutData('OKMsg', 'Your information has been saved.');
			}
		}
	} else {
		$userid = $this->GetData['userid'];
	}

	$this->LoadView('home');
}
}
?>