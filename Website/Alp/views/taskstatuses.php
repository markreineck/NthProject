<h1>Task Status List</h1>
<div id="AjaxList" name="AjaxList">
<?php
$db = $this->Model();
//$c = $this->Cookie();
$form = $this->Forms();

//$this->PutData ('Company', $c->GetDefaultCompany());
//$this->PutData ('ProjectStatus', $c->GetDefaultProjectStatus());
$this->LoadView('widgets/showtaskstatus');
?>
</div>
<br clear="all">
<?php
if ($db->IsGlobalSupervisor()) {
	$form->ShowRedirectButton ('taskstatusedit', 'Add Status');
}
$this->Ajax()->AjaxBase();
?>
<br clear="all">
