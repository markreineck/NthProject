<h1>User Status List</h1>
<div id="ProjectList" name="ProjectList">
<?php
$db = $this->Database();
$c = $this->Cookie();
$form = $this->Forms();

$this->PutData ('Company', $c->GetDefaultCompany());
$this->PutData ('ProjectStatus', $c->GetDefaultProjectStatus());
$this->LoadView('widgets/showuserstatus');
?>
</div>
<br clear="all">
<?php
if ($db->IsGlobalSupervisor()) {
	$form->ShowRedirectButton ('userstatusedit', 'Add Status');
}
$this->Ajax()->AjaxBase();
?>
<br clear="all">
