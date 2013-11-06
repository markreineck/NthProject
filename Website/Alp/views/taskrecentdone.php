<?php
$db = $this->Database();
$data = $db->ListTasksByCompletedOn($this->Cookie());
$this->PutData ('data', $data);
?>
<h1>Recently Completed</h1>
<div id="TaskList" name="TaskList">
<?php
$this->LoadView('widgets/showrecentdone');
?>
</div>
<?php
$this->Ajax()->AjaxBase();
?>