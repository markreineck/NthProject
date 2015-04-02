<?php
$this->LoadLibrary('timeicons');
?>
<table class="table table-striped">
    <tr class="ColumnTitles">
		<th align="left">Name</th>
		<th align="left">Project</th>
		<th align="left">Date</th>
		<th align="left">Time</th>
		<th align="center">Elapse Time</th>
		<th align="center">Adjustment</th>
		<th align="center">Adjusted Time</th>
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
		<td align="left">
<?php 
		echo $vals->starttime . ' - ';
		if (!empty($vals->endtime))
			echo $vals->endtime;
?>
		</td>
		<td align="center"><?php echo $elapsetime;?></td>
		<td align="center">
<?php 		
		if ($adjustment<=0) {
			echo '0';
		} else {
			echo $adjustment;
		}
?>
		</td>
		<td align="center"><?php echo $adjustedtime;?></td>
		<td align="left"><?php echo $vals->comment;?></td>
		<td width="122">
<?php
		TimeProjectIcon($vals->timeid, $vals->name, $vals->startdate, $vals->elapsetime);
		if ($elapsetime < 24)
			TimeAdjustIcon($vals->timeid, $vals->name, $vals->startdate, $vals->elapsetime);
		if ($elapsetime >= 16)
			LogOutIcon($vals->timeid, $vals->name, $vals->startdate, $vals->elapsetime);
?>
		</td>
	</tr>
<?php
		$totlapsetime += $elapsetime;
		$totadjustment += $adjustment;
		$totadjustedtime += $adjustedtime;
		$stripe = ($stripe > 1) ? 1 : 2;
	}
?>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align="right">Total Hours:</td>
		<td align="center"><?php echo $totlapsetime;?></td>
		<td align="center"><?php echo $totadjustment;?></td>
		<td align="center"><?php echo $totadjustedtime; ?></td>
	</tr>
</table>

