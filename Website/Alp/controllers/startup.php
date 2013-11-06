<?php
include 'UserController.php';

class startup extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::AlpFramework($url);
}

function Start()
{
	$db = $this->LoadModel(array('DatabaseDB', 'LoginDB', 'StartupDB'));
//$db->DebugMode(2);

	if (isset($_POST['Organization'])) {
		$pwd1 = $_POST['Password'];
		$pwd2 = $_POST['Password2'];
		if ($pwd1 != $pwd2) {
			$db->SetError(1, 'The passwords that you entered do not match');
		} else {
			$enc = $this->LoadClass('EncryptionClass');
			$salt = $enc->CreateSalt();
			$pwd = $enc->EncryptString($pwd1, $salt);
			$email = $_POST['Email'];

			if (!$db->CreateSubscription($_POST['Organization'],
					$_POST['FirstName'],
					$_POST['LastName'],
					$_POST['Initials'],
					$email, $pwd, $salt)) {

				$c = $this->Cookie('ProjectCookie');
				$db->DoLogin($c, $email, $pwd);
				if ($c->GetSessionID() < 1) {
					$db->SetError(1, 'Invalid username and password');
				} else {
					$this->RedirectTo('home');
				}
			}
		}
	} else {
		$data = $db->ReadStartupStatus();
		if (!$data) {
			$this->LoadView('startup');
		} else if ($data->superuser) {
			$this->LoadView('alreadystarted');
		} else if ($data->aorgid) {
echo 'No super user account';
		} else if ($data->name) {
echo 'No organization record';
		} else {
			$this->LoadView('startup');
		}
	}
/*
	$c = $this->Cookie();

	if (isset($this->PostData['Organization'])) {
		$db = $this->Database();
	
		$org = $this->PostData['Organization'];
		$first = $this->PostData['FirstName'];
		$last = $this->PostData['LastName'];
		$init = $this->PostData['Initials'];
		$status = $this->PostData['UserType'];
		$email = $this->PostData['Email'];
		if (empty($init))
			$init = substr($first,0,1) . substr($last,0,1);

		$db->CreateUser($org, $status, $first, $last, $init, $email);
		$userid = $db->SelectMysqlVariable('userid');
	
		if ($userid > 0) {
			$maxfld = $this->PostData['FieldCnt'];
			for ($x=0; $x<$maxfld && !$err; $x++) {
				$fldid = 'Field'.$x;
				if (!empty($this->PostData[$fldid])) {
					$err = $db->UpdateUserField($userid, $this->PostData['FieldID'.$x], $this->PostData[$fldid]);
				}
			}
		} else {
			$err = 1;
		}
		if (!$err) {
			$this->RedirectTo('userreset?userid='.$userid);
		}
	} else {
		$orgid = (isset($this->GetData['o'])) ? $this->GetData['o'] : $c->GetDefaultCompany();
	}

	$this->PutData ('Verb', 'Create');
	$this->PutData ('OrgID', $orgid);
*/
}
}
?>