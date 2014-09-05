<?php
$db = $this->Model();

if ($db->IsSupervisor()) {
	$data = $db->ListUserStatuses();

	if ($data) {
?>
<table class="ListTable" cellpadding="3" cellspacing="0">
    <tr>
      <th>Status Name</th>
      <th>
<?php
		if ($this->UserSetting('Time') || $this->UserSetting('TaskCost'))
			echo 'Payment';
?>
	  </th>
      <th></th>
    </tr>
<?php
		$stripe = 1;
		foreach ($data as $row) {
			$statusid = $row->statusid;
?>
    <tr class="stripe<?php echo $stripe; ?>">      
      <td align="left" ><?php echo $row->name; ?></td>
<?php 
			if ($this->UserSetting('Time') || $this->UserSetting('TaskCost')) {
				switch ($row->paytype) {
					case 'H':
						$statcc = 'Hourly';
						break;
					case 'P':
						$statcc = 'Per Task';
						break;
					default:
						$statcc = '';
				}
			} else {
				$statcc = '';
			}
?>

      <td><?php echo $statcc; ?></td>
      <td>
      	<a href='userstatusedit?id=<?php echo $statusid; ?>'><img src="/image/pencil.png" width="32" height="32" alt="Edit" title="Edit"></a>
      </td>
      </tr>
<?php
	$stripe = ($stripe > 1) ? 1 : 2;
		}
?>
  </table>
<?php
	}
} else {
	echo 'You do not have rights to view this page.';
}
?>

