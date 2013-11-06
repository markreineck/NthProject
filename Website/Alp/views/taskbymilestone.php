<?php
$db = $this->Database();
?>
<h1>By Milestone</h1>
<div id="TaskList" name="TaskList">
<?php
$data = $db->ListTasksByMilestone($this->Cookie());
$this->PutData ('data', $data);
$this->PutData ('GroupTitle', 'Status');
$this->PutData ('GroupField', 'status');
$this->LoadView('widgets/showtasklisting');
?>
</div>
<?php
$this->Ajax()->AjaxBase();
?>