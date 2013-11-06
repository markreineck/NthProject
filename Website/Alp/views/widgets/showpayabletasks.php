<?php
//$c = $this->Cookie();
//$db = $this->Database();
$form = $this->Forms();
?>
<table width="100%" border="0" cellpadding="3" cellspacing="0" class="table">
	<tr class="ColumnTitles">
		<th>Project</th>
		<th colspan="2">Task</th>
		<th>Assigned to</th>
		<th>Completed</th>
		<th>Approved</th>
		<th>Price</th>
		<th>Paid</th>
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
		<td><b><?php echo $dt->project; ?>: </b><?php echo $dt->area; ?></td>
		<td><?php $this->TaskInfoLink($dt->taskid, $dt->taskid); ?></a></td>
		<td><?php $this->TaskInfoLink($dt->taskid, $dt->task); ?></a></td>
		<td><?php echo $dt->assignedto; ?></td>
		<td><?php echo $dt->complete; ?></td>
		<td><?php echo $dt->approved; ?></td>
		<td><?php echo $dt->cost; ?></td>
		<td>
<?php
	$form->ShowCheckBox('Paid'.$x, 1, ($dt->paid) ? 1 : 0);
	$form->ShowHiddenField('TaskID'.$x, $dt->taskid);
?>
		</td>
	</tr>
<?php 
	$stripe = ($stripe > 1) ? 1 : 2;
	$x++;
} 
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td colspan="5"></td>
		<td><b>Total:</b></td>
		<td><?php echo $total; ?></td>
		<td>
		</td>
	</tr>
</table>
<?php
$form->ShowHiddenField('TaskCnt', $x);
?>
