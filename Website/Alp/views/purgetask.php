<?php
$db = $this->Database();
$form = $this->Forms();
$errmsg = $db->ErrorMsg();
$ajax = $this->LoadClass('AjaxClass');
?>

<h1>Purge Old Tasks from Project X</h1>
<?php
$form->ShowFormErrors($errmsg);
?>
<form method="post" name="form" id="form" <?php $form->ShowOnSubmit(); ?>>
    <table border="0" cellpadding="3" cellspacing="0">
<?php
$milestonelist = array(
  array('M1', 'Milestone 1'),
  array('M2', 'Milestone 2'),
  array('M3', 'Milestone 3'),
  array('M4', 'Milestone4')
);
$form->ShowListField ('Milestone', 'Milestone', $milestonelist, '');
?>
		<tr>
    		<td valign="top">
        		<label for="OldestTaskCompletionDate" class="LabelClass">Oldest Task Completion Date:</label>
            </td>
            <td>
                <input type="text" value="5/30/2012" maxlength="20" size="20" id="OldestTaskCompletionDate" name="OldestTaskCompletionDate" readonly="readonly" class="TextInputClass ReadOnlyText">
            </td>
        </tr>
        <tr>
    		<td valign="top">
        		<label for="MostRecentTaskCompletionDate" class="LabelClass">Most Recent Task Completion Date:</label>
            </td>
            <td>
                <input type="text" value="5/30/2012" maxlength="20" size="20" id="MostRecentTaskCompletionDate" name="MostRecentTaskCompletionDate" readonly="readonly" class="TextInputClass ReadOnlyText">
            </td>
        </tr>
        <tr>
    		<td valign="top">
        		<label for="OldestTimeRecord" class="LabelClass">Oldest Time Record:</label>
            </td>
            <td>
                <input type="text" value="5/30/2012" maxlength="20" size="20" id="OldestTimeRecord" name="OldestTimeRecord" readonly="readonly" class="TextInputClass ReadOnlyText">
            </td>
        </tr>
        <tr>
    		<td valign="top">
        		<label for="TasksWithoutTimeRecords" class="LabelClass">Tasks Without Time Records:</label>
            </td>
            <td>
                <input type="text" value="000" maxlength="20" size="20" id="TasksWithoutTimeRecords" name="TasksWithoutTimeRecords" readonly="readonly" class="TextInputClass">
                &nbsp;&nbsp;<a href="purgetime?id=1">Purge Time Records</a>
            </td>
        </tr>
        
        <tr>
    		<td valign="top">
        		<label for="PurgeTaskCompletedBefore" class="LabelClass"><input type="radio" name="purgetask" value="purgebefore" id="PurgeTaskCompletedBefore"  /> Purge tasks completed before:</label>
            </td>
            <td>
                <input type="text" value="" maxlength="20" size="20" name="PurgeTaskCompletedBefore" readonly="readonly" class="TextInputClass">                
            </td>
        </tr>
        
        
        <tr>
    		<td valign="top">
        		<label for="PurgeTaskReleasedBefore" class="LabelClass"><input type="radio" name="purgetask" value="purgebefore" id="PurgeTaskReleasedBefore" />  Purge tasks released before:</label>
            </td>
            <td>
                <input type="text" value="" maxlength="20" size="20" name="PurgeTaskReleasedBefore" readonly="readonly" class="TextInputClass">                
            </td>
        </tr>
        
        
        <tr>
    		<td valign="top">
        		<label for="PurgeTaskBefore" class="LabelClass"><input type="radio" name="purgetask" id="PurgeTaskBefore" value="purgebefore" /> Purge All</label>
            </td>
            <td></td>
        </tr>
        

		<tr>
			<td></td>
			<td>
<?php
$form->ShowSubmitButton('Purge');
$form->ShowRedirectButton('','Cancel');
$form->ShowRedirectButton('purgetime','Purge Time Records');
?>
			</td>
		  </tr>
	</table>
</form>
<?php
$form->FormValidation();
$ajax->AjaxBase();
?>