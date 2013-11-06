<?php
$c = $this->Cookie();
$db = $this->Database();
$form = $this->Forms();
?>
<h1>Release Tasks</h1>
<?php
$form->ShowFormErrors($db->ErrorMsg(), $OKMsg);
?>
<form method="post" name="form" id="form">
<div id="TaskList" name="TaskList">
<?php
if ($c->GetDefaultProject() > 0) {
	$data = $db->ListTasksForRelease($c);
	$this->PutData ('data', $data);
//	$this->PutData ('UserID', $c->GetDefaultUser());
	$this->LoadView('widgets/showtaskstorelease');
} else {
?>
	<p>Select a project.</p>
<?php
}
?>
</div>
<br>
<?php
$form->ShowSubmitButton('Release');
?>
</form>
<br clear="all">
<?php
$this->Ajax()->AjaxBase();
?>