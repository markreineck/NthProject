<?php
$db = $this->Model();
?>

<?php
$lastpr = '_';
foreach ($data as $dt) {
	$pr = $dt->complete;
	if ($lastpr != $pr) {
		$lastpr = $pr;
		$bg = "#F0F1F2";

?>
<h3>Complete: <?php echo $pr; ?></h3>
<table class="table table-striped">
	<tr align="left">
		<th width="150">Project</th>
		<th colspan="2">Task</th>
		<th>Completed by</th>
		<th>Priority</th>
		<th colspan="2"></th>
	</tr>
<?php //bgcolor="#C1E0FF" 
	}
	$bg = ($bg=='#F0F1F2' ? '#FFFFFF' : '#F0F1F2');
?>
	<tr bgcolor="<?php echo $bg;?>">
		<td><b><?php echo $dt->project; ?>: </b><?php echo $dt->area; ?></td>
		<td width="80"><?php $this->TaskInfoLink($dt->taskid, $dt->taskid); ?></a></td>
		<td><?php $this->TaskInfoLink($dt->taskid, $dt->task); ?></a></td>
		<td width="100"><?php echo $dt->assignedto; ?></td>
		<td width="50"><?php echo $dt->priority; ?></td>
<?php 
ShowTaskIcons($db->GetUserID(), $db->IsSupervisor(), $dt);
?>
	</tr></table>
	<?php 
} 
?>
