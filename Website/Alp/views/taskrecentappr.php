<?php
$db = $this->Model();
$data = $db->ListTasksByApprovedOn($this->Cookie());
$this->PutData ('data', $data);
?>
<h1>Recently Approved</h1>
<div id="TaskList" name="TaskList">
<?php
$this->LoadView('widgets/showrecentapproved');
?>
</div>
<?php
$this->Ajax()->AjaxBase();
?>