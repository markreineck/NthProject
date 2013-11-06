<?php
$c = $this->Cookie('ProjectCookie');

if (isset($this->PostData['DefaultUser']))
	$c->SetDefaultUser($this->PostData['DefaultUser']);
if (isset($this->PostData['DefaultPrj']))
	$c->SetDefaultProject($this->PostData['DefaultPrj']);
if (isset($this->PostData['TaskStatus']))
	$c->SetDefaultTaskStatus($this->PostData['TaskStatus']);
if (isset($this->PostData['DefaultPaymentStatus']))
	$c->SetDefaultPaymentStatus($this->PostData['DefaultPaymentStatus']);

if (isset($this->PostData['DefaultDateRange'])) {
//	$this->LoadLibrary('DateClass');
	$this->LoadLibrary('DateRange');
	$daterange = new DateRange($_POST['DefaultDateRange']);
	$c->SetDefaultStartDate($daterange->StartDate());
	$c->SetDefaultEndDate($daterange->EndDate());
/*
	$range = $this->PostData['DefaultDateRange'];
	$todate = (substr($range,2,1) == '2');
	$c->SetDefaultDateRange($range);
	$sd = new DateClass();
	$ed = new DateClass();
	switch (substr($range,0,2)) {
		case '2W':
			$sd->AddWeeks(-1);
		case 'LW':
			$sd->AddWeeks(-1);
			if (!$todate)
				$ed->AddWeeks(-1);
		case 'TW':
			$sd->FirstDayOfWeek();
			$ed->LastDayOfWeek();
			break;

		case 'LM':
			$sd->AddMonths(-1);
			if (!$todate)
				$ed->AddMonths(-1);
		case 'TM':
			$sd->FirstDayOfMonth();
			$ed->LastDayOfMonth();
			break;

		case 'LY':
			$sd->AddMonths(-12);
			if (!$todate)
				$ed->AddMonths(-12);
		case 'TY':
			$sd->FirstDayOfYear();
			$ed->LastDayOfYear();
			break;

	}
	$c->SetDefaultStartDate($sd->DateString());
	$c->SetDefaultEndDate($ed->DateString());
*/
}
?>