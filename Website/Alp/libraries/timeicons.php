<?php
$this->LoadLibrary('iconlinks');

function TimeProjectIcon($timeid, $person, $date, $hours)
{
	MakeJSIcon('editproject.png', "ShowEditProject($timeid,'$person','$date','$hours')", "Change Project");
}

function TimeAdjustIcon($timeid, $person, $date, $hours)
{
	MakeJSIcon('edittime.png', "ShowEditTime($timeid,'$person','$date','$hours')", "Adjust Time");
}

function LogOutIcon($timeid, $person, $date, $hours, $hint='Adjust Time')
{
	MakeJSIcon('x.png', "ShowLogOut($timeid,'$person','$date','$hours')", $hint);
}

function LogOutForm($nth, $form)
{
?>
<div name="LogOut" id="LogOut" class="memoarea" <?php echo 'style="display:none"'; ?>>
<h2>Log Out</h2>
<form method="post" action="<?php echo $nth->Controller(); ?>" <?php $form->ShowOnSubmit(); ?>>
<input type="hidden" name="LogOutID" id="LogOutID">
	<table border="0" cellpadding="0" cellspacing="5" class="tabledata">
	<tr>
	<td>Person</td>
	<td id="LogOutPerson"></td>
	</tr>
	<tr>
	<td>Date</td>
	<td id="LogOutDate"></td>
	</tr>
	<tr>
	<td>Logged Time</td>
	<td id="LogOutTime"></td>
	</tr>
	<tr>
	<td>Time</td>
	<td>
<?php
$form->ShowNumericList ('LogOut1', 0, 16, 1, '', true);
echo '&nbsp;<strong>.</string>&nbsp;';
$form->ShowNumericList ('LogOut2', 0, 9, 1, '', true);
?>
	</td>
	</tr>
	</table>
<?php
	$form->ShowSubmitButton();
	$form->ShowJavaScriptButton('document.getElementById(\'LogOut\').style.display=\'none\'', 'Cancel');
?>
</form>
<br clear="all">
</div>

<script type="text/javascript">
function ShowLogOut(tid,person,date,hours)
{
	f = document.getElementById('LogOut');
	p = document.getElementById('LogOutPerson');
	d = document.getElementById('TimeDate');
	t = document.getElementById('LogOutTime');
	i = document.getElementById('LogOutID');
	if (f && p && d && t) {
		f.style.display = 'block';
		p.innerHTML = person;
		d.innerHTML = date;
		t.innerHTML = hours;
		i.value = tid;

        edt = document.getElementById('EditTime');
        if (edt)
            edt.style.display = 'none';
        prj = document.getElementById('EditProject');
        if (prj)
            prj.style.display = 'none';
	}
}

</script>
<?php
}

function AdjustTimeForm($nth, $form)
{
?>
<div name="EditTime" id="EditTime" class="memoarea" <?php echo 'style="display:none"'; ?>>
<h2>Adjust Time</h2>
<form method="post" action="<?php echo $nth->Controller(); ?>" <?php $form->ShowOnSubmit(); ?>>
<input type="hidden" name="TimeID" id="TimeID">
	<table border="0" cellpadding="0" cellspacing="5" class="tabledata">
	<tr>
	<td>Person</td>
	<td id="TimePerson"></td>
	</tr>
	<tr>
	<td>Date</td>
	<td id="TimeDate"></td>
	</tr>
	<tr>
	<td>Logged Time</td>
	<td id="TimeTime"></td>
	</tr>
	<tr>
	<td>Time Adjustment</td>
	<td>
<?php
$form->ShowNumericList ('Time1', 0, 50, 1, '', true);
echo '&nbsp;<strong>.</string>&nbsp;';
$form->ShowNumericList ('Time2', 0, 9, 1, '', true);
?>
	</td>
	</tr>
	</table>
<?php
	$form->ShowSubmitButton();
	$form->ShowJavaScriptButton('document.getElementById(\'EditTime\').style.display=\'none\'', 'Cancel');
?>
</form>
<br clear="all">
</div>

<script type="text/javascript">
function ShowEditTime(tid,person,date,hours)
{
	f = document.getElementById('EditTime');
	p = document.getElementById('TimePerson');
	d = document.getElementById('TimeDate');
	t = document.getElementById('TimeTime');
	i = document.getElementById('TimeID');
	if (f && p && d && t) {
		f.style.display = 'block';
		p.innerHTML = person;
		d.innerHTML = date;
		t.innerHTML = hours;
		i.value = tid;

        lo = document.getElementById('LogOut');
        if (lo)
            lo.style.display = 'none';
        prj = document.getElementById('EditProject');
        if (prj)
            prj.style.display = 'none';
	}
}

</script>
<?php
}

function AdjustProjectForm($nth, $form)
{
?>
<div name="EditProject" id="EditProject" class="memoarea" <?php echo 'style="display:none"'; ?>>
<h2>Change Project</h2>
<form method="post" action="<?php echo $nth->Controller(); ?>" <?php $form->ShowOnSubmit(); ?>>
<input type="hidden" name="PrjTimeID" id="PrjTimeID">
	<table border="0" cellpadding="0" cellspacing="5" class="tabledata">
	<tr>
	<td>Person</td>
	<td id="PrjPerson"></td>
	</tr>
	<tr>
	<td>Date</td>
	<td id="PrjDate"></td>
	</tr>
	<tr>
	<td>Logged Time</td>
	<td id="PrjTime"></td>
	</tr>
<?php
	$prjlist = $nth->Model()->ReadProjectList();

	$form->ShowListField ('Project', 'Project', $prjlist, 1);
?>
	</table>
<?php
	$form->ShowSubmitButton();
	$form->ShowJavaScriptButton('document.getElementById(\'EditProject\').style.display=\'none\'', 'Cancel');
?>
</form>
<br clear="all">
</div>

<script type="text/javascript">
function ShowEditProject(tid,person,date,hours)
{
	f = document.getElementById('EditProject');
	p = document.getElementById('PrjPerson');
	d = document.getElementById('PrjDate');
	t = document.getElementById('PrjTime');
	i = document.getElementById('PrjTimeID');
	if (f && p && d && t) {
		f.style.display = 'block';
		p.innerHTML = person;
		d.innerHTML = date;
		t.innerHTML = hours;
		i.value = tid;

        lo = document.getElementById('LogOut');
        if (lo)
            lo.style.display = 'none';
        edt = document.getElementById('EditTime');
        if (edt)
            edt.style.display = 'none';
	}
}
</script>

<?php
}
?>