<?php

class ProjectCookie extends CookieClass 
{
var $framework;

function ProjectCookie($framework)
{
	$this->CookieClass($framework);
	$this->framework = $framework;
}

function SetRangeDates()
{
	if (!$this->Get('DefaultStartDate') || !$this->Get('DefaultEndDate')) {
		$daterange = new DateRange($this->GetDefaultDateRange());
		$this->SetDefaultStartDate($daterange->StartDate());
		$this->SetDefaultEndDate($daterange->EndDate());	
	}	
}

function SetSiteID($val)
{
	$this->Set('SiteID', $val);
}

function GetSiteID()
{
	return $this->Get('SiteID');
}

function SetLastTaskPage($val)
{
	$this->Set('LastTaskPage', $val);
}

function GetLastTaskPage()
{
	return $this->Get('LastTaskPage');
}

function SetDefaultCompany($val)
{
	$this->Set('DefaultCompany', $val);
}

function GetDefaultCompany()
{
	return $this->Get('DefaultCompany');
}

function SetDefaultMilestone($val)
{
	$this->Set('DefaultMilestone', $val);
}

function GetDefaultMilestone()
{
	return $this->Get('DefaultMilestone');
}

function SetDefaultUser($val)
{
	$this->Set('DefaultUser', $val);
}

function GetDefaultUser()
{
	return $this->Get('DefaultUser');
}

function SetDefaultProjectStatus($val)
{
	$this->Set('DefaultPrjStatus', $val);
}

function GetDefaultProjectStatus()
{
	return $this->Get('DefaultPrjStatus', 'A');
}

function SetDefaultPaymentStatus($val)
{
	$this->Set('DefaultPaymentStatus', $val);
}

function GetDefaultPaymentStatus()
{
	return $this->Get('DefaultPaymentStatus', 'U');
}

function SetDefaultTaskStatus($val)
{
	$this->Set('DefaultTaskStatus', $val);
}

function GetDefaultTaskStatus()
{
	return $this->Get('DefaultTaskStatus');
}

function SetDefaultProject($val)
{
	$this->Set('DefaultPrj', $val);
	$this->SetDefaultMilestone(0);
}

function GetDefaultProject()
{
	return ($this->Get('DefaultPrj') > 0) ? $this->Get('DefaultPrj') : -1;
}

function SetDefaultUserType($val)
{
	$this->Set('DefaultUserType', $val);
}

function GetDefaultUserType()
{
	return $this->Get('DefaultUserType');
}

function SetDefaultDateRange($val)
{
	$this->Set('DefaultDateRange', $val);
}

function GetDefaultDateRange()
{
	return $this->Get('DefaultDateRange');
}

function SetDefaultStartDate($val)
{
	$this->Set('DefaultStartDate', $val);
}

function GetDefaultStartDate()
{
	$this->SetRangeDates();
	return $this->Get('DefaultStartDate');
}

function SetDefaultEndDate($val)
{
	$this->Set('DefaultEndDate', $val);
}

function GetDefaultEndDate()
{
	$this->SetRangeDates();
	return $this->Get('DefaultEndDate');
}

function GetSessionID()
{
	return $this->Get('SessionID');
	return $_SESSION['SessionID'];
}

function StartSession($sesid, $orgid, $owner, $superuser, $usermaint, $assnto)
{
	$this->Set('SessionID', $sesid);
	$this->Set('OrgID', $orgid);
	$this->Set('OwnerID', $owner);
	$this->Set('SuperUser', $superuser);
	$this->Set('UserMaint', $usermaint);
	$this->SetDefaultUser($assnto);
}

function EndSession()
{
	unset($_SESSION['SessionID']);
	$this->Clear('SessionID');
	$this->Clear('OrgID');
	$this->Clear('OwnerID');
	$this->Clear('SuperUser');
	$this->Clear('UserMaint');
	$this->Set('DefaultTaskStatus', -2);
}

}
?>