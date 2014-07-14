<?php
include 'TaskController.php';

class taskinfo extends TaskController implements AlpController {

public function __construct($url)
{
	parent::TaskController($url);
}

function Start()
{
	$db = $this->Database();
	$okmsg = '';

	$this->LoadLibrary('iconlinks');
	$this->LoadLibrary('taskicons');
	$this->ApproveTasks();

	if (isset($this->PostData['Keyword'])) {
		if (is_numeric($this->PostData['Keyword'])) {
			$taskid = $this->PostData['Keyword'];
		}
	} else if (isset($this->PostData['TaskID'])) {
		$taskid = $this->PostData['TaskID'];
		if (isset($this->PostData['Status'])) {
			if ($this->DataChanged(array('Area','Status','Priority','Description'))) {
				if (!$db->EditTask($taskid, 
						$this->PostData['Area'], 
						$this->PostData['Status'], 
						$this->PostData['Priority'], 
						$this->PostData['Description']))
					$okmsg .= 'Task status was successfully changed. ';
			}
			if ($this->DataChanged('NeedBy')) {
				if (!$db->EditTaskDates($taskid, '', $this->PostData['NeedBy']))
					$okmsg .= 'Task need by date was successfully changed. ';
			}
			if ($this->DataChanged('AssignTo')) {
				if (!$db->EditTaskAssignment($taskid, 'T', $this->PostData['AssignTo'])) {
					$email = $this->LoadClass(array('EmailClass', 'TaskEmailClass'));
					$email->SendTaskAssigned($taskid);
					$okmsg .= 'Task assigned to was successfully changed. ';
				}
			}
			if ($this->DataChanged('Cost')) {
				$cost = $this->PostData['Cost'];
				if (!$db->EditTaskCost($taskid, $cost))
					$okmsg .= 'Task cost was successfully changed. ';
			}
			if ($this->DataChanged('ApproveBy')) {
				if (!$db->EditTaskAssignment($taskid, 'A', $this->PostData['ApproveBy']))
					$okmsg .= 'Task approval resonsibility has been changed. ';
			}
			if ($this->DataChanged('SubmittedBy')) {
				if (!$db->EditTaskAssignment($taskid, 'S', $this->PostData['SubmittedBy']))
					$okmsg .= 'Task submitted by user has been changed. ';
			}
			if ($this->DataChanged(array('StartMS','EndMS'))) {
				if (!$db->EditTaskMilestones($taskid, $this->PostData['StartMS'], $this->PostData['EndMS']))
					$okmsg = 'Task milestones were successfully changed. ';
			}
		} else if (isset($this->PostData['NoteID'])) {
			if (!$db->EditTaskNote($taskid, $this->PostData['NoteID'], $this->PostData['Notes']))
				$okmsg = 'Task note was successfully changed';
		} else if (isset($this->PostData['MAX_FILE_SIZE'])) {
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

				if (!$db->AddTaskFile($taskid, $filename, $type, $this->PostData['FileDescr'], $tmpName))
					$okmsg = 'Attachment was successfully uploaded';
			}
		} else if (isset($this->PostData['EndMS'])) {
			if ($this->DataChanged(array('StartMS','EndMS')))
				if (!$db->EditTaskMilestones($taskid, $this->PostData['StartMS'], $this->PostData['EndMS']))
					$okmsg = 'Task milestones were successfully changed';
		} else if (isset($this->PostData['NoteID'])) {
			if (!$db->EditTaskNote($taskid, $this->PostData['NoteID'], $this->PostData['Notes']))
				$okmsg = 'Task note was successfully changed';
		} else if (isset($this->PostData['Notes'])) {
			if (!$db->AddTaskNote($taskid, $this->PostData['Notes']))
				$okmsg = 'Task note was successfully changed';
			
		} else {
			$this->LoadLibrary('sendmessage');
			SendContactMessage($this);
		}
	} else if (isset($this->GetData['apprid'])) {
		$taskid = $this->GetData['apprid'];
	} else if (isset($this->GetData['delid'])) {
		$taskid = $this->GetData['delid'];
		if ($db->DeleteTask($taskid) == 0)
			$this->RedirectTo($this->Cookie()->GetLastTaskPage());
	} else if (isset($this->GetData['undelid'])) {
		$taskid = $this->GetData['undelid'];
		if ($db->UndeleteTask($taskid) == 0)
			$this->RedirectTo($this->Cookie()->GetLastTaskPage());
	} else {
		$taskid = $this->GetData['tid'];
		if (isset($this->GetData['dn']))
			$db->DeleteTaskNote($taskid, $this->GetData['dn']);
		else if (isset($this->GetData['df']))
			$db->DeleteTaskFile($taskid, $this->GetData['df']);
	}

	$this->PutData ('PageHeading', array('filters'));
	$this->PutData ('TaskID', $taskid);
	$this->PutData ('OKMsg', $okmsg);
	$this->LoadView('home');
}
}
?>