<?php
$db = $this->Model();
$form = $this->Forms('ProjectFormClass');
$c = $this->Cookie();
$errmsg = $db->ErrorMsg();

$data = $db->ReadTaskDesc($TaskID);

if (!empty($data->removed)) {
	$status = 'Deleted';
	$sid = 'D';
} else if (!empty($data->approved)) {
	$status = 'Approved';
	$sid = 'A';
} else if (!empty($data->complete)) {
	$status = 'Complete';
	$sid = 'C';
} else {
	$status = $data->status;
	$sid = 'W';
}

$super = ($db->IsGlobalSupervisor() || $db->IsSupervisor() == $data->orgid);

if (!$super) {
	$privs = $db->ReadMyProjectRights($data->prjid);
	if ($privs->superuser)
		$super = true;
}

if ($sid == 'A' || $sid == 'D') {
	$hasedit = false;
	$canedit = false;
	$canassn = false;
} else {
	$hasedit = $super || $privs->edit;
	$canedit = ($hasedit || $data->submittedby == $db->GetUserID());
	$canassn = ($super || $privs->assign || $data->submittedby == $db->GetUserID());
}
?>

<input type="hidden" value="mysample101" />
<h1><?php echo $data->name; ?>&nbsp;&nbsp;
<span class="taskicons">
<?php
MakeIconLink('circle-arrow-left', $c->GetLastTaskPage(), 'Back', '', 'Blue');

MakeIconLink('calendar', 'taskhistory?id='.$TaskID, 'Task History',  '', 'Yellow');


if ($super || $canedit || $data->assignedto == $db->GetUserID()) {
	if ($sid == 'W')
		TaskDoneLink($TaskID);
}

if ($super || $data->approvedby == $db->GetUserID()) {
	if ($sid != 'A' && $sid != 'D')
		TaskApproveLink($TaskID);
	if ($sid != 'W' && $sid != 'D')
		TaskDispproveLink($TaskID);
}

if ($canedit) {
	if ($sid != 'A' && $sid != 'D')
		echo "<a href='?delid=".$TaskID."' class=\"ActionIconLinks Red\" onclick=\"return confirm('Delete Task?');\"><span class=\"glyphicon glyphicon-trash\"></span></a>";
}
if ($super || $canedit) {
	if(!empty($data->removed) ) {
		MakeIconLink('repeat', '?undelid='.$TaskID, 'Undelete Task', '');	
	} 
}

?>

<?php
AddTaskToProjectLink($data->prjid);
?>

</span>
</h1><br clear="all" />
<div class="push-left container-setion">
<?php
$form->ShowFormErrors($errmsg, $OKMsg);
?>
<div id="ViewTask">
	<div class="pagesection TaskInfo">
		<div class="Section-func">
<?php
if ($canedit) { MakeJSIcon('pencil', "ToggleViews('ViewTask','EditTask')", 'Edit Task', 33); }
?>
		</div>
	<div>
	<label>Area: </label><strong><span><a href="projectinfo?id=<?php echo $data->prjid; ?>"><?php echo $data->project; ?></a>: <?php echo $data->area; ?></span></strong><br />
    <label>Priority: </label><strong><span><?php echo $data->priority; ?></span></strong><br />
    <label>Status: </label><strong><span><?php echo $status; ?>Submitted on:<?php echo $data->submittedon; ?></span></strong><br />
<?php
if (!empty($data->complete)) {
?>
	<label>Completed on: </label><strong><span><?php echo $data->complete; ?></span</strong>><br />
<?php
}
if (!empty($data->approved)) {
?>
	<label>Approved on: </label><strong><span><?php echo $data->approved; ?></span></strong><br />
<?php
}
?>
</div>

<?php
		
function ShowAssignment($label, $userid, $name, $email, $taskid, $taskname)
{
	if ($userid) {
?>
<?php echo $label; ?><strong><?php echo $name; ?></strong>
<?php
		MakeJSIcon('comment', "ShowContactPerson($userid,$taskid,'$name','$email','$taskname')", 'Contact', '');
?>

				
<?php
	}
}

ShowAssignment('Submitted By: ', $data->submittedby, $data->submittedname, $data->submittedemail, $TaskID, $data->name); echo "<br>";
ShowAssignment('Assigned To: ', $data->assignedto, $data->assignedname, $data->assignedemail, $TaskID, $data->name); echo "<br>";
ShowAssignment('Approval By: ', $data->approvedby, $data->approvedname, $data->approvedemail, $TaskID, $data->name); echo "<br>";

