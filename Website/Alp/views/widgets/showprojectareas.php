<?php
$this->LoadLibrary('iconlinks');
$this->LoadLibrary('taskicons');
$db = $this->Model();

if ($ProjectID > 0) {
	$canedit = $db->IsProjectSuperUser($ProjectID);
?>
<h2>
<?php
	echo $db->ReadProjectName($ProjectID);
	$data = $db->ReadProjectAreas($ProjectID);
?>
</h2>
<table class="ListTable">
	<tr>
		<th>Area Name</th>
		<th>Status</th>
		<th>Responsible</th>
		<th>Completion Date</th>
		<th colspan="2"></th>
	</tr>
<?php
	$stripe = 1;
	foreach ($data as $row) {
		if (empty($row->completed)) {
			$date = $row->due;
			$style = '';
			$status = 'Not Complete';
		} else {
			$date = $row->completed;
			$style = 'style="color:#E4201B"';
			$status = 'Complete';
		}
?>
	<tr class="stripe<?php echo $stripe; ?>">
	<td><?php echo $row->name; ?></td>
	<td <?php echo $style; ?>><?php echo $status; ?></td>
	<td><?php echo $row->responsible; ?></td>
	<td><?php echo $date; ?></td>
	<td>
<?php
		if ($canedit)
			MakeIconLink('pencil.png', "projectareaedit?aid=$row->areaid&pid=$ProjectID", 'Edit');
?>
	</td>
	<td>
<?php
		if ($canedit)
			AddTaskToAreaLink($ProjectID, $row->areaid, $row->name);
?>
	</td>
	</tr>
<?php
		$stripe = ($stripe > 1) ? 1 : 2;
	}
?>
</table>
<?php
} else {
	echo '<p>Please select a project.</p>';
}
?>
