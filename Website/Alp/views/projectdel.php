<?php
$db = $this->Model();
$c = $this->Cookie();
$form = $this->Forms();
$errmsg = $db->ErrorMsg();


if ($db->IsSupervisor()) {

	$sql = 'select ifnull(count(*),0) from usertime where prjid=' . $PrjID;
	$time = $db->Select($sql);
	if (!$time) {
		$sql = 'select ifnull(count(*),0) from usertime u, tasks t, projectareas a where t.taskid=u.taskid and t.areaid=a.areaid and a.prjid=' . $PrjID;
		$time = $db->Select($sql);
	}
	if ($time > 0)
		$errmsg = 'You cannot delete a project that has time records.';

	$data = $db->ReadProject($PrjID);
?>
<h1>Delete Project: <?php echo $data->name; ?></h1>
<?php
	$form->ShowFormErrors($errmsg);

	if ($time < 1) {
		$sql = 'select count(*) from tasks t, projectareas a where t.areaid=a.areaid and t.complete is null and a.prjid=' . $PrjID;
		$cnt = $db->Select($sql);
		if ($cnt)
			echo "<p>The project has $cnt incomplete tasks assigned to it.</p>";
	}
?>
<br><br>
<?php
	if ($time < 1) {
?>
<p>Are you sure that you want to delete this project?</p>
<?php
	}
?>
<br><br>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<?php
	if ($time < 1) {
		$form->ShowHiddenField ('ProjectID', $PrjID);
		$form->ShowSubmitButton('Delete');
	}
	$form->ShowRedirectButton('projects');
	$form->ShowRedirectButton('projectedit?id='.$PrjID, 'Edit');
?>
</form>
<?php
}
?>
