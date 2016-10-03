<?php 
/*
Copyright (c) 2012, 2013, Nth Generation. All rights reserved.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

/*
The cookie class provides functions to set, get and clear cookie fields.
It is anticipated that child classes will be derived from this class to implement 
a cookie with the appropriate fields. This fake class is used by ajax controllers
to fake out classed that are expecting certain fields to be held in the cookie.
*/

class TaskListFilter {

var $DefaultMilestone, $DefaultUser, $DefaultPaymentStatus, $DefaultTaskStatus, $DefaultPrj, $DefaultArea;
var $DefaultDateRange, $DefaultStartDate, $DefaultEndDate, $Submitter;

function TaskListFilter ()
{
	$this->DefaultPaymentStatus = 'U';
	$this->DefaultPrj = -1;
	$this->DefaultArea = 0;
}

function SetDefaultMilestone($val)
{
	$this->DefaultMilestone = $val;
}

function GetDefaultMilestone()
{
	return $this->DefaultMilestone;
}

function SetDefaultUser($val)
{
	$this->DefaultUser = $val;
}

function GetDefaultUser()
{
	return $this->DefaultUser;
}

function SetSubmitter($val)
{
	$this->Submitter = $val;
}

function GetSubmitter()
{
	return $this->Submitter;
}

function SetDefaultPaymentStatus($val)
{
	$this->DefaultPaymentStatus = $val;
}

function GetDefaultPaymentStatus()
{
	return $this->DefaultPaymentStatus;
}

function SetDefaultTaskStatus($val)
{
	$this->DefaultTaskStatus = $val;
}

function GetDefaultTaskStatus()
{
	return $this->DefaultTaskStatus;
}

function SetDefaultProject($val)
{
	$this->DefaultPrj = $val;
}

function SetDefaultArea($val)
{
	$this->DefaultArea = $val;
}

function GetDefaultProject()
{
	return $this->DefaultPrj;
}

function GetDefaultArea()
{
	return $this->DefaultArea;
}

function SetDefaultStartDate($val)
{
	$this->DefaultStartDate = $val;
}

function GetDefaultStartDate()
{
	return $this->DefaultStartDate;
}

function SetDefaultEndDate($val)
{
	$this->DefaultEndDate = $val;
}

function GetDefaultEndDate()
{
	return $this->DefaultEndDate;
}

function SetDefaultDateRange($val)
{
	$this->DefaultDateRange = $val;
}

function GetDefaultDateRange()
{
	return $this->DefaultDateRange;
}

function ExtractCookie($cookie)
{
	$this->DefaultMilestone = $cookie->GetDefaultMilestone();
	$this->DefaultUser = $cookie->GetDefaultUser();
	$this->DefaultPaymentStatus = $cookie->GetDefaultPaymentStatus();
	$this->DefaultTaskStatus = $cookie->GetDefaultTaskStatus();
	$this->DefaultPrj = $cookie->GetDefaultProject();
	$this->DefaultDateRange = $cookie->GetDefaultDateRange();
}

}
?>
