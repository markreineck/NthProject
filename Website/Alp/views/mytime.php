<?php
$c = $this->Cookie();

$this->LoadLibrary('timeicons');
$form = $this->Forms();
?>
<div id="TimeList" name="TimeList">
<?php
$this->LoadView('widgets/showmytime');
?>
</div>
<?php
AdjustProjectForm($this, $form);
$this->Ajax()->AjaxBase();
?>
