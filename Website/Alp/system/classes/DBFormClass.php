<?php
/*
Copyright (c) 2012, 2013, Nth Generation. All rights reserved.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

/*
The purpose of the form class is to
1. Provide a common styling of forms throughout the site.
2. Provide easy and cinsistent implementation of javascript field falidation
3. Implement original values in order to determine what values have changed
4. Implement fields with special requirements such as numeric fields or fixed length fields
5. Reload posted data in case the operation fails requiring the user to make a change to what data is submitted.

The class supports forms built using <table> tags or <div> tags

It is anticipated that child classes will be derived from this class to implement 
more specific fields related to your project. For instance a FirstNameField() function
might be used in an application that requires entry of a first name in order to require
consistent input with the same constraints throughout the application.
*/

class DBFormClass extends FormClass {

var $table;
var $key;
var $tablefields;
var $data;
var $updateproc;
var $createproc;
var $deleteproc;
var $where;

/********************************************************************************
Constructor

The constructor should not be called directly. It is called by the framework 
when the form() method is called to implement the form class. It initiates the 
class by reading the setting from the configuration file.
*********************************************************************************/
function DBFormClass($framework, $binding, $keyval)
{
	$data = NULL;
	$this->FormClass($framework);
	$bindings = $framework->LoadBindingConfig($binding);
	$this->table = $bindings['TableName'];
	$this->tablefields = $bindings['FieldList'];
	$this->createproc = (isset($bindings['CreateProc'])) ? $bindings['CreateProc'] : NULL;
	$this->updateproc = (isset($bindings['UpdateProc'])) ? $bindings['UpdateProc'] : NULL;
	$this->deleteproc = (isset($bindings['DeleteProc'])) ? $bindings['DeleteProc'] : NULL;
	$keyfield = $bindings['KeyField'];
	$this->where = '';

	if (is_array($keyfield)) {
		for ($x=0; $x<count($bindings); $x++) {
			if (isset($keyval[$x])) {
				$this->key[] = array($bindings[$x] => $keyval[$x]);
				$this->where .= $bindings[$x].'='.$this->MakeFieldValue($bindings[$x], $keyval[$x]);
			} else
				$framework->ShowErrorMessage('No value found for key field ' . $keyfield[$x]);
		}
	} else {
		$this->key = array($keyfield => $keyval);
		$this->where = $keyfield.'='.$this->MakeFieldValue($keyfield, $keyval);
	}
}

// NewData() informs the class that the form should be loaded with new data instead of reloading posted data.
// You would do this if reloading the same form for additional input after a successful save of the previous submission.
function NewData()
{
	$data = NULL;
	parent::NewData();
}

function Populate()
{
	$this->data = $this->Framework()->Database()->QueryTableRow($this->table, $this->key);

	foreach ($this->key as $var => $val) {
		$this->ShowHiddenField ($this->tablefields[$var][0], $val);
	}
}

private function MakeFieldValue($fldid, $posted=NULL)
{
	$val = '';
	if (!isset($this->tablefields[$fldid])) {
		$framework->ShowErrorMessage('No bound database binding for ' . $fldid);
		return $val;
	}

	$data = $this->tablefields[$fldid];

	if (!$posted && isset($_POST[$data[0]]))
		$posted = $_POST[$data[0]];

	if ($posted) {
		$db = $this->Framework()->Database();

		switch ($data[1]) {
			case 'S':
				$val = $db->MakeStringValue($posted);
				break;
			case 'I':
			case 'F':
				$val = $db->MakeNumericValue($posted);
				break;
			case 'D':
				$val = $db->MakeDateValue($posted);
				break;
			default:
				$framework->ShowErrorMessage('No bound database field type for ' . $fldid);
		}
	}
	return $val;
}

private function FindFieldname($name)
{
	foreach ($this->tablefields as $fld => $val) {
		if ($val[0] == $name)
			return $fld;
	}
	$this->Framework()->ShowErrorMessage('No bound database field for ' . $name);
}

function DoProcedure($data)
{
	$db = $this->Framework()->Database();
	$args = array();
	if (isset($data['SessionID']) && $data['SessionID']) {
		$args[] = array('field'=>'SessionID', 'type'=>'I', 'value'=>$db->GetSessionID());
	}
	foreach ($data['Fields'] as $fldid) {
		$binding = $this->tablefields[$fldid];
		$args[] = array('field'=>$fldid, 'type'=>$binding[1], 'value'=>$_POST[$binding[0]]);
	}
	return $db->ExecuteBoundProc($data['Name'], $args);
/*
	$sql = $data['Name'] . '(';
	$x = 0;
	if ($data['SessionID']) {
		$sql .= $this->Framework()->Database()->GetSessionID();
		$x++;
	}
	foreach ($data['Fields'] as $fldid) {
		$val = $this->MakeFieldValue($fldid);
		if ($x > 0)
			$sql .= ',';
		$sql .= $val;
		$x++;
	}
	$sql .= ')';

	return $this->Framework()->Database()->ExecuteProc($sql);
*/
}

function DoUpdate()
{
	if ($this->updateproc) {
		return $this->DoProcedure($this->updateproc);
	} else {
		$sql = "update $this->table set ";
		$x=0;
		foreach ($this->tablefields as $fldid => $binding) {
			if ($x>0)
				$sql .= ', ';
			$sql .= $fldid . '=' . $this->MakeFieldValue($fldid);
			$x++;
		}
		$sql .= ' where ' . $this->where;
		return $this->Framework()->Database()->Execute($sql);
	}
}

function DoCreate()
{
	if ($this->createproc) {
		return $this->DoProcedure($this->createproc);
	} else {
		$sql = "insert into $this->table (";
		$sql2 = ') values (';
		$x=0;
		foreach ($this->tablefields as $fldid => $binding) {
			if ($x>0) {
				$sql .= ', ';
				$sql2 .= ', ';
			}
			$sql .= $fldid;
			$sql2 .= $this->MakeFieldValue($fldid);
			$x++;
		}
		$sql .= $sql2 . ')';
		return $this->Framework()->Database()->Execute($sql);
	}
}

function DoDelete()
{
	if ($this->deleteproc) {
		return $this->DoProcedure($this->deleteproc);
	} else {
		$sql = "delete from $this->table where $this->where";
		return $this->Framework()->Database()->Execute($sql);
	}
}

function GetQueryValue($fieldname)
{
	if ($this->data) {
		$key = $this->FindFieldname($fieldname);
		$value = $this->data->$key;
	} else {
		$value = '';
	}
	return $value;
}

function ShowBoundListField ($label, $name, $list=NULL, $req=0, $sel='', $onchange='')
{
	parent::ShowListField ($label, $name, $list, $req, $this->GetQueryValue($name), $onchange);
}

function ShowBoundTextField ($label, $name, $maxlen, $size, $value='', $minlen=0, $help='')
{
	parent::ShowTextField ($label, $name, $maxlen, $size, $this->GetQueryValue($name), $minlen, $help);
}

}
?>