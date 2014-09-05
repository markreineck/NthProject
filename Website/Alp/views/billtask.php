<?php
$c = $this->Cookie();
$db = $this->Model();
$form = $this->Forms();
?>
<h1>Bill Fixed Price Tasks</h1>
<?php
$form->ShowFormErrors($db->ErrorMsg(), $OKMsg);
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<div id="TaskList" name="TaskList">
<?php
$data = $db->ListApproveBilledTasks($c);
$this->PutData ('data', $data);
$this->LoadView('widgets/showbillabletasks');
?>
</div>
<br>
<?php
$form->ShowSubmitButton();
?>
</form>
<br clear="all">
<?php
$this->Ajax()->AjaxBase();
?>