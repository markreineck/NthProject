<h1>Project Milestones</h1>
<div id="AjaxList" name="AjaxList">
<?php
$c = $this->Cookie();
$this->PutData ('prjid', $c->GetDefaultProject());
$this->LoadView('widgets/showmilestones');
?>
</div>
<?php
$this->Ajax()->AjaxBase();
?>