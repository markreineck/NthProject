<?php
class User extends DBTable {

function User($framework)
{
	$userbinding = array (
		'TableName' => 'users',
		'FieldList' => array (
			'FirstName' => new TextField ('First Name', 'firstname', 20, 1),
			'LastName' => new TextField ('Last Name', 'lastname', 20, 1),
			'Initials' => new TextField ('Initials', 'initials', 4, 1),
			'Email' => new TextField ('Email Address', 'email', 100, 1),
			'Organization' =>  new SelectField ('Organization', 'orgid', 2),
			'Status' =>  new SelectField ('User Type', 'status', 2)
		),
		'KeyField' => new KeyField ('userid','I'),

		'UpdateProc' => new DBProcedure('UpdateUser', array('SessionID', 'Key-1', 'Organization', 'Status', 'FirstName', 'LastName', 'Initials', 'Email')),
		'CreateProc' => new DBProcedure('CreateUser', array('SessionID', 'Organization', 'Status', 'FirstName', 'LastName', 'Initials', 'Email'))
	);

	$attributebinding = array (
		'TableName' => 'userfields',
		'FieldList' => array (
			'FirstName' => new TextField ('First Name', 'firstname', 20, 1),
			'LastName' => new TextField ('Last Name', 'lastname', 20, 1),
			'Initials' => new TextField ('Initials', 'initials', 4, 1),
			'Email' => new TextField ('Email Address', 'lastname', 100, 1),
			'Organization' =>  new SelectField ('Organization', 'orgid', 2),
			'Status' =>  new SelectField ('User Type', 'status', 2)
		),
		'KeyField' => array(new KeyField ('userid','I'), new KeyField ('fieldid','I')),

		'UpdateProc' => array(
			'Name' => 'UpdateOrganization',
			'Fields' => array('SessionID', 'Key-1', 'OrgType', 'Name')
		),
		'CreateProc' => array(
			'Name' => 'CreateCompany',
			'Fields' => array('SessionID', 'OrgType', 'Name')
		),
		'DeleteProc' => array(
			'Name' => 'DeleteOrganization',
			'Fields' => array('SessionID', 'Key-1')
		)
	);

	$this->DBTable($framework, $userbinding);
}

}
?>