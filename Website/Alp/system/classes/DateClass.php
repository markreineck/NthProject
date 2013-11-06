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

class DateClass {

var $DateFormat='';

function DateClass($framework)
{
	$this->DateFormat = $framework->DateFormat();
}

function CurrentDayOfWeek()
{
	return date("w");
}

function CurrentDayOfMonth()
{
	return date("j");
}

function CurrentMonth()
{
	return date("n");
}

function LastMonth()
{
	$m = $this->CurrentMonth() - 1;
	if ($m < 1) $m = 12;
	return $m;
}

function LastMonthYear()
{
	$y = $this->CurrentYear();
	if ($this->CurrentMonth() < 2) $y--;
	return $y;
}

function CurrentYear()
{
	return date("Y");
}

function DaysInTheMonth()
{
	return date("t");
}

function FirstDayOfThisWeek()
{
	return date($this->DateFormat, mktime() - ($this->CurrentDayOfWeek() * 86400));
}

function LastDayOfThisWeek()
{
	return date($this->DateFormat, mktime() + ((6 - $this->CurrentDayOfWeek()) * 86400));
}

function FirstDayOfLastWeek()
{
	return date($this->DateFormat, mktime() - (($this->CurrentDayOfWeek() + 7) * 86400));
}

function LastDayOfLastWeek()
{
	return date($this->DateFormat, mktime() - (($this->CurrentDayOfWeek() + 1) * 86400));
}

function FirstDayOfLast3Weeks()
{
	return date($this->DateFormat, mktime() - (($this->CurrentDayOfWeek() + 14) * 86400));
}

function FirstDayOfThisMonth()
{
	return date($this->DateFormat, mktime() - (($this->CurrentDayOfMonth() - 1) * 86400));
}

function XDayOfThisMonth($x)
{
	return date($this->DateFormat, mktime() + (($x - $this->CurrentDayOfMonth()) * 86400));
}

function LastDayOfThisMonth()
{
	return date($this->DateFormat, mktime() + (($this->DaysInTheMonth() - $this->CurrentDayOfMonth()) * 86400));
}

function FirstDayOfLastMonth()
{
	return $this->XDayOfLastMonth(1);
}

function FirstDayOfThisYear()
{
	return date($this->DateFormat, mktime() - (date('z') * 86400));
}

function LastDayOfThisYear()
{
	$year = $this->CurrentYear();
	return date($this->DateFormat, strtotime($year.'/12/31'));
}

}
?>
