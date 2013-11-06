<?php
include 'UserController.php';

class useradd extends UserController implements AlpController {

public function __construct($url)
{
	parent::UserController($url);
}

function Start()
{
	$c = $this->Cookie();
	$this->PutData ('NextPage', 'useredit');

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
	$this->LoadView('home');
}
}
?>