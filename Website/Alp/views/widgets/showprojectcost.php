<?php
$db = $this->Model();

$timedata = $db->ListProjectTimeCosts($ProjectID);
$taskdata = $db->ListProjectTaskCosts($ProjectID);
?>
<table class="table table-striped">
    <tr class="ColumnTitles">
		<th align="left">Name</th>
		<th align="right">Time</th>
		<th align="right">Rate</th>
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
		<td align="right"><?php echo $vals->elapsetime; ?></td>
		<td align="right"><?php echo number_format($vals->payrate,2); ?></td>
		<td align="right"><?php echo number_format($vals->pay,2); ?></td>
	</tr>
<?php
		$totaltime += $vals->elapsetime;
		$totalpay += $vals->pay;
		$stripe = ($stripe > 1) ? 1 : 2;
	}
?>
	<tr class="table-totals">
		<td align="left">Total Hours:</td>
		<td align="right"><?php echo $totaltime;?></td>
		<td></td>
		<td align="right"><?php echo number_format($totalpay,2);?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
    <tr class="ColumnTitles">
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
	<tr class="table-totals">
		<td align="left">Total Tasks:</td>
		<td align="right"><?php echo $taskcnt;?></td>
		<td align="right"><?php echo number_format($totaltasks,2);?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr class="table-totals">
		<td align="left" colspan="2">Total Project Cost:</td>
		<td align="right"><?php echo number_format($totaltasks+$totalpay,2);?></td>
	</tr>
</table>

