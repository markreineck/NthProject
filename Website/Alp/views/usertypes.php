<?php
$db = $this->Database();
$form = $this->Forms();
$orgtbl = $this->DBTable();

if ($db->IsGlobalSupervisor()) {
?>
<h1>User Status List</h1>
<form method="post">
	<table class="ListTable">
		<tr class="tableclass">
			<th>Name</th>
			<th>Payment Method</th>
			<th>Delete</th>
		</tr>
<?php
$stripe = 1;
$idx = 0;
$data = $orgtbl->SelectAll();
$paylist = array( array('', 'Unpaid'), array('H', 'Hourly'), array('P', 'Per Project'));
foreach ($data as $row) {
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td>
<?php
	$form->ShowTextInput ($ListName, 'Name'.$idx, 40, 40, $row->name)
?>
		</td>
		<td>
<?php
	$form->ShowList ('PayType'.$idx, $paylist, 1, $row->paytype);
?>
		</td>
		<td>
<?php
	$form->ShowCheckBox ('Delete'.$idx, 1, false);
	$form->ShowHiddenField ('ID'.$idx, $row->statusid);
?>
		</td>
	</tr>
<?php
	$stripe = ($stripe > 1) ? 1 : 2;
	++$idx;
}

for ($x=0; $x<5; $x++) {
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td>
<?php
	$form->ShowTextInput ('Organization Status', 'Name'.$idx, 40, 40)
?>
		</td>
		<td>
<?php
	$form->ShowList ('PayType'.$idx, $paylist)
?>
		</td>
		<td></td>
	</tr>
<?php
	$stripe = ($stripe > 1) ? 1 : 2;
	++$idx;
}
?>
	</table><br>
<?php
$form->ShowSubmitButton();
$form->ShowHiddenField ('RowCnt', $idx);
?>
</form>
<?php
}
?>
<br clear="all">