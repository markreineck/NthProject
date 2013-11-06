<?php
$c = $this->Cookie();
$db = $this->Database();
$form = $this->Forms();
?>
<h1>Pay Fixed Price Tasks</h1>
<?php
$form->ShowFormErrors($db->ErrorMsg(), $OKMsg);
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
<div id="TaskList" name="TaskList">
<?php
$data = $db->ListApproveFixedPriceTasks($this->Cookie());
$this->PutData ('data', $data);
$this->LoadView('widgets/showpayabletasks');
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