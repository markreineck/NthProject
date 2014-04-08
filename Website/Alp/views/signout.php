<?php
$db = $this->Database();
$form = $this->Forms();
$errmsg = $db->ErrorMsg();

function ShowField($label, $data)
{
	echo "<tr><td class=\"LabelClass\">$label:</td><td>$data</td></tr>
";
}
?>
<h1>log out</h1>
<?php
$form->ShowFormErrors($errmsg);

$sql = 'select t.starton, TIME_TO_SEC(TIMEDIFF(now(),t.starton)) secs, p.name project, x.name task
from usersession s
inner join usertime t on t.userid=s.userid
left outer join projects p on t.prjid=p.prjid
left outer join tasks x on t.taskid=x.taskid
where t.endon is null and s.sessionid=' . $db->GetSessionID();
$data = $db->SelectRow($sql);

if (!$data || !$data->starton) {
	echo '<p>You are not logged in</p>';
} else {
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
	<table>
<?php
	$time = round($data->secs/60);
	if ($time >= 120)
		$time = $data->secs/60 . ' hours';
	else
		$time .= ' minutes';
	ShowField('Signed in since', $data->starton);
	ShowField('Signed in time', $time);
	if ($data->project)
		ShowField('Signed in to', $data->project);
	if ($data->task)
		ShowField('Signed in to', $data->task);
?>
<tr><td>&nbsp;</td></tr>
<?php
	$form->ShowPasswordTextField ('Password', 'Password', '', 1);
	$form->ShowTextAreaField ('Comment', 'Comment', 4, 60);
?>
		<tr><td></td><td>
<?php
	$form->ShowSubmitButton('Sign Out');
?>
		</td></tr>
	</table>
</form>
<?php
	$form->FormValidation();
}
?>