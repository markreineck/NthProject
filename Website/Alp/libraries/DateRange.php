<?php
$this->LoadLibrary('DateClass');

class DateRange {

var $range;

function DateRange($range)
{
	$this->range = $range;
}

function StartDate()
{
	$sd = new DateClass();
	switch (substr($this->range,0,2)) {
		case '2W':
			$sd->AddWeeks(-1);
		case 'LW':
			$sd->AddWeeks(-1);
		case 'TW':
			$sd->FirstDayOfWeek();
			break;

		case 'LM':
			$sd->AddMonths(-1);
		case 'TM':
			$sd->FirstDayOfMonth();
			break;

		case 'LY':
			$sd->AddMonths(-12);
		case 'TY':
			$sd->FirstDayOfYear();
			break;

	}
	if (substr($this->range,2) == 'B')
		$sd->AddDays(15);
	return $sd->DateString();
}

function EndDate()
{
	$ed = new DateClass();
	$todate = (substr($this->range,2,1) == '2');
	$half = (substr($this->range,2,1) == 'A');
	switch (substr($this->range,0,2)) {
		case '2W':
		case 'LW':
			if (!$todate)
				$ed->AddWeeks(-1);
		case 'TW':
			$ed->LastDayOfWeek();
			break;

		case 'LM':
			if (!$todate)
				$ed->AddMonths(-1);
		case 'TM':
			if ($half) {
				$ed->FirstDayOfMonth();
				$ed->AddDays(14);
			} else
				$ed->LastDayOfMonth();
			break;

		case 'LY':
			if (!$todate)
				$ed->AddMonths(-12);
		case 'TY':
			$ed->LastDayOfYear();
			break;

	}
	return $ed->DateString();
}

}
?>