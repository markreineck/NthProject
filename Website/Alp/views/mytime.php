<?php
$db = $this->Database();
$c = $this->Cookie();

$this->LoadLibrary('timeicons');
$form = $this->Forms();
?>

<h1>Hours for <b><?php echo $c->GetDefaultStartDate().' - '.$c->GetDefaultEndDate(); ?></b></h1>
<div id="TimeList" name="TimeList">
<?php
$this->LoadView('widgets/showmytime');
?>
</div>
<?php
AdjustProjectForm($this, $form);
$this->Ajax()->AjaxBase();
?>
