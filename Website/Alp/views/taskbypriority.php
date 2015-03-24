<div id="TaskList" name="TaskList">
<?php
$c = $this->Cookie();
$this->PutData ('filter', $c);
$this->PutData ('TaskSort', $c->GetTaskSort());
$this->LoadView('widgets/showactivetasks');
?>
</div>
<?php
$this->Ajax()->AjaxBase();
?>