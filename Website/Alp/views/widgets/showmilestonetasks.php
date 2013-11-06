<?php
if (!$data || count($data) < 1)) {
?>
<p>Please Select a Milestone</p>
<?php
} else {
?>
<table width="100%" border="0" cellpadding="3" cellspacing="0" class="table">
<?php
$db = $this->Database();

$lastpr = '_';
$stripe = 1;
foreach ($data as $dt) {
	$pr = $dt->status;
	if ($lastpr != $pr) {
		$lastpr = $pr;
		$stripe = 1;

?>
	<tr>
		<td></td>
	</tr>
	<tr class="tabletitle"><td colspan=8><?php echo $dt->status; ?></td></tr>
	<tr class="ColumnTitles">
		<th>Project</th>
		<th colspan="2">Task</th>
		<th>Due</th>
		<th>Assigned to</th>
		<th>Status</th>
		<th colspan="2"></th>
	</tr>
<?php
	}
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td><a class="parent"  href="projectinfo?id=<?php echo $dt->prjid; ?>"><?php echo $dt->project; ?></a>: <?php echo $dt->area; ?></td>
		<td><?php $this->TaskInfoLink($dt->taskid, $dt->taskid); ?></a></td>
		<td><?php $this->TaskInfoLink($dt->taskid, $dt->task); ?></a></td>
		<td><?php echo $dt->needby; ?></td>
		<td><?php echo $dt->assignedto; ?></td>
		<td id="status<?php echo $dt->taskid; ?>"><?php echo TaskStatus($dt); ?></td>
<?php 
ShowTaskIcons($db->GetUserID(), $db->IsSupervisor(), $dt);
?>
	</tr>
<?php 
	$stripe = ($stripe > 1) ? 1 : 2;
} 
?>
</table>
<?php
}
?>
