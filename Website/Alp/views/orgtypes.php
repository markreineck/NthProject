<?php
$db = $this->Database();
$form = $this->Forms();
$orgtbl = $this->DBTable();

if ($db->IsGlobalSupervisor()) {
?>
<h1>Organization Status List</h1>
<form method="post">
	<table class="ListTable">
		<tr class="tableclass">
			<th>Name</th>
			<th>Delete</th>
		</tr>
<?php
$stripe = 1;
$idx = 0;
$data = $orgtbl->SelectAll();

foreach ($data as $row) {
	$id = $row->statusid;
	$name = $row->name;
?>
	<tr class="stripe<?php echo $stripe; ?>">
		<td>
<?php
	$form->ShowTextInput ($ListName, 'Name'.$idx, 40, 40, $name)
?>
		</td>
		<td>
<?php
	$form->ShowCheckBox ('Delete'.$idx, 1, false);
	$form->ShowHiddenField ('ID'.$idx, $id);
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