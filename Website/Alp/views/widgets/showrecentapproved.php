<table width="100%" border="0" cellpadding="3" cellspacing="0" class="table">
<?php
$db = $this->Model();

$lastpr = '_';
foreach ($data as $dt) {
	$pr = $dt->approved;
	if ($lastpr != $pr) {
		$lastpr = $pr;
		$bg = "#F0F1F2";

?>
	<tr>
		<td></td>
	</tr>
	<tr class="SectionTitle">
		<td colspan=8>Approved: <?php echo $pr; ?></td></tr>
	<tr bgcolor="#C1E0FF" align="left">
		<th>Project</th>
		<th colspan="2">Task</th>
		<th>Completed by</th>
		<th>On</th>
		<th>Priority</th>
		<th colspan="2"></th>
	</tr>
<?php
	}
	$bg = ($bg=='#F0F1F2' ? '#FFFFFF' : '#F0F1F2');
?>
	<tr bgcolor="<?php echo $bg;?>">
		<td><b><?php echo $dt->project; ?>: </b><?php echo $dt->area; ?></td>
		<td><?php $this->TaskInfoLink($dt->taskid, $dt->taskid); ?></a></td>
		<td><?php $this->TaskInfoLink($dt->taskid, $dt->task); ?></a></td>
		<td><?php echo $dt->assignedto; ?></td>
		<td><?php echo $dt->complete; ?></td>
		<td><?php echo $dt->priority; ?></td>
<?php 
ShowTaskIcons($db->GetUserID(), $db->IsSupervisor(), $dt);
?>
	</tr>
	<?php 
} 
?>
</table>