<?php
class ajaxtime extends AlpFramework implements AlpController {

public function __construct($url)
{
	parent::AlpFramework($url);
}

function Start()
{
	$db = $this->LoadModel(array('DatabaseDB', 'TimeDB', 'TimeRptDB'));

	$sid = @$_GET['sid'];
	if ($sid) {
		$sid = $db->ReadUserSession($sid);
		if ($sid && $db->IsGlobalSupervisor()) {

			$this->LoadLibrary('DateRange');
			$this->LoadLibrary('TaskFilter');
			$daterange = new DateRange($_GET['DefaultDateRange']);
			$filter = new TaskListFilter();

			$prjid = (isset($_GET['DefaultPrj'])) ? $_GET['DefaultPrj'] : 0;
			$user = (isset($_GET['DefaultUser'])) ? $_GET['DefaultUser'] : -1;
			$filter->SetDefaultUser($user);
			$filter->SetDefaultProject($prjid);
			$filter->SetDefaultStartDate($daterange->StartDate());
			$filter->SetDefaultEndDate($daterange->EndDate());

			switch ($this->Controller(1)) {
				case 'Test':
					echo 'AJAX Test';
					break;

				case 'GetTimeDetail':
					$this->PutData ('data', $this->Database()->ReadTimeExport($filter));
					$this->LoadView('widgets/showtimedetail');
					break;

				case 'GetTimeExport':
					$this->PutData ('data', $this->Database()->ReadTimeExport($filter));
					$this->LoadView('widgets/showtimeexport');
					break;

				case 'GetPayroll':
					$this->PutData ('StartDate', $daterange->StartDate());
					$this->PutData ('EndDate', $daterange->EndDate());
					$this->LoadView('widgets/showpayroll');
					break;

				default:
					echo 'AJAX Error';
			}
		}
	} else
		echo 'Insufficient privileges';
}
}
?>