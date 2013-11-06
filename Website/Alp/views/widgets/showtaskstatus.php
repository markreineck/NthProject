<?php
$db = $this->Database();

if ($db->IsSupervisor()) {
	$data = $db->ListTaskStatuses();

	if ($data) {
?>
<table class="ListTable" cellpadding="3" cellspacing="0">
    <tr>
      <th>Status Name</th>
      <th>Hold</th>
      <th></th>
    </tr>
<?php
		$stripe = 1;
		foreach ($data as $row) {
			$statusid = $row->statusid;
?>
    <tr class="stripe<?php echo $stripe; ?>">      
      <td align="left" ><?php echo $row->name; ?></td>
      <?php if ($row->hold==1)  {	
					$statcc = "Hold";					
					$color = '#E4201B';
			} else {	
					$statcc = "Active";
					$color = '#00A000';
			}
?>

      <td>
     	 <div style="color:<?php echo $color; ?>"><?php echo $statcc; ?></div>
      </td>
      <td>
      	<a href='taskstatusedit?id=<?php echo $statusid; ?>'><img src="/image/pencil.png" width="32" height="32" alt="Edit" title="Edit"></a>
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

