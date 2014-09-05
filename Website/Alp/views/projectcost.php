<?php
$db = $this->Model();
$c = $this->Cookie();
$form = $this->Forms();
//$this->LoadLibrary('timeicons');

if ($db->IsGlobalSupervisor()) {
?>
<h1>Project Cost</h1>
<div id="AjaxList" name="AjaxList">
<?php
$this->LoadView('widgets/showprojectcost');
?>
</div>
<?php
	$form->ShowFormErrors($errmsg);
	$this->Ajax()->AjaxBase();
}
?>
