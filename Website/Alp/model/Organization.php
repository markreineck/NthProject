<?php
class Organization extends DBTable {

function Organization($framework)
{
	$bindings = array (
		'TableName' => 'organizations',
		'FieldList' => array (
			'Name' => new TextField ('Organization Name', 'name', 80, 1),
			'OrgType' =>  new SelectField ('Organization Type', 'status', 2)
		),
		'KeyField' => new KeyField ('orgid','I'),

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

	$this->DBTable($framework, $bindings);
}

}
?>