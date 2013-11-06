<?php
$c = $this->Cookie();
$db = $this->Database();
?>
<h1>Billing History</h1>
<div id="TaskList" name="TaskList">
<?php
$data = $db->ListBilledTasks($c);
$this->PutData ('data', $data);
$this->LoadView('widgets/showbillinghistory');
?>
</div>
<br clear="all">
<?php
$this->Ajax()->AjaxBase();
?>