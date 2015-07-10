<?php
$db = $this->Model();
$form = $this->Forms();
?>

<div name="ContactPerson" id="ContactPerson" class="memoarea pagesection" <?php echo 'style="display:none"'; ?>>
<h2>Contact</h2>
<form method="post" action="<?php echo $this->Controller(); ?>" <?php $form->ShowOnSubmit(); ?>>
<?php
$form->ShowHiddenField ('MsgUserID', '');
$form->ShowHiddenField ('TaskID', $TaskID);
$form->ShowHiddenField ('MsgUserEmail', $MsgUserEmail);
?>
	<table border="0" cellpadding="3" cellspacing="5" class="tabledata" width="100%">
<?php
$form->ShowTextField ('Recipient', 'MsgUser', 100, 26, $value='', $minlen=0);
$form->ShowTextField ('Subject', 'Subject', 80, 49);
$form->ShowTextAreaField ('Message', 'Message', 5, 37, $value='', $minlen=0);
?>
		<tr>
			<td>&nbsp;</td>
			<td>
<?php
$form->ShowSubmitButton($caption='Send', $name='MsgSend');
$form->ShowJavaScriptButton("ToggleViews('ContactPerson','ViewTask')", 'Cancel');
?>
			</td>
		</tr>
	</table>
</form>
</div>

<script type="text/javascript">
function ToggleViews(hide, show)
{
	h = document.getElementById(hide);
	s = document.getElementById(show);
	if (h && s) {
		h.style.display = 'none';
		s.style.display = 'block';
	}
}

function ShowContactPerson(userid, taskid, user, useremail, subject) {
	document.getElementById('ContactPerson').style.display='block';
	document.getElementById('MsgUser').value = user;
	document.getElementById('MsgUserID').value = userid;	
	document.getElementById('TaskID').value = taskid;	
	document.getElementById('MsgUserEmail').value = useremail;	
	document.getElementById('Subject').value = subject;	
}
</script>