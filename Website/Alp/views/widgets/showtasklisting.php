<?php
if ($data && count($data)) {
?>
<?php /*?><div class="result-msg">Sorted By <strong><?php echo $TaskSort; ?></strong></div><?php */?>
<table class="table table-striped">
<?php
	$db = $this->Model();

	$lastpr = '_';
	$stripe = 1;
	foreach ($data as $dt) {
		$pr = $dt->$GroupField;
		if ($lastpr != $pr) {
			$lastpr = $pr;
			$stripe = 1;
?>
	<tr class="TableTitle"><td colspan=8><div><?php echo "$GroupTitle: $pr"; ?></div></td></tr>
	<tr class="ColumnTitles">
		<th>Project</th>
		<th colspan="2">Task</th>
		<th><?php echo $datefield; ?></th>
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
} else {
?>
<p>No results found.</p>
<?php
}
?>
