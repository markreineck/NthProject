<?php
$form = $this->Forms();
$c = $this->Cookie();
$db = $this->Model();
?>
<h1>Messages</h1>
<?php
$form->ShowFormErrors($db->ErrorMsg());
?>
<form method="post">
<table width="100%" border="0" cellpadding="3" cellspacing="0" class="tabledata">
	<tr class="columntitles">
		<th colspan="2"></th>
		<th align="left">Time</th>
		<th align="left">Notification</th>
		<th align="left">Project</th>
		<th align="left">Task</th>
		<th align="left">From</th>
	</tr>
<?php
$this->LoadLibrary('iconlinks');
$this->LoadLibrary('taskicons');

$sql = 'select t.noteid, t.taskid, t.fromid, t.toid, t.msgtypedesc, t.senton, t.message, 
t.name, p.name project, t.fromname, t.email, t.subject
from tasknotedesc t
inner join projectareas a on t.areaid=a.areaid
inner join projects p on a.prjid=p.prjid
where toid=' . $db->GetUserID();
if ($c->GetDefaultProject() > 0)
	$sql .= ' and a.prjid=' . $c->GetDefaultProject();
$sql .= ' and seenon is null order by senton desc';

$data = $db->SelectAll($sql);

$stripe = 1;
$cnt = 0;

if ($data) {
	foreach ($data as $row) { 
		$userid = $usersql->userid;
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td width="20">
		<input type="checkbox" name="chk<?php echo $cnt; ?>" value="1">
		<input type="hidden" name="msgid<?php echo $cnt; ?>" value="<?php echo $row->noteid; ?>">
		</td>
		<td width="20">
<?php
	if (!empty($row->message)) {
?>
		<img src="/image/openicon.png" id="open<?php echo $row->noteid; ?>" onClick="ShowMsg(this,<?php echo $row->noteid; ?>)">
		<img src="/image/closeicon.png" id="close<?php echo $row->noteid; ?>" onClick="HideMsg(this,<?php echo $row->noteid; ?>)" style="display:none">
<?php
} 
?>
		</td>
		<td><?php echo $row->senton; ?></td>
		<td><?php echo $row->msgtypedesc; ?></td>
		<td><?php echo $row->project; ?></td>
		<td><?php $this->TaskInfoLink($row->taskid, $row->name); ?></td>
		<td><?php echo $row->fromname; ?></td>
	</tr>
	<tr class="stripe<?php echo $stripe; ?>">
<?php
		if (!empty($row->message)) {
			echo "<tr class=\"stripe$stripe\"><td></td><td colspan=\"6\"><div id=\"msg$row->noteid\" style=\"display:none\">$row->message&nbsp;&nbsp;&nbsp;";
			if ($row->fromid > 0) {
				MakeJSIcon('mail.png', "ShowContactPerson($row->fromid,$row->taskid,'$row->fromname','$row->email','$row->subject')", 'Respond');
			}
			echo '</div></td></tr>
';
		}
		$cnt++;
		$stripe = ($stripe > 1) ? 1 : 2;
	} 
}
?>
	<tr>
		<td colspan="3">
<?php
	$form->ShowSubmitButton('Mark as Seen');
?>
		</td>
	</tr>
</table>
<input type="hidden" name="RowCnt" value="<?php echo $cnt; ?>">
</form>
<?php
	$this->LoadView('contactform');
?>

<SCRIPT LANGUAGE="JavaScript">
function ShowMsg(img,id)
{
var x=0;
var s;

	if (document.getElementById) {
		icon = document.getElementById('close'+id);
		msg = document.getElementById('msg'+id);
		if (icon && msg) {
			img.style.display = 'none';
			icon.style.display = 'block';
			msg.style.display = 'block';
		}
	}
}

function HideMsg(img,id)
{
var x=0;
var s;

	if (document.getElementById) {
		icon = document.getElementById('open'+id);
		msg = document.getElementById('msg'+id);
		if (icon && msg) {
			img.style.display = 'none';
			icon.style.display = 'block';
			msg.style.display = 'none';
		}
	}
}
</script>
