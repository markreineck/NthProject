<?php
$form = $this->Forms();
$db = $this->Model();
$c = $this->Cookie();
$taskid = $_GET['id'];
?>
<h1>Task History</h1>
<h2>
<?php
$data = $db->ReadTaskDesc($taskid);

echo "$data->project: $data->area: <a href=\"taskinfo?tid=$taskid\">$data->name</a>";
?>
</h2>
<table width="100%" border="0" cellpadding="3" cellspacing="0" class="tabledata">
	<tr class="columntitles">
		<th align="left">Time</th>
		<th align="left">Action By</th>
		<th align="left">Action</th>
	</tr>
<?php
$sql = "select t.noteid, t.taskid, t.fromid, t.msgtypedesc, t.senton, t.message, t.subject, t.fromname, u.name targetname
from taskhistory t
left outer join usernames u on t.targetuser=u.userid
where t.taskid=$taskid order by senton";

$data = $db->SelectAll($sql);

$stripe = 1;
$cnt = 0;

foreach ($data as $row) { 
	$userid = $usersql->userid;
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td><?php echo $row->senton; ?></td>
		<td><?php echo $row->fromname; ?></td>
		<td>
<?php
echo $row->msgtypedesc;
if ($row->targetname)
	echo ' to ' . $row->targetname;
?>
		</td>
	</tr>
	<tr class="stripe<?php echo $stripe; ?>">
<?php
	if (!empty($row->message)) {
		echo "<tr class=\"stripe$stripe\"><td colspan=\"6\"><div id=\"msg$row->noteid\">$row->message&nbsp;&nbsp;&nbsp;";
		echo '</div></td></tr>
';
	}
	$cnt++;
	$stripe = ($stripe > 1) ? 1 : 2;
} 
?>
</table>
