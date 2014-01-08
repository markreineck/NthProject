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

		'UpdateProc' => new DBProcedure('UpdateOrganization', array('SessionID', 'Key-1', 'OrgType', 'Name')),
		'CreateProc' => new DBProcedure('CreateCompany', array('SessionID', 'OrgType', 'Name')),
		'DeleteProc' => new DBProcedure('DeleteOrganization', array('SessionID', 'Key-1'))
	);

	$this->DBTable($framework, $bindings);
}

}
?>