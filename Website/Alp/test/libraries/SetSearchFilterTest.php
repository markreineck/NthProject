<?php
class SetSearchFilterTest extends TestCase {

function BeforeAll()
{
	$this->PostData('DefaultUser', 1);
	$this->PostData('DefaultPrj', 1);
	$this->PostData('TaskStatus', 1);
	$this->PostData('DefaultPaymentStatus', 1);
	$this->PostData('DefaultDateRange', 'TW');

	$this->LoadLibrary('checkfilters');
}

function DefaultUser()
{
	$this->Validate($this->Cookie()->GetDefaultUser() == 1, 'The default user is incorrect');
}

function DefaultProject()
{
	$this->Validate($this->Cookie()->GetDefaultProject() == 1, 'The default project is incorrect');
}

function DefaultTaskStatus()
{
	$this->Validate($this->Cookie()->GetDefaultTaskStatus() == 1, 'The default task status is incorrect');
}

function DefaultPaymentStatus()
{
	$this->Validate($this->Cookie()->GetDefaultPaymentStatus() == 1, 'The default payment status is incorrect');
}

function DefaultDateRange()
{
	$start = new DateClass();
	$end = new DateClass();
	$start->FirstDayOfWeek();
	$end->LastDayOfWeek();

	$this->Validate($this->Cookie()->GetDefaultStartDate() == $start->DateString(), 'The default start date is incorrect');
	$this->Validate($this->Cookie()->GetDefaultEndDate() == $end->DateString(), 'The default end date is incorrect');
}

}
?>