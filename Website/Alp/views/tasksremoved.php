<?php
$db = $this->Database();
?>
<h1>Deleted Tasks</h1>
<?php
$data = $db->ListDeletedTasks($this->Cookie());
echo $db->ErrorMsg();

$this->PutData ('data', $data);
?>
<table width="100%" border="0" cellpadding="3" cellspacing="0" class="table">
<?php
$lastpr = '_';
$stripe = 1;
foreach ($data as $dt) {
	$pr = $dt->removedon;
	if ($lastpr != $pr) {
		$lastpr = $pr;
		$stripe = 1;

?>
	<tr>
		<td></td>
	</tr>
	<tr class="tabletitle"><td colspan=8>Removed On <?php echo $pr; ?></td></tr>
	<tr class="ColumnTitles">
		<th>Project</th>
		<th colspan="2">Task</th>
		<th>Assigned to</th>
		<th colspan="2"></th>
	</tr>
<?php
	}
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td><a class="parent"  href="projectinfo?id=<?php echo $dt->prjid; ?>"><?php echo $dt->project; ?></a>: <?php echo $dt->area; ?></td>
		<td><?php $this->TaskInfoLink($dt->taskid, $dt->taskid); ?></a></td>
		<td><?php $this->TaskInfoLink($dt->taskid, $dt->task); ?></a></td>
		<td><?php echo $dt->assignedto; ?></td>
<?php 
//ShowTaskIcons($db->GetUserID(), $db->IsSupervisor(), $dt);
?>
	</tr>
<?php 
	$stripe = ($stripe > 1) ? 1 : 2;
} 
?>
</table>