<h1>Project Areas</h1>
<?php
$c = $this->Cookie();
$db = $this->Model();
$form = $this->Forms();
$prjid = $c->GetDefaultProject();
?>
<div id="AjaxList" name="AjaxList">
<?php
$this->PutData ('ProjectID', $prjid);
$this->LoadView('widgets/showprojectareas');
?>
</div>
<br>
<?php
if ($db->IsProjectSuperUser($prjid))
	$form->ShowRedirectButton('projectareaadd&pid='.$prjid, 'New Area');
$form->ShowRedirectButton('projects', 'Project List');

$this->Ajax()->AjaxBase();
?>
<br clear="all">
