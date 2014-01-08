<?php
class TaskStatus extends DBTable {

function TaskStatus($framework)
{
	$bindings = array (
		'TableName' => 'taskstatus',
		'FieldList' => array (
			'Name' => new TextField ('Organization Name', 'name', 40, 1),
			'Hold' => new CheckField ('Task Hold', 'hold')
		),
		'KeyField' => new KeyField ('statusid','I')
	);

	$this->DBTable($framework, $bindings);
}

}
?>