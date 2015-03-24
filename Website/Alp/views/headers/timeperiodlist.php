<?php
$db = $this->Model();
$form = $this->Forms();
$c = $this->Cookie();
?>
<div class="field-container inline">
Period &nbsp;<br />
<?php
$list = array(
	array('TW', 'This week'),
	array('LW', 'Last week'),
	array('LW2', 'Last week to date'),
	array('2W', 'Last 2 weeks'),
	array('2W2', 'Last 2 weeks to date'),
	array('TM', 'This month'),
	array('LM', 'Last month'),
	array('LM2', 'Last month to date'),
	array('TMA', 'This month 1-15'),
	array('TMB', 'This month 16-EOM'),
	array('LMA', 'Last month 1-15'),
	array('LMB', 'Last month 16-EOM'),
	array('TY', 'This Year'),
	array('LY', 'Last Year'),
	array('LY2', 'Last Year to date')
);

$form->ShowList ('DefaultDateRange', $list, 0, $c->GetDefaultDateRange(), 'ChangeDateRange(this)');
?>
<script language="javascript">

function ChangeDateRange(field) {

mycookie = new Cookie();
mycookie.SetValue("DefaultDateRange", field.value, 1);
<?php
echo $this->Ajax()->DefaultFieldQuery();
?>
}
</script> 
</div>
