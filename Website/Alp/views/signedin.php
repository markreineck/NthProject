<?php
$db = $this->Database();
$c = $this->Cookie();
$form = $this->Forms();
$this->LoadLibrary('timeicons');
$this->LoadLibrary('taskicons');
?>
<h1>Signed In Users</h1>
<table class="ListTable">
	<tr> </tr>
	<tr>
		<th>Employee Name </th>
		<th>Signed In Since </th>
		<th>Duration </th>
		<th>Project </th>
		<th>Task</th>
		<th>&nbsp; </th>
	</tr>
<?php
$data = $db->ReadSignedInUsers();

$count = 0;
$bg = "#F0F1F2";
$super = $db->IsGlobalSupervisor();

foreach ($data as $dx) {
	$bg = ($bg=='#F0F1F2' ? '#FFFFFF' : '#F0F1F2');		
	$count ++;

	if (@$evenFlag==1) {
		$evenFlag=0;
	} else {
		$evenFlag=1;
	}
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td><?php echo $dx->username; ?></td>
		<td><?php echo $dx->starttime;?></td>
		<td>
<?php
	if ($dx->hours>1.5) {
        $duration = $dx->hours." hours";
	} else if ($dx->minutes>1) {
        $duration = $dx->minutes." minutes";
    } else {
        $duration = $dx->minutes." minute";
    }
    echo $duration;
?>
			</td>
		<td><?php echo $dx->projectname; ?></td>
		<td><?php TaskInfoLink($dx->taskid, $dx->task); ?></td>
<?php
	if ($super) {
?>
		<td>
<?php
		$etime = $dx->hours + ($dx->minutes/10);
		TimeProjectIcon($dx->timeid, $dx->username, $dx->starttime, $duration);
		TimeAdjustIcon($dx->timeid, $dx->username, $dx->starttime, $duration);
		LogOutIcon($dx->timeid, $dx->username, $dx->starttime, $duration, 'Log Out');
?>
		</td>
<?php
		$stripe = ($stripe > 1) ? 1 : 2;
	}
?>
	</tr>
<?php
}
?>
</table>
<br clear="all">
<br>
<?php
if ($super) {
	$form->ShowFormErrors($errmsg);
	AdjustProjectForm($this, $form);
	AdjustTimeForm($this, $form);
	LogOutForm($this, $form);
}
?>
