<table width="100%" border="0" cellpadding="3" cellspacing="0" class="table">
<?php
$db = $this->Model();

$lastpr = '_';
$stripe = 1;
foreach ($data as $dt) {
	$pr = $dt->completedon;
	if ($lastpr != $pr) {
		$lastpr = $pr;
		$stripe = 1;

?>
	<tr>
		<td></td>
	</tr>
	<tr class="tabletitle"><td colspan="9">
<?php
echo ($pr) ? 'Completed on '.$pr : 'Not complete';
?>
	</td></tr>
	<tr class="ColumnTitles">
		<th>Area</th>
		<th>Task</th>
		<th>Due</th>
		<th>Assigned to</th>
		<th>Completed</th>
		<th>Approved</th>
		<th>Price</th>
		<th colspan="2"></th>
	</tr>
<?php
	}
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td><b><?php echo $dt->project; ?>: </b><?php echo $dt->area; ?></td>
		<td><?php $this->TaskInfoLink($dt->taskid, $dt->task); ?></a></td>
		<td><?php echo $dt->needby; ?></td>
		<td><?php echo $dt->assignedto; ?></td>
		<td><?php echo $dt->completedon; ?></td>
		<td><?php echo $dt->approved; ?></td>
		<td><?php echo $dt->cost; ?></td>
<?php 
ShowTaskIcons($db->GetUserID(), $db->IsSupervisor(), $dt);
?>
	</tr>
<?php 
	$stripe = ($stripe > 1) ? 1 : 2;
} 
?>
</table>