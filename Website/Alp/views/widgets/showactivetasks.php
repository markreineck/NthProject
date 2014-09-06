<?php
$db = $this->Model();

switch ($TaskSort) {

	case 'Priority':
		$data = $db->ListTasksByPriority($filter);
		$this->PutData ('data', $data);
		$this->PutData ('GroupTitle', 'Priority');
		$this->PutData ('GroupField', 'priority');
		$this->LoadView('widgets/showtasklisting');
		break;

	case 'CreatedOn':
		$data = $db->ListTasksByCreatedOn($filter);
		$this->PutData ('data', $data);
		$this->PutData ('GroupTitle', 'Submitted On');
		$this->PutData ('GroupField', 'submitted');
		$this->LoadView('widgets/showtasklisting');
		break;

	case 'AssignedTo':
		$data = $db->ListTasksByPerson($filter);
		$this->PutData ('data', $data);
		$this->PutData ('GroupTitle', 'Assigned To');
		$this->PutData ('GroupField', 'assignedto');
		$this->LoadView('widgets/showtasklisting');
		break;

	case 'Due':
		$data = $db->ListTasksByTargetDate($filter);
		$this->PutData ('data', $data);
		$this->PutData ('GroupTitle', 'Target Date');
		$this->PutData ('GroupField', 'needby');
		$this->LoadView('widgets/showtasklisting');
		break;

	case 'Project':
		$data = $db->ListActiveTasksByProject($filter);
		$this->PutData ('data', $data);
		$this->PutData ('icons', true);
		$this->PutData ('PageTitle', 'By Project');
		$this->LoadView('widgets/showtasktree');
		break;
}
?>

