<?php
include 'UserController.php';

class userdel extends UserController implements AlpController {

public function __construct($url)
{
	parent::UserController($url);
	$this->DBTable('User');
}

function Post()
{
	$usertbl = $this->DBTable();
	$usertbl->SetPostedKey();
	if ($usertbl->DataChanged()) {
		$err = $usertbl->DoUpdate();
	}
/*		
	if ($this->DataChanged(array('Organization', 'FirstName', 'LastName', 'Initials', 'UserType', 'Email'))) {
		$db = $this->Database();
		$err = 0;
$db->DebugMode(2);

		$userid = $this->PostData['UserID'];
		$org = $this->PostData['Organization'];
		$first = $this->PostData['FirstName'];
		$last = $this->PostData['LastName'];
		$init = $this->PostData['Initials'];
		$email = $this->PostData['Email'];
		$status = $this->PostData['UserType'];
		if (empty($init))
			$init = substr($first,0,1) . substr($last,0,1);

		$err = $db->UpdateUser($userid, $org, $status, $first, $last, $init, $email);

		if (!$err) {
			$maxfld = $this->PostData['FieldCnt'];
			for ($x=0; $x<$maxfld && !$err; $x++) {
				$fldid = 'Field'.$x;
				if ($this->DataChanged($fldid)) {
					$err = $db->UpdateUserField($userid, $this->PostData['FieldID'.$x], $this->PostData[$fldid]);
				}
			}
		}
*/
		if (!$err) {
			$this->RedirectTo('users');
		}
//	}
}

function Start()
{
	if (isset($this->PostData['UserID'])) {
		$userid = $this->PostData['UserID'];
	} else {
		$userid = $this->GetData['userid'];
	}
	$usertbl = $this->DBTable();
	$usertbl->SetKey(array('userid' => $userid));

	$this->PutData ('Verb', 'Edit');
	$this->PutData ('UserID', $userid);
	$this->LoadView('home');
}
}
?>