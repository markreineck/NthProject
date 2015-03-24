<?php
class DateRangeTest extends TestCase {

function BeforeAll()
{
	$this->LoadLibrary('DateClass');
	$this->LoadLibrary('DateRange');
}

function BeforeEach() 
{
	$this->start = new DateClass();
	$this->end = new DateClass();
}

private function ShowDates($should, $actual)
{
//	echo "<br>Should be $should - Actual $actual";
}

private function CheckDates($rangestr)
{
	$range = new DateRange($rangestr);
	$this->ShowDates($this->start->DateString(), $range->StartDate());
	$this->Validate($this->start->DateString() == $range->StartDate(), 'Start date is incorrect');
	$this->ShowDates($this->end->DateString(), $range->EndDate());
	$this->Validate($this->end->DateString() == $range->EndDate(), 'End date is incorrect');
}

function ThisWeek()
{
	$this->start->FirstDayOfWeek();
	$this->end->LastDayOfWeek();
	$this->CheckDates('TW');
}

function LastWeek()
{
	$this->start->FirstDayOfWeek();
	$this->end->LastDayOfWeek();
	$this->start->AddDays(-7);
	$this->end->AddDays(-7);
	$this->CheckDates('LW');
}

function LastWeekToDate()
{
	$this->start->FirstDayOfWeek();
	$this->end->LastDayOfWeek();
	$this->start->AddDays(-7);
	$this->CheckDates('LW2');
}

function Last2Weeks()
{
	$this->start->FirstDayOfWeek();
	$this->end->LastDayOfWeek();
	$this->start->AddDays(-14);
	$this->end->AddDays(-7);
	$this->CheckDates('2W');
}

function Last2WeeksToDate()
{
	$this->start->FirstDayOfWeek();
	$this->end->LastDayOfWeek();
	$this->start->AddDays(-14);
	$this->CheckDates('2W2');
}

function ThisMonth()
{
	$this->start->FirstDayOfMonth();
	$this->end->LastDayOfMonth();
	$this->CheckDates('TM');
}

function LastMonth()
{
	$this->start->FirstDayOfMonth();
	$this->end->LastDayOfMonth();
	$this->start->AddMonths(-1);
	$this->end->AddMonths(-1);
	$this->CheckDates('LM');
}

function LastMonthToDate()
{
	$this->start->FirstDayOfMonth();
	$this->end->LastDayOfMonth();
	$this->start->AddMonths(-1);
	$this->CheckDates('LM2');
}

function ThisYear()
{
	$this->start->FirstDayOfYear();
	$this->end->LastDayOfYear();
	$this->CheckDates('TY');
}

function LastYear()
{
	$this->start->FirstDayOfYear();
	$this->end->FirstDayOfYear();
	$this->start->AddMonths(-12);
	$this->end->AddDays(-1);
	$this->CheckDates('LY');
}

function LastYearToDate()
{
	$this->start->FirstDayOfYear();
	$this->end->LastDayOfYear();
	$this->start->AddMonths(-12);
	$this->CheckDates('LY2');
}

}
?>