//if ($db->HasMilestones()) {
if ($this->UserSetting('Milestones')) {
	if (!empty($data->startmsname)) {
?>
			Start after:<?php echo $data->startmsname; ?>
<?php
	}
	if (!empty($data->endmsname)) {
?>
			Complete By:<?php echo $data->endmsname; ?>
<?php
	}
}
if ($this->UserSetting('TaskDates') && !empty($data->startafter) || !empty($data->needby)) {
?>
			Complete By:<?php echo $data->needby; ?>
<?php
}
if ($this->UserSetting('TaskCost') && !empty($data->cost) &&
	($db->IsGlobalSupervisor() || $db->GetUserID() == $data->assignedto)) {
?>
			Cost:<?php echo $data->cost; ?>
<?php
}
?>
		
	</div>

<div class="portfolio pagesection">
<h2>Attachments&nbsp;&nbsp;&nbsp;
<?php
if ($canedit) {
	MakeJSIcon('plus', "document.getElementById('AttachImage').style.display='block'", 'Attach Image', '');
/*
?>
<img src="/image/plus.png" alt="Attach Image" title="Attach Image" onClick="document.getElementById('AttachImage').style.display='block'" class="icons">
<?php
*/
}
?>
</h2>


<table border="0" cellpadding="5" cellspacing="0" class="tabledata">
<?php
$files = $db->ReadTaskFiles($TaskID);
if ($files) {
	foreach ($files as $f) {
?>
	<tr>
		<td><?php echo $f->uploadedon; ?></td>
		<td><?php echo $f->loadedby; ?></td>
		<td><a href="http://projectserver.nth-generation.com/showimg.php?id=<?php echo $f->fileid; ?>" target=_new><?php echo $f->descr; ?></a></td>
		<td></td>
		<td><img src="/image/x.png" alt="Delete" title="Delete Attachment" onClick="window.location='<?php echo $this->Controller()."?tid=$TaskID&df=$f->fileid"; ?>'" class="icons"></td>
	</tr>
<?php
	}
}
?>
</table>

</div>

</div>
<?php
if ($canedit || $canassn) {
?>
<div name="EditTask" id="EditTask" class="memoarea pagesection" <?php echo 'style="display:none"'; ?>>
<h2>Edit Task</h2>
<form method="post" action="<?php echo $this->Controller(); ?>" <?php $form->ShowOnSubmit(); ?>>
	<table border="0" cellpadding="3" cellspacing="5" class="tabledata">
<?php
	$arealist = $db->GetProjectAreaList($data->prjid);
	$statuslist = $db->GetTaskStatusList();

if ($canedit) {
	$form->ShowTextField ('Description', 'Description', 80, 40, $data->name, 1);
	$form->ShowListField ('Area', 'Area', $arealist, 2, $data->areaid);
	$form->ShowListField ('Status', 'Status', $statuslist, 2, $data->statusid);
	$form->ShowNumericListField ('Priority', 'Priority', 1, 5, 1, $data->priority, 2);
}
if ($canassn) {
	$form->ShowListField ('Submitted by', 'SubmittedBy', $db->GetSubmittedByList($data->prjid), 0, $data->submittedby);
	$form->ShowListField ('Assign to', 'AssignTo', $db->GetAssignToList($data->prjid), 0, $data->assignedto);
	$form->ShowListField ('Approve by', 'ApproveBy', $db->GetApproveByList($data->prjid), 0, $data->approvedby);
}
//if ($canedit && $db->HasMilestones()) {
if ($canedit) {
	if ($this->UserSetting('Milestones')) {
		$mslist = $db->GetMilestoneList($data->prjid);
		if ($mslist) {
			$form->ShowListField ('Start After', 'StartMS', $mslist, 0, $data->startmilestone);
			$form->ShowListField ('Need By', 'EndMS', $mslist, 0, $data->endmilestone);
		}
	}
	if ($this->UserSetting('TaskDates')) {
		$form->ShowDatePickerField  ('Need By', 'NeedBy', $data->needby, false,"+2y", "-1y");
	}
}
if ($canedit && $this->UserSetting('TaskCost')) {	
	$form->ShowNumericField ('Cost', 'Cost', 0, 1000000, 7, $data->cost);
}
?>

		<tr>
			<td></td>
			<td>
<?php
	$form->ShowHiddenField ('TaskID', $TaskID);
	$form->ShowSubmitButton();
	$form->ShowJavaScriptButton("ToggleViews('EditTask','ViewTask')", 'Cancel');
?>
			</td>
		<tr>		
	</table>
</form>
</div>
<?php
}
?>
<div name="AttachImage" id="AttachImage" class="memoarea pagesection" <?php echo 'style="display:none"'; ?>>
<h2>Attach File</h2>
<form enctype="multipart/form-data" method="post" action="<?php echo $this->Controller(); ?>" <?php $form->ShowOnSubmit(); ?>>
	<input type="hidden" name="MAX_FILE_SIZE" value="200000000">
	<table border="0" cellpadding="3" cellspacing="5" class="tabledata">
		<tr>
			<td><label for="FileDescr" class="LabelClass">File:</label></td>
			<td><input name="Attachment" type="file" id="Attachment"></td>
	<?php
		$form->ShowTextField ('Description', 'FileDescr', 80, 49);
	?>
		<tr>
			<td>&nbsp;</td>
			<td>
	<?php
		$form->ShowHiddenField ('TaskID', $TaskID);
		$form->ShowSubmitButton();
		$form->ShowJavaScriptButton("ToggleViews('AttachImage','ViewTask')", 'Cancel');
	?>
			</td>
		</tr>
	</table>
