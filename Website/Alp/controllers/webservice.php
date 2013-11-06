<?php
class webservice extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::AlpFramework($url);
}

function Start()
{
	switch ($this->Controller(1)) {
		case 'Test':
			echo 'AJAX Test';
			break;

		case 'Authenticate':
			$db = $this->LoadModel(array('DatabaseDB'));
			$status->ErrorCode = 0;
			$status->ErrorMsg = '';
			$key = $_POST['MobileKey'];
			$email = $_POST['Email'];

			$sql = "select userid from users where email='$email' and mobilekey='$key' and mobilecode is null and mobileid is null";
			$uid = $db->Select($sql);
			$devid = 1;
			$code='1234567890';
			$sql = "call ActivateMobile($uid, $devid, '$code')";
			$status->ErrorCode = $db->ExecuteProd($sql);
			if ($status->ErrorCode) {
				$status->ErrorMsg = $db->ErrorMsg();
			} else {
				$status->DeviceID = $devid;
				$status->DeviceCode = $code;
			}
print_r($data);
			break;

		case 'GetLists':
			$status->ErrorCode = 0;
			$status->ErrorMsg = '';
			$db = $this->LoadModel(array('DatabaseDB'));
			$data->UserList = $db->ReadUserList();
			$data->TaskStatusList = $db->GetTaskStatusList();
			$data->ProjectList = $db->ReadProjectList();
			$data->Status = $status;
print_r($data);
			break;

		default:
			echo 'Service Error';
	}
}

function AuthenticateDevice()
{
	$key = $_POST['MobileKey'];
	$device = $_POST['DeviceID'];

	$sql = 'select userid, mobilecode from users where mobileid='.$device;
	$row = $this->Database()->SelectRow($sql);
	return $row['userid'];
}

}
?>