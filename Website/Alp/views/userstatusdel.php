<?php
$db = $this->Model();
$c = $this->Cookie();
$form = $this->Forms();
$err = $db->ErrorMsg();

if ($db->IsGlobalSupervisor()) {
	$data = $db->ReadUserType($_GET['id']);
?>
<h1>Delete User Status</h1>
<p>Delete user status <b><?php echo $data->name; ?></b>.</p>
<?php
	$form->ShowFormErrors($err);
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<br>
<?php
	$form->ShowRedirectButton('userstatuses', 'Cancel');
}
?>
