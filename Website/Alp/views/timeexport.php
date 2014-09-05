<?php
$db = $this->Model();
$c = $this->Cookie();
$form = $this->Forms();

if ($db->IsGlobalSupervisor()) {
?>

<h1>Time Export</h1>
<div id="TimeList" name="TimeList">
<?php
$this->LoadView('widgets/showtimeexport');
?>
</div>
<?php
}
$this->Ajax()->AjaxBase();
?>