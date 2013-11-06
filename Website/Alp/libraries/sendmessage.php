<?php
function SendContactMessage($nth)
{
	if (isset($nth->PostData['MsgSend'])) {
		$db = $nth->Database();
		$okmsg = 'Message was successfully sent!';
		
//		$MsgSenderEmail = $nth->PostData['MsgSenderEmail'];
		$MsgUserEmail = $nth->PostData['MsgUserEmail'];
		$MsgSubject = $nth->PostData['Subject'];
		$MsgContent = $nth->PostData['Message'];
		
		$msg = $MsgContent;			
		
		$email = $nth->LoadClass('EmailClass');
		$email->From($db->GetUserEmail());
		$email->To($MsgUserEmail);
		$email->Subject($MsgSubject);
		$email->Message($msg);
		$email->Send();
	
		$db->AddTaskMessage($nth->PostData['TaskID'], $nth->PostData['MsgUserID'], $MsgSubject, $MsgContent);
	}
}
?>