<h1>Sample Reportica page</h1>
<div id="ProjectList" name="ProjectList">
<?php
$db = $this->Database();
$c = $this->Cookie();
$form = $this->Forms();

$this->PutData ('Company', $c->GetDefaultCompany());
$this->PutData ('ProjectStatus', $c->GetDefaultProjectStatus());
//$this->LoadView('widgets/showuserstatus');
?>
<form action="http://projects.nth-generation.com/reportico/run.php?execute_mode=EXECUTE&project=NthProject&xmlin=approvedtasks.xml&target_format=HTML" method="post">
<table class="swPrpCritEntryBox">
                    <tbody><tr id="criteria_DateRange" class="swPrpCritLine">
                        <td class="swPrpCritTitle">
                            Date Range
                        </td>
                        <td class="swPrpCritSel">
<input type="hidden" value="2013-10-01" maxlength="0" size="0" name="HIDDEN_DateRange_FROMDATE"><input type="text" value="2013-10-01" maxlength="20" size="20" name="MANUAL_DateRange_FROMDATE" id="swDateField_DateRange_FROMDATE" class="swDateField hasDatepicker">&nbsp;- <input type="hidden" value="2013-10-16" maxlength="0" size="0" name="HIDDEN_DateRange_TODATE"><input type="text" value="2013-10-16" maxlength="20" size="20" name="MANUAL_DateRange_TODATE" id="swDateField_DateRange_TODATE" class="swDateField hasDatepicker">
<input type="submit" value="go" />
                        </td>
                        
                    </tr>
                </tbody></table>
</form>
</div>
<br clear="all">

