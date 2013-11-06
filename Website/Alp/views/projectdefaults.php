<?php
$c = $this->Cookie();
$db = $this->Database();
$form = $this->Forms();
?>
<h1>Project Defaults</h1>
<?php
$form->ShowFormErrors($db->ErrorMsg());
?>
<form method="post" name="form" id="form">
<div id="AjaxList" name="AjaxList">
<?php
$this->PutData ('orgid', $c->GetDefaultCompany());
$this->LoadView('widgets/showprojectdefaults');
?>
</div>
</form>
<?php
$this->Ajax()->AjaxBase();
?>