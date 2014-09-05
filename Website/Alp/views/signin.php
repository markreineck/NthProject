<?php
$db = $this->Model();
$form = $this->Forms();
$errmsg = $db->ErrorMsg();
$ajax = $this->LoadClass('AjaxClass');
$c = $this->Cookie();
?>

<h1>Sign In to a Project</h1>
<?php
$form->ShowFormErrors($errmsg);
$prjlist = $db->ReadUserProjectList();
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<table border="0" cellpadding="2" cellspacing="0" class="">
<?php
$ajax->AddArg('u', $db->GetUserID());
$form->ShowListField ('Project', 'Project', $prjlist, 1, '', 
	$ajax->QueryThis('GetProjectTaskNames', 'Task', 'id'));
$form->ShowListField ('Task', 'Task');
?>
		<tr>
			<td>
			</td>
			<td>
<?php
$form->ShowSubmitButton('Sign In');
$form->ShowHiddenField('SessionID', $c->GetSessionID());
?>
		</td>
		</tr>
</table><div id="TestList"></div>
</form>
<?php
$form->FormValidation();
$ajax->AjaxBase();
?>