<?php
include 'TaskBaseController.php';

class ajaxtasklist extends TaskBaseController implements AlpController {

function Start()
{
	$db = $this->LoadModel(array('DatabaseDB', 'TaskDB', 'TaskListDB'));

	$sid = @$_GET['sid'];
	if ($sid) {
		$sid = $db->ReadUserSession($sid);
		if (!$sid) {
			echo 'Your session has expired';
		} else {
			$this->LoadLibrary('iconlinks');
			$this->LoadLibrary('taskicons');
			$this->LoadLibrary('DateRange');
			$this->LoadLibrary('TaskFilter');
			$filter = new TaskListFilter();

			if (isset($_GET['DefaultDateRange'])) {
				$filter->DefaultDateRange = $_GET['DefaultDateRange'];
			}

			$filter->DefaultPrj = $_GET['DefaultPrj'];
			$filter->DefaultMilestone = (isset($_GET['DefaultMilestone'])) ? $_GET['DefaultMilestone'] : -1;
			$filter->DefaultUser = (isset($_GET['DefaultUser'])) ? $_GET['DefaultUser'] : -1;
			$filter->DefaultTaskStatus = (isset($_GET['TaskStatus'])) ? $_GET['TaskStatus'] : '';
			$filter->DefaultPaymentStatus = (isset($_GET['DefaultPaymentStatus'])) ? $_GET['DefaultPaymentStatus'] : '';
			$filter->Submitter = (isset($_GET['Submitter'])) ? $_GET['Submitter'] : -1;

			switch ($this->Controller(1)) {
				case 'Test':
					echo 'AJAX Test';
					break;

				case 'ActiveTaskList':
					$this->PutData ('filter', $filter);
					$this->PutData ('TaskSort', $_GET['TaskSort']);
					$this->LoadView('widgets/showactivetasks');
					break;

				case 'GetTasksWithCosts':
					$data = $db->ListTasksWithCost($filter);
					$this->PutData ('data', $data);
					$this->LoadView('showpaidtasks');
					break;

				case 'GetTasksByMilestone':
					$data = $db->ListTasksByMilestone($filter);
					$this->PutData ('data', $data);
					$this->PutData ('GroupTitle', 'Status');
					$this->PutData ('GroupField', 'status');
					$this->LoadView('widgets/showtasklisting');
					break;

				case 'GetTasksByPriority':
					$data = $db->ListTasksByPriority($filter);
					$this->PutData ('data', $data);
					$this->PutData ('GroupTitle', 'Priority');
					$this->PutData ('GroupField', 'priority');
					$this->LoadView('widgets/showtasklisting');
					break;

				case 'GetTasksByCreatedOn':
					$data = $db->ListTasksByCreatedOn($filter);
					$this->PutData ('data', $data);
					$this->PutData ('GroupTitle', 'Submitted On');
					$this->PutData ('GroupField', 'submitted');
					$this->LoadView('widgets/showtasklisting');
					break;

				case 'GetTasksByAssignedTo':
					$data = $db->ListTasksByPerson($filter);
					$this->PutData ('data', $data);
					$this->PutData ('GroupTitle', 'Assigned To');
					$this->PutData ('GroupField', 'assignedto');
					$this->LoadView('widgets/showtasklisting');
					break;

				case 'GetTasksByDue':
					$data = $db->ListTasksByTargetDate($filter);
					$this->PutData ('data', $data);
					$this->PutData ('GroupTitle', 'Target Date');
					$this->PutData ('GroupField', 'needby');
					$this->LoadView('widgets/showtasklisting');
					break;

				case 'GetTasksByProject':
					$data = $db->ListActiveTasksByProject($filter);
					$this->PutData ('data', $data);
					$this->PutData ('icons', true);
					$this->PutData ('PageTitle', 'By Project');
					$this->LoadView('widgets/showtasktree');
					break;

				case 'GetTasksByCompleted':
					$data = $db->ListTasksByCompletedOn($filter);
					$this->PutData ('data', $data);
					$this->LoadView('widgets/showrecentdone');
					break;

				case 'GetCompletedTasks':
					$data = $db->ListCompletedTasksByProject($filter);
					$this->PutData ('data', $data);
					$this->PutData ('icons', false);
					$this->PutData ('PageTitle', 'Completed Tasks');
					$this->LoadView('widgets/showtasktree');
					break;

				case 'GetApprovedTasks':
					$data = $db->ListApprovedTasksByProject($filter);
					$this->PutData ('data', $data);
					$this->PutData ('icons', false);
					$this->PutData ('PageTitle', 'Approved Tasks');
					$this->LoadView('widgets/showtasktree');
					break;

				case 'GetRecentApprovedTasks':
					$data = $db->ListTasksByApprovedOn($filter);
					$this->PutData ('data', $data);
					$this->PutData ('icons', false);
					$this->PutData ('PageTitle', 'Released Tasks');
					$this->LoadView('widgets/showrecentapproved');
					break;

				case 'GetRecentReleasedTasks':
					$data = $db->ListTasksByReleasedOn($filter);
					$this->PutData ('data', $data);
					$this->PutData ('icons', false);
					$this->PutData ('PageTitle', 'Approved Tasks');
					$this->LoadView('widgets/showrecentreleased');
					break;

				case 'GetTasksForRelease':
					$data = $db->ListTasksForRelease($filter);
					$this->PutData ('data', $data);
					$this->LoadView('widgets/showtaskstorelease');
					break;

				case 'GetBillableTasks':
					$data = $db->ListApproveBilledTasks($filter);
					$this->PutData ('data', $data);
					$this->LoadView('widgets/showbillabletasks');
					break;

				case 'GetBillingHistory':
					$data = $db->ListBilledTasks($filter);
					$this->PutData ('data', $data);
					$this->LoadView('widgets/showbillinghistory');
					break;

				case 'GetPayableTasks':
					$data = $db->ListApproveFixedPriceTasks($filter);
					$this->PutData ('data', $data);
					$this->LoadView('widgets/showpayabletasks');
					break;

				case 'GetPaymentHistory':
					$data = $db->ListPaidTasks($filter);
					$this->PutData ('data', $data);
					$this->LoadView('widgets/showpaymenthistory');
					break;

				case 'GetMilestoneSelectList':
					echo '<option value="-1">All</option>';
					$sql = "select milestoneid, name from milestones where prjid=$filter->DefaultPrj and completion is null order by targetdate";
					$db->FillList($sql);
					break;

				default:
					echo 'AJAX Error';
			}
		}
	}
}
}
?>