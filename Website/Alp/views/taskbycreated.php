<?php
// Depricated 2/16/2015
$db = $this->Model();
?>
<h1>By Priority</h1>
<div id="TaskList" name="TaskList">
<?php
$data = $db->ListTasksByCreatedOn($this->Cookie());
$this->PutData ('data', $data);
$this->PutData ('GroupTitle', 'Submitted On');
$this->PutData ('GroupField', 'submitted');
$this->LoadView('widgets/showtasklisting');
?>
</div>
<?php
$this->Ajax()->AjaxBase();
?>