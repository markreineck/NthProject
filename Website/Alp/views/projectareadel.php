<?php
$db = $this->Model();
$c = $this->Cookie();
$form = $this->Forms();

if ($db->IsProjectSuperUser($PrjID)) {
	$area = $db->ReadProjectArea($AreaID);
?>
<h1>Delete Project Area</h1>
<h2><?php echo $area->name; ?></h2>
<?php
	$form->ShowFormErrors($db->ErrorMsg());
	$paid = ($this->UserSetting('TaskCost')) ? $db->CountProjectAreaPaidTasks($AreaID) : 0;
	$active = $db->CountProjectAreaActiveTasks($AreaID);
	$done = $db->CountProjectAreaCompleteTasks($AreaID);
	$all = $db->CountProjectAreaTasks($AreaID);
	if ($all > 0) {
?>
<p><?php echo $area->name; ?> has <?php echo $all; ?> tasks including:</p>
<?php
		if ($paid) {
?>
<p><?php echo $paid; ?> paid tasks</p>
<?php
		}
?>
<p><?php echo $done; ?> completed tasks</p>
<p><?php echo $active; ?> active tasks</p>
<?php
	} else {
?>
<p><?php echo $area->name; ?> has no tasks.</p>
<?php
	}
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
	<table border="0" cellpadding="3" cellspacing="0" class="">
<?php
	$arealist = $db->ListProjectAreas($PrjID);

	$form->ShowHiddenField ('ProjectID', $PrjID);
	$form->ShowHiddenField ('SourceArea', $AreaID);
	if ($all > 0)
		$form->ShowListField ('Move Tasks to', 'DestArea', $arealist, 1, $AreaID);
?>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td></td>
			<td>
<?php
	$form->ShowSubmitButton('Delete');
	$form->ShowRedirectButton('projectareas');
?>
			</td>
		</tr>
	</table>
</form>
<?php
}
?>
