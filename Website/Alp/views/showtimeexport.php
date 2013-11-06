<table class="ListTable">
	<tr bordercolordark="#666666">
		<th align="left">Name</th>
		<th align="left">Project</th>
		<th align="left">Date</th>
		<th align="left">Start</th>
		<th align="left">Time</th>
		<th align="left">Task</th>
		<th align="left">Comment</th>
		<th></th>
	</tr>
<?php
	$stripe = 1;
	$totlapsetime = 0;
	$totadjustment = 0;
	$totadjustedtime = 0;

	foreach ($data as $vals) {
		$elapsetime = $vals->elapsetime;
		$adjustment = $vals->adjustment;
		$adjustedtime = $elapsetime - $adjustment;
		$reason = $vals->reason;
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td><?php echo $vals->name; ?></td>
		<td><?php echo $vals->project; ?></td>
		<td align="left"><?php echo $vals->startdate; ?></td>
		<td align="left"><?php echo $vals->starttime; ?></td>
		<td align="center"><?php echo number_format($adjustedtime,1);?></td>
		<td align="left"><?php echo $vals->task;?></td>
		<td align="left"><?php echo $vals->comment;?></td>
		</td>
	</tr>
<?php
		$stripe = ($stripe > 1) ? 1 : 2;
	}
?>
</table>