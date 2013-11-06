<table width="100%" border="0" cellpadding="3" cellspacing="0" class="table">
	<tr class="ColumnTitles">
		<th>Billed</th>
		<th>Project</th>
		<th colspan="2">Task</th>
		<th>Assigned to</th>
		<th>Price</th>
		<th>&nbsp;</th>
	</tr>
<?php
$lastpr = '_';
$stripe = 1;
$x = 0;
$total = 0;
foreach ($data as $dt) {
	$total += $dt->cost;
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td><?php echo $dt->billed; ?></td>
		<td><b><?php echo $dt->project; ?>: </b><?php echo $dt->area; ?></td>
		<td><?php $this->TaskInfoLink($dt->taskid, $dt->taskid); ?></a></td>
		<td><?php $this->TaskInfoLink($dt->taskid, $dt->task); ?></a></td>
		<td><?php echo $dt->assignedto; ?></td>
		<td><?php echo $dt->cost; ?></td>
	</tr>
<?php 
	$stripe = ($stripe > 1) ? 1 : 2;
	$x++;
} 
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td colspan="3">&nbsp;</td>
		<td colspan="2">Total:</td>
		<td><?php echo $total; ?></td>
		<td>&nbsp;</td>
	</tr>
</table>
