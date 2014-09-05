<?php
$db = $this->Model();
$db->DebugMode(0);
$cookie = $this->Cookie();

$this->LoadLibrary('DateRange');

$data = $db->ListTasksByReleasedOn($this->Cookie());
$this->PutData ('data', $data);
?>
<h1>Recently Released</h1>
<div id="TaskList" name="TaskList">
<?php
$this->LoadView('widgets/showrecentreleased');
?>
</div>
<?php
$this->Ajax()->AjaxBase();
?>