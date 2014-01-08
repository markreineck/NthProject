<?php
class UserType extends DBTable {

function UserType($framework)
{
	$bindings = array (
		'TableName' => 'userstatus',
		'FieldList' => array (
			'Name' => new TextField ('Organization Name', 'name', 40, 1),
			'PayType' => new SelectField ('Payment Type', 'paytype', 40, 2)
		),
		'KeyField' => new KeyField ('statusid','I')
	);

	$this->DBTable($framework, $bindings);
}

}
?>