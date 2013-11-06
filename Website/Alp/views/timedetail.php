<?php
$db = $this->Database();
$c = $this->Cookie();
$form = $this->Forms();
//$this->LoadLibrary('timeicons');

if ($db->IsGlobalSupervisor()) {
?>
<h1>Detail Time Listing</h1>
<div id="TimeList" name="TimeList">
<?php
$this->LoadView('widgets/showtimedetail');
?>
</div>
<?php
	$form->ShowFormErrors($errmsg);
	AdjustProjectForm($this, $form);
	AdjustTimeForm($this, $form);
	LogOutForm($this, $form);
	$this->Ajax()->AjaxBase();
}
?>
