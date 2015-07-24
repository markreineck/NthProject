<?php
if ($data) {

	$form = $this->Forms();

?>
<table class="table table-striped">
	<tr>
		<th>Area</th>
<?php
	if ($this->UserSetting('Milestones')) {
?>
		<th>Milestone</th>
<?php
	}
?>
		<th colspan="2">Task</th>
		<th>Completed</th>
		<th>Completed By</th>
		<th>Approved</th>
		<th>Approved By</th>
		<th>Release</th>
	</tr>
<?php
	$stripe = 1;
	$x = 0;

	foreach ($data as $dt) {
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td><?php echo $dt->area; ?></td>
<?php
		if ($this->UserSetting('Milestones')) {
?>
		<td><?php echo $dt->milestone; ?></td>
<?php
		}
?>
		<td><?php $this->TaskInfoLink($dt->taskid, $dt->taskid); ?></a></td>
		<td><?php $this->TaskInfoLink($dt->taskid, $dt->task); ?></a></td>
		<td><?php echo $dt->complete; ?></td>
		<td><?php echo $dt->assignedto; ?></td>
		<td><?php echo $dt->approved; ?></td>
		<td><?php echo $dt->approvedby; ?></td>
		<td>
<?php
		$form->ShowCheckBox('Release'.$x, $dt->taskid, ($dt->released) ? 1 : 0);
		$form->ShowHiddenField('TaskID'.$x, $dt->taskid);
?>
		</td>
	</tr>
<?php 
		$stripe = ($stripe > 1) ? 1 : 2;
		$x++;
	} 
?>
</table>
<?php
	$form->ShowHiddenField('TaskCnt', $x);
} else {
?>
	<p>No results found.</p>
<?php
}
?>
