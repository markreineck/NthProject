<?php
$db = $this->Model();
?>
<h1>Fixed Price Tasks</h1>
<div id="TaskList" name="TaskList">
<?php
$data = $db->ListTasksWithCost($this->Cookie());
$this->PutData ('data', $data);
$this->LoadView('showpaidtasks');
?>
</div>
<?php
$this->Ajax()->AjaxBase();
?>