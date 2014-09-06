<h1>Active Tasks</h1>
<div id="TaskList" name="TaskList">
<?php
$this->PutData ('filter', $this->Cookie());
$this->PutData ('TaskSort', $this->Cookie()->GetTaskSort());
$this->LoadView('widgets/showactivetasks');
?>
</div>
<?php
$this->Ajax()->AjaxBase();
?>