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

class DBTable {

private $table;
private $key;
private $tablefields;
private $data;
private $updateproc;
private $createproc;
private $deleteproc;
private $framework;

/********************************************************************************
Constructor

The constructor should not be called directly. It is called by the framework 
when the form() method is called to implement the form class. It initiates the 
class by reading the setting from the configuration file.
*********************************************************************************/
function DBTable($framework, $bindings)
{
	$this->framework = $framework;
	$this->data = NULL;
//	$bindings = $framework->LoadBindingConfig($binding);
	$this->table = $bindings['TableName'];
	$this->tablefields = $bindings['FieldList'];
	$this->createproc = (isset($bindings['CreateProc'])) ? $bindings['CreateProc'] : NULL;
	$this->updateproc = (isset($bindings['UpdateProc'])) ? $bindings['UpdateProc'] : NULL;
	$this->deleteproc = (isset($bindings['DeleteProc'])) ? $bindings['DeleteProc'] : NULL;
	$keyfield = $bindings['KeyField'];
	$this->where = '';

	if (is_array($keyfield)) {
		$this->key = $keyfield;
	} else {
		$this->key = array($keyfield);
	}
}

function __call($method, $args)
{
	if (isset($this->data[$method]))
		return $this->data[$method];
	if (isset($this->tablefields[$method]))
		return $this->data[$this->tablefields[$method]->Field];
	foreach ($this->key as $key) {
		if ($key->Field == $method)
			return $key->Value;
	}
}

function Framework()
{
	return $this->framework;
}

// NewData() informs the class that the form should be loaded with new data instead of reloading posted data.
// You would do this if reloading the same form for additional input after a successful save of the previous submission.
function NewData()
{
	$this->data = NULL;
	if ($form = $this->framework->GetForm())
		$form->NewData();
	foreach ($this->key as $k) {
		$k->SetValue('');
	}
}

function HasKey()
{
	$haskey = true;
	foreach ($this->key as $k) {
		if (!$k->Value)
			$haskey = false;
	}
	return $haskey;
}

function SetPostedKey()
{
	foreach ($this->key as $k) {
		$keyidx = 'key'.$k->Field;
		if (isset($_POST[$keyidx]))
			$k->SetValue($_POST[$keyidx]);
		else
			$k->SetValue('');
	}
}

function SetPostedData()
{
	$this->SetPostedKey();

	if ($this->tablefields) {
		if (!$this->data)
			$this->data = array();
		foreach ($this->tablefields as $key => $val) {
			$this->data[$key] = $_POST[$key];
		}
	}
}

function SetKey($keyval)
{
	if (is_array($keyval)) {
		foreach ($this->key as $k) {
			if (isset($keyval[$k->Field]))
				$k->SetValue($keyval[$k->Field]);
			else
				$this->framework->ShowErrorMessage('No value found for key field ' . $k->Field);
		}
	} else if (count($this->key) > 1) {
		$this->framework->ShowErrorMessage('Compound key requited', 'DBTab;e');
	} else {
		$this->key[0]->SetValue($keyval);
	}
}

function SelectAll($order='')
{
	$db = $this->Framework()->Database();
	return $db->QueryTableRows($this->table, $order);
}

function Populate()
{
	if (!$this->HasKey()) {
		$this->framework->ShowErrorMessage('Attempted to read a record without a key', 'DBTable Error');
		$this->data = NULL;
	} else {
		$db = $this->Framework()->Database();
		$this->data = $db->QueryTableRow($this->table, $this->MakeWhere());

		if ($this->data) {
			$x = 1;
			$form = $this->Framework()->Forms();

			foreach ($this->key as $k) {
				$form->ShowHiddenField ('key'.$k->Field, $k->FormattedValue($db));
			}
		}
	}
}

private function MakeWhere()
{
	$where = '';
	$db = $this->Framework()->Database();
	foreach ($this->key as $k) {
		if ($where)
			$where .= ' and ';
		else
			$where .= ' where ';
		$where .= $k->Field . '=' . $k->FormattedValue($db);
	}
	return $where;
}

private function MakeFieldValue($fldid, $posted=NULL)
{
	$val = '';
	if (!isset($this->tablefields[$fldid])) {
		$framework->ShowErrorMessage('No bound database binding for ' . $fldid);
		return $val;
	}

	if (!$posted && isset($_POST[$fldid]))
		$posted = $_POST[$fldid];

	if ($posted) {
		$fld = $this->tablefields[$fldid];
		$val = $fld->FormattedValue($this->Framework()->Database(), $posted);
	} else
		$val = 'null';

	return $val;
}

private function DoProcedure($data)
{
	$db = $this->Framework()->Database();
	$args = MakeFieldList();
	$args = array();
	foreach ($data->Fields as $fldid) {
		if ($fldid == 'SessionID') {
			$args[] = array('field'=>'SessionID', 'type'=>'I', 'value'=>$db->GetSessionID());
		} else if (substr($fldid,0,4) == 'Key-') {
			$keyidx = substr($fldid,4);
			$fld = $this->key[$keyidx-1];
			$args[] = array('field'=>$fld->Field, 'type'=>$fld->DataType, 'value'=>$fld->Value);
		} else {
			$fld = $this->tablefields[$fldid];
			$args[] = array('field'=>$fld->Field, 'type'=>$fld->DataType, 'value'=>$_POST[$fldid]);
		}
	}
	return $db->ExecuteBoundProc($data->Name, $args);
}

function DoUpdateOrCreate()
{
	if ($this->HasKey()) {
		return $this->DoUpdate();
	} else {
		return $this->DoCreate();
	}
}

function DoUpdate()
{
	if ($this->updateproc) {
		return $this->DoProcedure($this->updateproc);
	} else {
		$sql = "update $this->table set ";
		$x=0;
		foreach ($this->tablefields as $fldid => $binding) {
			if (isset($_POST[$fldid])) {
				if ($x>0)
					$sql .= ', ';
				$sql .= $binding->Field . '=' . $this->MakeFieldValue($fldid);
				$x++;
			}
		}
		$sql .= $this->MakeWhere();
		return $this->Framework()->Database()->Execute($sql) < 1;
	}
}

function DoCreate()
{
	if ($this->createproc) {
		return $this->DoProcedure($this->createproc);
	} else {
		$args = array();
		$sql = "insert into $this->table (";
		$sql2 = ') values (';
		$x=0;
		foreach ($this->tablefields as $fldid => $binding) {
			if ($x>0) {
				$sql .= ', ';
				$sql2 .= ', ';
			}
			$sql .= $binding->Field;
			$sql2 .= $this->MakeFieldValue($fldid);
			$x++;
		}
		$sql .= $sql2 . ')';

		$result = $this->Framework()->Database()->Execute($sql);
		if ($result > 0 && count($this->key) == 1) {
			$this->key[0]->SetValue($this->Framework()->Database()->GetLastID());
		}
		return $result < 1;
	}
}

function DoDelete()
{
	if ($this->deleteproc) {
		return $this->DoProcedure($this->deleteproc);
	} else {
		$sql = "delete from $this->table" . $this->MakeWhere();
		return $this->Framework()->Database()->Execute($sql) < 1;
	}
}

function GetQueryValue($fld)
{
	if ($this->data && $fld && isset($this->data[$fld->Field]))
		$value = $this->data[$fld->Field];
	else
		$value = '';

	return $value;
}

function ShowListField ($fieldname, $list=NULL, $onchange='')
{
	if (isset($this->tablefields[$fieldname])) {
		$fld = $this->tablefields[$fieldname];
		$this->Framework()->GetForm()->ShowListField ($fld->Label, $fieldname, $list, $fld->Required, $this->GetQueryValue($fld), $onchange);
	}
}

function ShowTextField ($fieldname, $size=0)
{
	if (isset($this->tablefields[$fieldname])) {
		$fld = $this->tablefields[$fieldname];
		$this->Framework()->GetForm()->ShowTextField ($fld->Label, $fieldname, $fld->Max, ($size) ? $size : $fld->Max, 
			$this->GetQueryValue($fld), $fld->Min, $fld->Hint);
	}
}

function ShowDateField ($fieldname)
{
	if (isset($this->tablefields[$fieldname])) {
		$fld = $this->tablefields[$fieldname];
		$this->Framework()->GetForm()->ShowDateField ($fld->Label, $fieldname, 
			$this->GetQueryValue($fld), $fld->Required);
	}
}

function ShowTextAreaField ($fieldname)
{
	if (isset($this->tablefields[$fieldname])) {
		$fld = $this->tablefields[$fieldname];
		$this->Framework()->GetForm()->ShowTextAreaField ($fld->Label, $fieldname, $fld->Rows, $fld->Cols, 
			$this->GetQueryValue($fld));
	}
}

function ShowCheckField ($fieldname)
{
	if (isset($this->tablefields[$fieldname])) {
		$fld = $this->tablefields[$fieldname];
		$this->Framework()->GetForm()->ShowCheckBoxField ($fld->Label, $fieldname, 1, 
			$this->GetQueryValue($fld), $fld->Hint);
	}
}

function ShowHiddenField ($fieldname)
{
	if (isset($this->tablefields[$fieldname])) {
		$fld = $this->tablefields[$fieldname];
		$this->Framework()->GetForm()->ShowHiddenField ($fieldname, $this->GetQueryValue($fld));
	}
}

function DataChanged()
{
	$fields = array_keys($this->tablefields);
	return $this->Framework()->DataChanged($fields);
}

}
?>