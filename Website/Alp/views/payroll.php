<h1>Payroll</h1>
<div id="TimeList" name="TimeList">
<?php
$this->LoadLibrary('DateRange');
$c = $this->Cookie();
$daterange = new DateRange($c->GetDefaultDateRange());

$this->PutData ('StartDate', $daterange->StartDate());
$this->PutData ('EndDate', $daterange->EndDate());

$this->LoadView('widgets/showpayroll');
?>
</div>
<?php
$this->Ajax()->AjaxBase();
?>