</form></div>
<?php
$this->LoadView('contactform');
?>
</div>

<div class="push-left container-setion">
<div class="memoarea pagesection Notes">
<h2>Notes&nbsp;&nbsp;&nbsp;
<div class="Section-func">
<?php
if ($canassn) {
	MakeJSIcon('plus', "document.getElementById('AddNoteForm').style.display='block'", 'Add a Note','');
}
?>
</div>
</h2>
<?php
$data = $db->ReadTaskNotes($TaskID);

if ($data) {
	foreach ($data as $dx) {
?>
	<div class="Container-TaskNote">
    <div class="Section-func">
<?php
		MakeJSIcon('pencil', "ShowEditNote($dx->noteid)", 'Edit Note', '');
		MakeIconLink('x.png', $this->Controller()."?tid=$TaskID&dn=$dx->noteid", 'Delete Note');
		if ($dx->fromid > 0 && $dx->fromid != $db->GetUserID())
			MakeJSIcon('mail.png', "FuncContactPerson($dx->fromid,'$dx->fromname','$dx->email')", 'Reply');

	}
?>
	</div>
    	
	<div class="memoarea ViewNote" id="ViewNote<?php echo $dx->noteid; ?>" >
    <div class="Noteby"><?php echo "$dx->sent : <strong>$dx->fromname</strong>";?></div>
<?php
	echo str_replace('
', '<br>', $dx->message);
		if ($hasedit || $dx->fromid == $db->GetUserID()) {
?>
	</div>
</div>
	<?php
		if ($hasedit || $dx->fromid == $db->GetUserID()) {
?>
	<form name="EditNote<?php echo $dx->noteid; ?>" id="EditNote<?php echo $dx->noteid; ?>" method="post" action="" <?php echo 'style="display:none"'; ?>>
		<textarea name="Notes" cols="45" rows="10" class="textbox"><?php echo $dx->message; ?></textarea>
		<br><br>
		<?php
			$form->ShowHiddenField ('TaskID', $TaskID);
			$form->ShowHiddenField ('NoteID', $dx->noteid);
			$form->ShowSubmitButton();
			$form->ShowJavaScriptButton("HideEditNote($dx->noteid)", 'Cancel');
?>
		<br clear="all">
	</form>
	<?php
		}	
?>
	<br clear="all">
	
	<?php
	}	
}
?>

<form name="AddNoteForm" id="AddNoteForm" method="post" action="<?php echo $this->Controller(); ?>" <?php echo 'style="display:none"'; ?>>


<div class="field-container">
    <label for="FileDescr" class="LabelClass">Notes:</label>
    <textarea name="Notes" cols="45" rows="10" class="textbox"></textarea>
</div>

<?php
	$form->ShowHiddenField ('TaskID', $TaskID);
	$form->ShowSubmitButton();
	$form->ShowJavaScriptButton("document.getElementById('AddNoteForm').style.display='none'", 'Cancel');
?>
		</td>
	</tr>
</table>
	<br clear="all">
</form>
</div>
</div>


<script type="text/javascript">
function ShowEditNote(id)
{
	v = document.getElementById('ViewNote'+id);
	e = document.getElementById('EditNote'+id);
	if (v && e) {
		v.style.display = 'none';
		e.style.display = 'block';
	}
}

function HideEditNote(id)
{
	v = document.getElementById('ViewNote'+id);
	e = document.getElementById('EditNote'+id);
	if (v && e) {
		e.style.display = 'none';
		v.style.display = 'block';
	}
}
</script>