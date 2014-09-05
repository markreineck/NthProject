<?php
$db = $this->Model();

if ($db->IsGlobalSupervisor()) {

	$data = $db->ReadPayroll($StartDate, $EndDate);
	echo "<h2>$StartDate - $EndDate</h2>";
?>
<table class="reporttable" cellspacing="0" cellpadding="5">
	<tr bordercolordark="#666666">
		<th align="left">Name</th>
		<th align="left">Time</th>
	</tr>
<?php
	$bg="#F0F1F2";

	foreach ($data as $vals) {
		$bg = ($bg=='#F0F1F2' ? '#FFFFFF' : '#F0F1F2');
?>
	<tr bgcolor="<?php echo $bg;?>" class="hover">
		<td><?php echo $vals->name; ?></td>
		<td align="right"><?php echo $vals->time; ?></td>
	</tr>
<?php
	}
?>
</table>
<?php
}
?>