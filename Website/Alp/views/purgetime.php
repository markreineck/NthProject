<?php
$db = $this->Model();
$form = $this->Forms();
$errmsg = $db->ErrorMsg();
$time = '';
$cnt = 0;
?>

<h1>Purge Old Time Records</h1>
<?php
$form->ShowFormErrors($errmsg, $OKMsg);
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
    <table border="0" cellpadding="3" cellspacing="0">
    	<tr>
    		<td valign="top">
        		<label for="OldestTimeRecord" class="LabelClass">Oldest Time Record:</label>
            </td>
            <td>
                <input type="text" value="<?php echo $db->ReadOldestTime(); ?>" maxlength="24" size="24" id="OldestTimeRecord" name="OldestTimeRecord" readonly="readonly" class="TextInputClass ReadOnlyText">
            </td>
        </tr>
<?php
$form->ShowTextField ('Delete time records older than', 'DeleteTimeRecords', 20, 20, $time);
?>
<?php
if (isset($_POST['DeleteTimeRecords']) && !$OKMsg) {
	$time = $_POST['DeleteTimeRecords'];
	$cnt = $db->ReadOldTime($time);
?>
		<tr><td colspan="2" align="center">
			<p>There are <?php echo $cnt; ?> time records prior to <?php echo $time; ?>.</p>
		</td></tr>
<?php
}
?>
		<tr>
			<td></td>
			<td>
<?php
if ($cnt > 0)
	$form->ShowSubmitButton('Confirm Purge', 'Confirm');
else
	$form->ShowSubmitButton('Purge');
?>
			</td>
		  </tr>
	</table>
</form>
<?php
$form->FormValidation();
?>