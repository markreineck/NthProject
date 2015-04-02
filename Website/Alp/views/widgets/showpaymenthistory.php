<?php
function ShowTitle($date, $name)
{
?>
	<tr class="tabletitle"><td colspan="4"><?php echo "$date: $name"; ?></td></tr>
    <tr class="ColumnTitles">
		<th>Project</th>
		<th colspan="2">Task</th>
		<th>Price</th>
	</tr>
<?php
}

function ShowTotal($stripe, $total)
{
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td colspan="2"></td>
		<td align="right"><b>Total:</b></td>
		<td align="right"><?php echo $total; ?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
<?php
}
?>

<table class="table table-striped">
<?php
$lastdate = '';
$lastname = '';
$stripe = 1;
$x = 0;
$total = 0;
foreach ($data as $dt) {
	if ($lastdate != $dt->paid || $lastname != $dt->assignedto) {
		if ($total)
			ShowTotal($stripe, $total);
		ShowTitle($dt->paid, $dt->assignedto);
		$total = 0;
		$stripe = 1;
	}

	$total += $dt->cost;
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td><b><?php echo $dt->project; ?>: </b><?php echo $dt->area; ?></td>
		<td><?php $this->TaskInfoLink($dt->taskid, $dt->taskid); ?></a></td>
		<td><?php $this->TaskInfoLink($dt->taskid, $dt->task); ?></a></td>
		<td align="right"><?php echo $dt->cost; ?></td>
	</tr>
<?php 
	$stripe = ($stripe > 1) ? 1 : 2;
	$lastdate = $dt->paid;
	$lastname = $dt->assignedto;
	$x++;
} 

ShowTotal($stripe, $total);
?>
</table>