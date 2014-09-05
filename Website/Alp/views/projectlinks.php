<?php
$db = $this->Model();
$c = $this->Cookie();
$form = $this->Forms();
$errmsg = $db->ErrorMsg();

function ShowLink ($form, $x, $id='', $descr='', $url='')
{
	$x++;
?>
		<tr>
			<td>
<?php
	$form->ShowTextInput ('', 'LinkName'.$x, 100, 50, $descr);
?>
			</td>
			<td>
<?php
	$form->ShowTextInput ('', 'LinkURL'.$x, 100, 100, $url);
	$form->ShowHiddenField ('LinkID'.$x, $id);
?>
			</td>
		</tr>
<?php
	return $x;
}

if ($db->IsSupervisor()) {
?>
<script type="text/javascript" src="ckeditor/adapters/jquery-1.3.1.js"></script>
<script src="ckeditor/ckeditor.js" type="text/javascript"></script>
<h1>Edit Project Links</h1>
<?php
	$form->ShowFormErrors($errmsg);
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
	<table border="0" cellpadding="3" cellspacing="0" class="">
		<tr>
			<td>Description</td>
			<td>Link</td>
		</tr>
<?php
$data = $db->ReadProjectLinks($PrjID);
$x = 0;

foreach ($data as $row) {
	$x = ShowLink ($form, $x, $row->linkid, $row->linkname, $row->url);
}

for ($y=1; $y<5; $y++) {
	$x = ShowLink ($form, $x);
}
?>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>
<?php
	$form->ShowSubmitButton();
	$form->ShowHiddenField ('ProjectID', $PrjID);
	$form->ShowHiddenField ('LinkCnt', $x);
?>
		</tr>
	</table>
</form>
<?php
}
?>
