<?php
include 'TaskController.php';

class taskinfo extends TaskController implements AlpController {

private function ShowPage($taskid, $okmsg)
{
	$this->LoadLibrary('iconlinks');
	$this->LoadLibrary('taskicons');
	$this->PutData ('PageHeading', array('filters'));
	$this->PutData ('TaskID', $taskid);
	$this->PutData ('OKMsg', $okmsg);
	$this->LoadView('template2015');
}

function Post()
{
	$db = $this->Model();
	$okmsg = '';

	if ($this->IsPosted('Keyword')) {
		$t = $this->PostedDigit('Keyword');
		if (is_numeric($t)) {
			$taskid = $t;
		}
	} else if ($this->IsPosted('TaskID')) {
		$taskid = $this->PostedDigit('TaskID');
		if ($this->IsPosted('Status')) {
			if ($this->DataChanged(array('Area','Status','Priority','Description','Branch'))) {
				if (!$db->EditTask($taskid, 
						$this->PostedDigit('Area'), 
						$this->PostedDigit('Status'), 
						$this->PostedDigit('Priority'), 
						$this->PostedString('Description'),
						$this->PostedString('Branch')))
					$okmsg .= 'Task status was successfully changed. ';
			}
			if ($this->DataChanged('NeedBy')) {
				if (!$db->EditTaskDates($taskid, '', $this->PostedString('NeedBy')))
					$okmsg .= 'Task need by date was successfully changed. ';
			}
			if ($this->DataChanged('AssignTo')) {
				if (!$db->EditTaskAssignment($taskid, 'T', $this->PostedDigit('AssignTo'))) {
					$email = $this->LoadClass(array('EmailClass', 'TaskEmailClass'));
					$email->SendTaskAssigned($taskid);
					$okmsg .= 'Task assigned to was successfully changed. ';
				}
			}
			if ($this->DataChanged('Cost')) {
				$cost = $this->PostedNumber('Cost');
				if (!$db->EditTaskCost($taskid, $cost))
					$okmsg .= 'Task cost was successfully changed. ';
			}
			if ($this->DataChanged('ApproveBy')) {
				if (!$db->EditTaskAssignment($taskid, 'A', $this->PostedDigit('ApproveBy')))
					$okmsg .= 'Task approval resonsibility has been changed. ';
			}
			if ($this->DataChanged('SubmittedBy')) {
				if (!$db->EditTaskAssignment($taskid, 'S', $this->PostedDigit('SubmittedBy')))
					$okmsg .= 'Task submitted by user has been changed. ';
			}
			if ($this->DataChanged(array('StartMS','EndMS'))) {
				if (!$db->EditTaskMilestones($taskid, $this->PostedDigit('StartMS'), $this->PostedDigit('EndMS')))
					$okmsg = 'Task milestones were successfully changed. ';
			}
		} else if ($this->IsPosted('NoteID')) {
			if (!$db->EditTaskNote($taskid, $this->PostedDigit('NoteID'), $this->PostedData('Notes')))
				$okmsg = 'Task note was successfully changed';
		} else if ($this->IsPosted('MAX_FILE_SIZE')) {
			if ($_FILES['Attachment']['size'] < 1) {
				$db->SetError(1,'No file was uploaded');
			} else if ($_FILES['Attachment']['error']) {
				$db->SetError(1,'There was an error uploading the file to the server.');
			} else if (!is_uploaded_file($_FILES['Attachment']['tmp_name'])) {
				$db->SetError('File is not an uploaded file');
			} else {
				// Temporary file name stored on the server
				$tmpName  = $_FILES['Attachment']['tmp_name'];  
				$type  = $_FILES['Attachment']['type'];
				$filename = $db->MakeStringValue($_FILES['Attachment']['name']);

				if (!$db->AddTaskFile($taskid, $filename, $type, $this->PostedString('FileDescr'), $tmpName))
					$okmsg = 'Attachment was successfully uploaded';
			}
		} else if ($this->IsPosted('EndMS')) {
			if ($this->DataChanged(array('StartMS','EndMS')))
				if (!$db->EditTaskMilestones($taskid, $this->PostedDigit('StartMS'), $this->PostedDigit('EndMS')))
					$okmsg = 'Task milestones were successfully changed';
		} else if ($this->IsPosted('NoteID')) {
			if (!$db->EditTaskNote($taskid, $this->PostedDigit('NoteID'), $this->PostedData('Notes')))
				$okmsg = 'Task note was successfully changed';
		} else if ($this->IsPosted('Notes')) {
			if (!$db->AddTaskNote($taskid, $this->PostedData('Notes')))
				$okmsg = 'Task note was successfully changed';
			
		} else if (isset($_POST['MsgSend'])) {
			$this->LoadLibrary('sendmessage');
			$okmsg = SendContactMessage($this);
		}
	}
	$this->ShowPage($taskid, $okmsg);
}

function Start()
{
	$db = $this->Model();
	$okmsg = '';

	$this->ApproveTasks();

	if ($this->IsGet('apprid')) {
		$taskid = $this->GetNumber('apprid');
	} else if ($this->IsGet('delid')) {
		$taskid = $this->GetNumber('delid');
		if ($db->DeleteTask($taskid) == 0)
			$this->RedirectTo($this->Cookie()->GetLastTaskPage());
	} else if ($this->IsGet('undelid')) {
		$taskid = $this->GetNumber('undelid');
		if ($db->UndeleteTask($taskid) == 0)
			$this->RedirectTo($this->Cookie()->GetLastTaskPage());
	} else {
		$taskid = $this->GetNumber('tid');
		if ($this->IsGet('dn'))
			$db->DeleteTaskNote($taskid, $this->GetNumber('dn'));
		else if ($this->IsGet('df'))
			$db->DeleteTaskFile($taskid, $this->GetNumber('df'));
	}
	$this->ShowPage($taskid, $okmsg);
}

}
?>