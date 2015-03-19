<?php
$form = $this->Forms();
$c = $this->Cookie();
?>
<div class="field-container inline">
Status &nbsp;<br />
<?php
$list = array(
	array('A', 'All'),
	array('U', 'Unpaid'),
	array('P', 'Paid')
);
$form->ShowList ('DefaultPaymentStatus', $list, 0, $c->GetDefaultPaymentStatus(), 'ChangePayStatus(this)');
?>
<script language="javascript">

function ChangePayStatus(field) {

mycookie = new Cookie();
mycookie.SetValue("DefaultPaymentStatus", field.value, 1);
<?php
echo $this->Ajax()->DefaultFieldQuery();
?>
}
</script> 
</div>
