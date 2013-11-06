<?php
$db = $this->Database();
$c = $this->Cookie();
$form = $this->Forms();
$this->LoadLibrary('iconlinks');

if ($db->IsGlobalUserManager()) {
	$orgid = $c->GetDefaultCompany();
} else {
	$orgid = $db->IsUserManager();
	if ($orgid < 1)
		$this->RedirectTo('/');
}

?>
<h1>Project Team Members</h1>
<div id="AjaxList" name="AjaxList">
<?php
$this->PutData ('Company', $orgid);
$this->PutData ('UserStatus', $c->GetDefaultUserType());
$this->LoadView('widgets/showuserlist');
?>
</div>
<br>
<?php
if ($db->IsGlobalUserManager())
	$form->ShowRedirectButton ('useradd', 'Add a User');
$this->Ajax()->AjaxBase();
?>
<br clear="all">
