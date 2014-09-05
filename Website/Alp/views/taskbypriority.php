<?php
$db = $this->Model();
?>
<h1>By Priority</h1>
<div id="TaskList" name="TaskList">
<?php
$data = $db->ListTasksByPriority($this->Cookie());
$this->PutData ('data', $data);
$this->PutData ('GroupTitle', 'Priority');
$this->PutData ('GroupField', 'priority');
$this->LoadView('widgets/showtasklisting');
?>
</div>
<?php
$this->Ajax()->AjaxBase();
?>