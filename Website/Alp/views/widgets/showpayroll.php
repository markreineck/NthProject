<?php
$db = $this->Model();

if ($db->IsGlobalSupervisor()) {

	$data = $db->ReadPayroll($StartDate, $EndDate);
	echo "<h2>$StartDate - $EndDate</h2>";
?>
<table class="table table-striped" width="500">
    <tr class="ColumnTitles">
		<th align="left">Name</th>
		<th align="right">Time</th>
		<th align="right">Adjustment</th>
		<th align="right">Rate</th>
		<th align="right">Pay</th>
	</tr>
<?php
	$bg="#F0F1F2";

	foreach ($data as $vals) {
//		$bg = ($bg=='#F0F1F2' ? '#FFFFFF' : '#F0F1F2');
?>
	<tr class="hover">
		<td><?php echo $vals->name; ?></td>
		<td align="center"><?php echo number_format($vals->time, 1); ?></td>
		<td align="center"><?php echo number_format($vals->adjustedtime, 1); ?></td>
		<td align="center"><?php echo number_format($vals->payrate, 2); ?></td>
		<td align="center"><?php echo number_format($vals->pay, 2); ?></td>
	</tr>
<?php
	}
?>
</table>
<?php
}
?>