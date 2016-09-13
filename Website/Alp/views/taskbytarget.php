Depricated 2/16/2015
<?php
$db = $this->Model();
?>
<h1>By Target Date</h1>
<div id="TaskList" name="TaskList">
<?php
$data = $db->ListTasksByTargetDate($this->Cookie());
$this->PutData ('data', $data);
$this->PutData ('GroupTitle', 'Target Date');
$this->PutData ('GroupField', 'needby');
$this->LoadView('widgets/showtasklisting');
?>
</div>
<?php
$this->Ajax()->AjaxBase();
?>