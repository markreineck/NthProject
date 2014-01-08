<?php
class DBProcedure {
	
	public $Name, $Fields;

	function DBProcedure ($name, $fields)
	{
		$this->Name = $name;
		$this->Fields = $fields;
	}
}
?>