<?php
$bindings = array (
	'TableName' => 'organizations',
	'FieldList' => array (
		'orgid' => array('OrgID','I'),
		'name' =>  array('Name', 'S'),
		'status' =>  array('OrgType', 'I')
	),
	'KeyField' => 'orgid',
	'UpdateProc' => array(
		'Name' => 'UpdateOrganization',
		'SessionID' => true,
		'Fields' => array('orgid', 'status', 'name')
	),
	'CreateProc' => array(
		'Name' => 'CreateCompany',
		'SessionID' => true,
		'Fields' => array('status', 'name')
	),
	'DeleteProc' => array(
		'Name' => 'DeleteOrganization',
		'SessionID' => true,
		'Fields' => array('orgid')
	)
);
?>