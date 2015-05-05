<?php
abstract class TimeReportController extends AlpFramework {

public function TimeReportController($url)
{
	parent::AlpFramework($url);
	$c = $this->Cookie('ProjectCookie');
	$db = $this->LoadModel(array('DatabaseDB', 'TimeDB', 'TimeRptDB'));
	$db->ValidateUserSession($c);

	if (isset($_POST['TimeID'])) {
		$adj = $_POST['Time1'] + (($_POST['Time2'] / 10));
		$err = $db->AdjustTime($_POST['TimeID'], $adj);
	} else if (isset($_POST['LogOutID'])) {
		$err = $db->AdminClockOut($_POST['LogOutID'], $_POST['LogOut1'], $_POST['LogOut2']);
	} else if (isset($_POST['PrjTimeID'])) {
		$err = $db->AdjustTimeProject($_POST['PrjTimeID'], $_POST['TimeProject']);
	}

	$this->LoadLibrary('DateRange');
	$this->LoadLibrary('checkfilters');
/*
	if (isset($_POST['From']))
		$c->SetDefaultStartDate($_POST['From']);
	if (isset($_POST['To']))
		$c->SetDefaultEndDate($_POST['To']);
*/
	$ajax = $this->Ajax();
	$ajax->SetPage('ajaxtime');
	$ajax->SetSection('TimeList');
	$ajax->AddArg('sid', $this->Cookie()->GetSessionID());
	$ajax->SetFields(array('DefaultDateRange','DefaultPrj','DefaultUser','TimeGroup'));
}

}
?>
