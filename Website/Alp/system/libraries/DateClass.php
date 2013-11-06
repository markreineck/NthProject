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
The date class performs date arithmetic. Functions are provided to add and subtract days, weeks, months and years as well as perform conversions such as first day or last of the week, month or year.

For day of week logic 1=Sunday and 7=Saturday.
*/

class DateClass {

var $DateFormat='Y-m-d';
var $mydate;

/********************************************************************************
Constructor

The constructor will accept a string date in most formats. If no initial value is given then the class will initialize to the current date
*********************************************************************************/
function DateClass($date='')
{
	if ($date) {
		$datedash = str_replace("'","",explode("-",$date));
		if (count($datedash)>0) {
			$date = str_replace("-","/",$date);
		} else {
			$date = str_replace("'","",$date);
		}
		if ($date && strlen($date) > 4) {
			$date = str_replace("'","",$date);
			$this->mydate = strtotime($date);
		} else
			$this->mydate = 0;
	} else {
		$this->mydate = time();
		// Need to adjust to noon time for daylight savings time fudge
	}
	$this->mydate += 43200;
}

/********************************************************************************
Date Query Functions

The date can be accessed as either a UNIX timestamp or as a string. 
You can also query descrete parts of the date like month, day, year, or day of week.
*********************************************************************************/
function UnixTime()
{
	return $this->mydate;
}

// Format string should be constructed in the same manner as as for the php date() function
function DateString($fmt='')
{
	if ($fmt)
		return date($fmt, $this->mydate);
	else
		return date($this->DateFormat, $this->mydate);
}

function DayOfWeek()
{
	return date("w", $this->mydate);
}

function DayOfMonth()
{
	return date("j", $this->mydate);
}

function DayOfYear()
{
	return date("z", $this->mydate);
}

function DayName()
{
	return date("l", $this->mydate);
}

function Month()
{
	return date("n", $this->mydate);
}

function MonthName()
{
	return date("F", $this->mydate);
}

function Year()
{
	return date("Y", $this->mydate);
}

function DaysInTheMonth()
{
	return date("t", $this->mydate);
}

function WeekOfMonth()
{
	return round(($this->DayOfMonth() + 3) / 7);
}

function WeekOfYear()
{
	return round(($this->DayOfYear() + 3) / 7);
}

/********************************************************************************
Date Arithmetic Functions

Subtraction can be accomplished by passing a negative value.
*********************************************************************************/
function AddDays($days)
{
	if ($days > 0)
		$this->mydate = strtotime("+$days day", $this->mydate);
	else if ($days < 0)
		$this->mydate = strtotime("$days day", $this->mydate);
}

function AddWeeks($weeks)
{
	if ($weeks)
		$this->AddDays($weeks * 7);
}

function AddMonths($mon)
{
	if ($mon > 0)
		$this->mydate = strtotime("+$mon month", $this->mydate);
	else if ($mon < 0)
		$this->mydate = strtotime("$mon month", $this->mydate);
}


/********************************************************************************
Date Manipulation Functions
*********************************************************************************/

//Set the date to the prior Sunday. The the date is already set to a Sunday no change is made.
function FirstDayOfWeek()
{
	$this->AddDays(-$this->DayOfWeek());
}

//Set the date to the next Saturday. The the date already is set to a Saturday no change is made.
function LastDayOfWeek()
{
	$this->AddDays(6 - $this->DayOfWeek());
}

//Set the date to the first day of the next month.
function FirstDayOfNextMonth($inc)
{
	$m = $this->Month() + $inc;
	$y = $this->Year();
	while ($m > 12) {
		$y++;
		$m -= 12;
	}
	$this->mydate = mktime(0,0,0,$m,1,$y);
}

//Set the date to the 1st day of the current month. The the date already the 1st then no change is made.
function FirstDayOfMonth()
{
	$this->XDayOfMonth(1);
}

//Set the date to an indicated day in the current month. If the date is currently set to Jan 1, 2000 then passing 10 would change the date to Jan 10, 2000. Passing 32 would change the date to Feb 1, 2000.
function XDayOfMonth($x)
{
	$this->AddDays($x-$this->DayOfMonth());
	return date($this->DateFormat, mktime() + (($x - $this->DayOfMonth()) * 86400));
}

//Set the date to the last day of the current month. The the date already the last then no change is made.
function LastDayOfMonth()
{
	$this->XDayOfMonth($this->DaysInTheMonth());
}

// This function is used to set the date to the first Sunday, Monday... Saturday of the month.
// Indicate the day of week that you desire where 1=Sunday and 7=Saturday.
function FirstWeekDayOfMonth($dow)
{
	$this->XDayOfMonth(1);
	$this->NextDayOfWeek($dow);
}

// This function is used to set the date to the next Sunday, Monday... Saturday.
// Indicate the day of week that you desire where 1=Sunday and 7=Saturday.
// If the current date is on the day of week that is requested then the value should be incremented by 1 week.
function NextDayOfWeek($dow)
{
	$d = $this->DayOfWeek();
	$dayinc = $dow - $d;
	if ($dayinc < 0)
		$dayinc += 7;
	$this->AddDays($dayinc);
}

//Set the date to the 1st day of the current year. The the date already the Jan 1st then no change is made.
function FirstDayOfYear()
{
	$this->AddDays(-$this->DayOfYear());
}

//Set the date to the last day of the current year. The the date already the Dec 12th then no change is made.
function LastDayOfYear()
{
	$this->mydate = mktime(0,0,0,12,31,$this->Year());
}

}
?>
