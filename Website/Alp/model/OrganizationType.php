<?php
class OrganizationType extends DBTable {

function OrganizationType($framework)
{
	$bindings = array (
		'TableName' => 'orgstatus',
		'FieldList' => array (
			'Name' => new TextField ('Organization Name', 'name', 40, 1)
		),
		'KeyField' => new KeyField ('statusid','I')
	);

	$this->DBTable($framework, $bindings);
}

function Add($name)
{
	$db = $this->Framework()->Database();
	$args = array();
	$args[] = array('field'=>'SessionID', 'type'=>'I', 'value'=>$db->GetSessionID());
	$args[] = array('field'=>'name', 'type'=>'S', 'value'=>$name);
	return $db->ExecuteBoundProc('CreateOrgType', $args);
}

function Update($id, $name)
{
	$db = $this->Framework()->Database();
	$args = array();
	$args[] = array('field'=>'SessionID', 'type'=>'I', 'value'=>$db->GetSessionID());
	$args[] = array('field'=>'statusid', 'type'=>'I', 'value'=>$id);
	$args[] = array('field'=>'name', 'type'=>'S', 'value'=>$name);
	return $db->ExecuteBoundProc('UpdateOrgType', $args);
}

function Delete($id)
{
	$db = $this->Framework()->Database();
	$args = array();
	$args[] = array('field'=>'SessionID', 'type'=>'I', 'value'=>$db->GetSessionID());
	$args[] = array('field'=>'statusid', 'type'=>'I', 'value'=>$id);
	return $db->ExecuteBoundProc('DeleteOrgType', $args);
}

}
?>