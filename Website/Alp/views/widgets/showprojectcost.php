<?php
$db = $this->Database();

$timedata = $db->ListProjectTimeCosts($ProjectID);
$taskdata = $db->ListProjectTaskCosts($ProjectID);
?>
<table class="ListTable" width="350">
	<tr>
		<th align="left">Name</th>
		<th align="right">Time</th>
		<th align="right">Cost</th>
	</tr>
<?php
	$stripe = 1;
	$totlapsetime = 0;
	$totadjustment = 0;
	$totadjustedtime = 0;

	foreach ($timedata as $vals) {
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td><?php echo $vals->name; ?></td>
		<td align="right"><?php echo $vals->elapsetime;?></td>
		<td align="right"><?php echo number_format($vals->pay,2);?></td>
	</tr>
<?php
		$totaltime += $vals->elapsetime;
		$totalpay += $vals->pay;
		$stripe = ($stripe > 1) ? 1 : 2;
	}
?>
	<tr>
		<td align="left">Total Hours:</td>
		<td align="right"><?php echo $totaltime;?></td>
		<td align="right"><?php echo number_format($totalpay,2);?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr bordercolordark="#666666">
		<th align="left">Name</th>
		<th align="right">Tasks</th>
		<th align="right">Cost</th>
	</tr>
<?php
	$stripe = 1;
	$taskcnt = 0;
	$totaltasks = 0;

	foreach ($taskdata as $vals) {
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td><?php echo $vals->name; ?></td>
		<td align="right"><?php echo $vals->taskcnt;?></td>
		<td align="right"><?php echo number_format($vals->cost,2);?></td>
	</tr>
<?php
		$taskcnt += $vals->taskcnt;
		$totaltasks += $vals->cost;
		$stripe = ($stripe > 1) ? 1 : 2;
	}
?>
	<tr>
		<td align="left">Total Tasks:</td>
		<td align="right"><?php echo $taskcnt;?></td>
		<td align="right"><?php echo number_format($totaltasks,2);?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td align="left" colspan="2">Total Project Cost:</td>
		<td align="right"><?php echo number_format($totaltasks+$totalpay,2);?></td>
	</tr>
</table>

