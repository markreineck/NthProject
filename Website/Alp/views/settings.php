<?php
$db = $this->Model();
$form = $this->Forms();
$errmsg = $db->ErrorMsg();

$data = $db->ReadSubscription();
?>

<h1>Settings</h1>
<?php
$form->ShowFormErrors($errmsg);
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<table border="0" cellpadding="4" cellspacing="0" class="">
<?php
$form->ShowTextField ('Subscription Name', 'SubscrName', 50, 50, $data->name, 1);
$form->ShowCheckBoxField ('Milestones', 'Milestones', 1, $data->milestones);
$form->ShowCheckBoxField ('Multiple Organizations', 'Organizations', 1, $data->organizations);
$form->ShowCheckBoxField ('Time Tracking', 'Time', 1, $data->timetracking);
$form->ShowCheckBoxField ('Fixed Price', 'FixedPrice', 1, $data->contractors);
?>
		<tr>
			<td>
			</td>
			<td>
<?php
$form->ShowSubmitButton();
?>
		</td>
		</tr>
</table>
</form>
<?php
$form->FormValidation();
?>