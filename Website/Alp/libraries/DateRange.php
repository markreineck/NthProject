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
	$suffix = substr($this->range,2,1);
	$todate = ($suffix == '2');
	$prefix = substr($this->range,0,2);

	switch ($prefix) {
		case '2W':
		case 'LW':
			if ($todate)
				return NULL;
			$ed->FirstDayOfWeek(-1);
			break;
		case 'TW':
			return NULL;
		case 'LM':
			if ($todate) {
				return NULL;
			} else {
				$ed->AddMonths(-1);
				if ($suffix == 'A') {
					$ed->FirstDayOfMonth();
					$ed->AddDays(14);
				} else
					$ed->LastDayOfMonth();
			}
			break;
		case 'TM':
			if ($suffix == 'A') {
				$ed->FirstDayOfMonth();
				$ed->AddDays(14);
			} else
				return NULL;
			break;

		case 'LY':
			if (!$todate) {
				$ed->AddMonths(-12);
				$ed->LastDayOfYear();
			} else
				return NULL;
			break;
		case 'TY':
			return NULL;

	}
	return $ed->DateString();
}

}
?>