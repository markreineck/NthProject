<?php
function SendContactMessage($nth)
{
	$okmsg = '';
	if (isset($_POST['MsgSend'])) {

		$db = $nth->Model();
		$okmsg = 'Message was successfully sent!';
		
		$MsgUserEmail = $nth->PostedData('MsgUserEmail');
		$MsgSubject = $nth->PostedData('Subject');
		$MsgContent = $nth->PostedData('Message');
		
		$msg = $MsgContent;			
		
		$email = $nth->LoadClass('EmailClass');
		$email->From($db->GetUserEmail());
		$email->To($MsgUserEmail);
		$email->Subject($MsgSubject);
		$email->Message($msg);
		$email->Send();
	
		$db->AddTaskMessage($nth->PostedData('TaskID'), $nth->PostedData('MsgUserID'), $MsgSubject, $MsgContent);
	}
	return $okmsg;
}
?>