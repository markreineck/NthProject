<?php
$db = $this->Database();
?>
<h1>By Assignment</h1>
<div id="TaskList" name="TaskList">
<?php
$data = $db->ListTasksByPerson($this->Cookie());
$this->PutData ('data', $data);
$this->PutData ('GroupTitle', 'Assigned To');
$this->PutData ('GroupField', 'assignedto');
$this->LoadView('widgets/showtasklisting');
?>
</div>
<?php
$this->Ajax()->AjaxBase();
?>