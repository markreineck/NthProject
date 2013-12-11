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
This version of the database class implements MySQL by deriving from the php mysqli class
and implementing the base functionality required by the Alp framework.
*/

class DatabaseClass extends mysqli {

var $debug=false;
var $conn;
var $stmt;
var $errorcode = 0;
var $errormsg = ''; //, $persistent, $result_proc, $result_parse;
var $fresh_row_xy;
var $procquery;
var $querytype = 1; // 1=associative array, 2=Indexed array, 3=Object
var $lastsql;
var $framework;

/****************************************************************************
Constructor
The constructor loads settings from the configuration file and then forms an 
initial connection to the database. The connection parameters can be overridden 
to connect to a different database.
*****************************************************************************/
function DatabaseClass($framework, $pwd='', $username='', $dbname='', $host='')
{
	$dbconfig = $framework->LoadDatabaseConfig();
	$this->framework = $framework;
	$this->debug = $framework->DebugMode;
	$this->querytype = $dbconfig['QueryMode'];
//	error_reporting (E_ALL);

	if (empty($host)) $host = $dbconfig['HostName'];
	if (empty($dbname)) $dbname = $dbconfig['DatabaseName'];
	if (empty($username)) $username = $dbconfig['UserName'];
	if (empty($pwd)) $pwd = $dbconfig['Password'];

	if ($this->debug > 2)
		$this->Debug("Connect: $host, $username, $pwd, $dbname");
	parent::__construct($host,$username,$pwd,$dbname);

	$this->errorcode = mysqli_connect_errno();
	if ($this->errorcode)
		$this->errormsg = mysqli_connect_error();

	return ($this->errorcode);
}

function Framework()
{
	return $this->framework;
}

function CloseDB()
{
	$this->close(); //close the connection to free the memory
}

/****************************************************************************
Debugging Functions
Debug mode has the following values:
0 = no debug
1 = sql and other debug information will be echoee to the page inside of html comments
2 or higher = sql and other debug information will be echoee to the page and visible
*****************************************************************************/
function DebugMode($dbg=-1)
{
	if ($dbg >= 0)
		$this->debug = $dbg;
	return $this->debug;
}

function Debug($sql)
{
	if ($this->debug == 1)
		echo "<!-- Debug sql = $sql -->
";
	else if ($this->debug > 1)
		echo "$sql<br>
";
}

// LastSQL retrieves the last executed sql statement
function LastSQL ()
{
	return ($this->lastsql);	
}

/****************************************************************************
Error Handling Functions
*****************************************************************************/
// HasError() returns true if an error condition exists.
function HasError ()
{
	if ($this->errorcode != 0)
		return true;
	else if (strlen($this->errormsg) > 0)
		return true;
	else
		return false;
}

// Sets an error condition. You wouild almost never do this from outside of the class
// but that has ot been precluded by making this function private.
function SetError ($code, $msg='')
{
	$this->errorcode = $code;
	if (!empty($msg))
		$this->errormsg = $msg;
}

// ErrorMsg() retrieves the pending error message if one exists.
// User configured error messages can be stored in the databaseerrors.php file.
function ErrorMsg ()
{
	if (strlen($this->errormsg) > 0) {
		return ($this->errormsg);
	} else if ($this->errorcode != 0) {
		$errorlist = $this->framework->LoadClassConfig('databaseerrors');
		if ($errorlist && isset($errorlist[$this->errorcode]))
			return $errorlist[$this->errorcode];
		else
			return 'Database error ' . $this->errorcode;
	} else
		return '';
}

// ErrorCode() retrieves the pending error code or else 0 if there is none
function ErrorCode ()
{
	return ($this->errorcode);	
}

// ClearErrors() is used to clear prior errors before executing new sql.
private function ClearErrors()
{
	$this->errorcode = 0;
	$this->errormsg = '';
}

// SaveErrors() will get mysqli errors and save them into the DatabaseClass fields.
private static function SaveObjectErrors($stmt)
{
	if ($stmt->errno) {
		$this->errorcode = $stmt->errno;
		$this->errormsg = $stmt->error;
		$this->Debug($this->errormsg);
	}
}

private function SaveErrors($stmt=NULL)
{
	if ($stmt) {
		$this->SaveObjectErrors($stmt);
	} else {
		if ($this->errno) {
			$this->errorcode = $this->errno;
			$this->errormsg = $this->error;
			$this->Debug($this->errormsg);
		}
	}
}

/****************************************************************************
Execution Functions

Execution functions are used for executing anything that is not a select.
*****************************************************************************/
// Execute is intended to be used to execute any database update statement
// (insert, update, delete, or stored procedure call)
function Execute ($sql)
{
	$result = $this->PrepareQuery($sql);
	if ($this->HasError())
		return false;
	return $this->affected_rows;
}

function CaptureProcErrors()
{
	if (!$this->errorcode)
		$this->errorcode = $this->GetErrorCode();
	if ($this->errorcode)
		$this->Debug('Error:'.$this->errorcode);
	return $this->errorcode;
}

// ExecuteProc is intended to be used to execute a stored procedure
// If an error is returned the vaslued will be saved to $this->error
function ExecuteProcSQL ($sql)
{
	$result = $this->Execute($sql);
	return $this->CaptureProcErrors();
}

function ExecuteProc ($data)
{
	if (strtolower(substr($data,0,5)) != 'call ')
		$data = 'call ' . $data;
	return $this->ExecuteProcSQL ($data);
}

function ExecuteBoundProc ($proc, $data)
{
	$this->ClearErrors();
	$size = count($data);
	$args = str_repeat('?, ', $size);
	$sql = "call $proc(" . substr($args, 0, $size*3-2) . ')';
	$this->lastsql = $sql;
	$this->Debug($sql);
	$stmt = $this->prepare($sql);
	if (!$stmt) {
		$this->SetError(-1,'Statement preparation error');
	} else {
		$types = '';
		$params = array();
		foreach ($data as $field) {
			switch ($field['type']) {
				case 'I':
					$type = 'i';	break;
				case 'F':
					$type = 'd';	break;
				case 'B':
					$type = 'b';	break;
				default:
					$type = 's';
			}
			$types .= $type;
			$params[] = $field['value'];
			$this->Debug("$field ($type) = " . $field['value']);
		}
		array_unshift($params, $types);

		call_user_func_array( array( $stmt, 'bind_param' ), $params);
		if (!$stmt->execute()) {
			$this->errorcode = $stmt->errno;
			$this->errormsg = $stmt->error;
			$this->Debug($this->errormsg);
		}
		$stmt->close();
		$this->SaveErrors();
	}
	return $this->CaptureProcErrors();
}

// ExecuteKey is like Execute except that it is intended for insertion into tables with an auto_increment only. 
// It will return the value of the autoincrement
function ExecuteKey ($sql)
{
	$result = $this->ExecuteProcSQL($sql);
	return (!$result) ? $this->GetLastID() : false;
}

/****************************************************************************
Query Functions

Different query functions are provided depending on whether you are selecting
a single value, a single row or multiple rows. Other specialized query functions
are also provided.
*****************************************************************************/
private function PrepareQuery($sql)
{
	$this->Debug($sql);
	$this->ClearErrors();
	$this->lastsql = $sql;
	$result = $this->query($sql);
	$this->SaveErrors();
	return $result;
}

// Select returns a single field
function Select($sql)
{
	$result = $this->PrepareQuery($sql); 
	if (!$result)
		return '';

	$row = $result->fetch_row();
	if (strtolower(substr($sql,0,5)) == 'call ')
		$this->next_result();
	return $row[0];
}


private function GetNextRow ($result, $fieldtype)
{
	if (!$result)
		return NULL;
	if ($fieldtype == 1)
		$row = $result->fetch_assoc();
	else if ($fieldtype == 2)
		$row = $result->fetch_row();
	else
		$row = $result->fetch_object();
	return $row;
}

// SelectRow returns all of the records in the row as an array 
// or as an object depending on the query type indicated
function SelectRow ($sql, $fieldtype=0)
{
	$result = $this->PrepareQuery($sql); 
	if (strtolower(substr($sql,0,5)) == 'call ')
		$this->next_result();

	if (!$result)
		return NULL;

	$fieldtype = ($fieldtype) ? $fieldtype : $this->querytype;
	return $this->GetNextRow ($result, $fieldtype);
}

// SelectRow returns all of the records in the row as an array 
// or as an object depending on the query type indicated
function QueryTableRow ($table, $where)
{
/*
		$where = '';
	foreach ($key as $k) {
		if ($where)
			$where .= ' and ';
		else
			$where .= ' where ';
		$where .= $k->Field . '=';
		switch ($k->DataType) {
			case 'I'
				$where .= $this->MakeNumericValue($k->Value);
				break;
			case 'D'
				$where .= $this->MakeDateValue($k->Value);
				break;
			default:
				$where .= $this->MakeStringValue($k->Value);
		}
	}
*/
	$sql = 'select * from ' . $table . $where;
	$result = $this->PrepareQuery($sql); 
//	if (strtolower(substr($sql,0,5)) == 'call ')
//		$this->next_result();

	if (!$result)
		return NULL;

	return $this->GetNextRow ($result, 1);
}
/*
function QueryTableRow ($table, $key)
{
	$where = '';
	foreach ($key as $field => $val) {
		if ($where)
			$where .= ' and ';
		else
			$where .= ' where ';
		$where .= $field . '=';
		$where .= $this->MakeStringValue($val);
	}
	$sql = 'select * from ' . $table . $where;
	$result = $this->PrepareQuery($sql); 
	if (strtolower(substr($sql,0,5)) == 'call ')
		$this->next_result();

	if (!$result)
		return NULL;

	return $this->GetNextRow ($result, 3);
}
*/
// SelectAll is intended for multi-row queries. It returns an array of records.
// Each record is either an array or as an object depending on the query type indicated.
function SelectAll($sql, $fieldtype=0)
{
	$result = $this->PrepareQuery($sql); 

	if (strtolower(substr($sql,0,5)) == 'call ')
		$this->next_result();

	if (!$result)
		return NULL;
	else if (!is_object($result))
		return NULL;

	$list = array();

	$fieldtype = ($fieldtype) ? $fieldtype : $this->querytype;

	$row = $this->GetNextRow ($result, $fieldtype);
	while ($row) {
		$list[] = $row;
		$row = $this->GetNextRow ($result, $fieldtype);
	}
	return $list;
}


// FillList will echo to the page the selected records as options for a selection box
// The 1st field selected should be the id and the 2nd should be the description
// ReturnList() is now the preferred method of doing this.
function FillList ($sql, $selected=0)
{
	$cnt = 0;
	$this->Debug($sql);
	$this->ClearErrors();
	$result = $this->query($sql);
	$row = $this->GetNextRow ($result, 2);
	while ($row) {
		$cnt++;
		$id = $row[0];
		$name = $row[1];
		if ($selected == $id) {
			echo "<OPTION value=$id selected>$name</OPTION>";
		} else {
			echo "<OPTION value=$id>$name</OPTION>";
		}
		$row = $this->GetNextRow ($result, 2);
	}
	return $cnt;			
}

// ReturnList does the same thing as FillList except that it returns the list
// as a string instead of echoing to the page. 
function ReturnList ($sql, $selected=0)
{
	$data = '';
	$this->Debug($sql);
	$this->ClearErrors();
	$result = $this->query($sql);

	$row = $this->GetNextRow ($result, 2);
	while ($row) {
		$id = $row[0];
		$name = $row[1];
		if ($selected == $id) {
			$data .= "<OPTION value=$id selected>$name</OPTION>";
		} else {
			$data .= "<OPTION value=$id>$name</OPTION>";
		}
		$row = $this->GetNextRow ($result, 2);
	}
	return $data;			
}

// SelectDelimitedList() will perform a multi-row select and return the result
// as a single string with each field and each row separated by the indicated delimiters.
function SelectDelimitedList($sql, $coldelim=',', $rowdelim='\n')
{
	$data = '';
	$list = $this->SelectAll ($sql, 2);

	foreach ($list as $row) {
		while ($row) {
			foreach ($row as $fld)
				$data .= $fld . $delim;
			$data .= $type;
			$row = $this->GetNextRow ($result, 2);
		}
	}
	return $data;
}

/****************************************************************************
MySQL specific Query Functions

These functions are specific to MySQL and therefor are not portable within the 
Alp framework. They are not recommended for use.
*****************************************************************************/
 
// GetLastID() is a MySQL specific query returning the id of the last autoincrement field
// When inserting into a table that generates an autoincrement value it is preferred to 
// use ExecuteKey() as that function will return the key directly.
function GetLastID()
{
	$id = $this->Select('select last_insert_id()');
	$this->Debug('ID=' . $id);
	return $id;
}

// SelectMysqlVariable() returns a MySQL session variable that can be set by a stored 
// procedure. This has been added as a means of returning error codes from stored 
// procedures as MySQL does not support throwing user defined error codes. It was 
// implemented in a generic manner because could have other uses.
function SelectMysqlVariable ($var)
{
	if (empty($var))
		return '';
	$val = $this->Select('select @'.$var);
	$this->Debug($val);
	return $val;
}

// GetErrorCode() returns a MySQL @err session variable that can be set by a stored 
// procedure. This has been added as a means of returning error codes from stored 
// procedures as MySQL does not support throwing user defined error codes. The convention 
// with the Alp framework is to hasve a stored procedure set @err to the error code.
function GetErrorCode()
{
	return $this->SelectMysqlVariable('err');
}

/****************************************************************************
Field Formatting Functions
These functions are provided to clean and format fields that will be used 
to create sql statements.
*****************************************************************************/

// MakeBoolValue() returns 'X' or null and is used to sore a boolean in a character field
function MakeBoolValue ($str)
{
	$str = trim($str);
	return (strlen($str) > 0) ? "'X'" : 'null';
}

// MakeBoolValue() returns 1 or null and is used to sore a boolean in an int field
function MakeNumericBoolValue ($str)
{
	return ($str > 0) ? '1' : 'null';
}

// MakeStringValue() will format a string escaping special characers. The final string 
// will be wrapped in single quites. If an empty string is passed then "null" will be 
// returned so that the resulting insert or update will not place an empty string into
// the field. By default html tags are removed from the string. This can be overridden
// by setting $allowhtml=true.
function MakeStringValue ($str, $allowhtml=false)
{
	$str = get_magic_quotes_gpc() ? stripslashes($str):$str;	
	$str = trim($str);
	$str = $this->real_escape_string($str);
	if (!$allowhtml)
		$str = strip_tags($str);
	return (strlen($str) > 0) ? "'$str'" : 'null';
}

// MakeImageValue() will read an image file that has been uploaded and format it
// so that it can be stored into a mysql blob field.
function MakeImageValue ($file)
{
	return $this->real_escape_string(file_get_contents($file));
}

function RemoveStringQuote($str)
{
	$countquote = explode("'",$str);
	if (count($countquote) > 2) {
		$str = substr($str, 1, -1);
	}
	return $str;
}

// MakeDateValue() will take a string representation of a date in most
// common date formats and convert it to the 'yyyy-mm-dd' format expected
// by mysql. If an empty string is passed then "null" will be returned.
function MakeDateValue ($date)
{
	if ($date && strlen($date) > 4) {
		$date = str_replace('-','/',str_replace("'","",$date));
		$newdate = date("'Y-m-d'",strtotime($date));
	} else {
		$newdate = 'null';
	}
	return $newdate;
}

// MakeDateValue() does the same thing as MakeDateValue() except that the
// date is incremented by 1 day. This is useful when querying a date range
// for records with a datetime field that contain a time componenet.
function MakeNextDayValue ($date)
{
	if ($date && strlen($date) > 4) {
		$date = str_replace('-','/',str_replace("'","",$date));
		$dt = strtotime("+1 day", strtotime($date));
		$newdate = date("'Y-m-d'",$dt);
	} else {
		$newdate = 'null';
	}
	return $newdate;
}

// MakeTimeValue() will take a time represented by hours, minutes and am/pm
// and convert it to the 'hh-mm-ss' format expected by mysql. 
// If an empty string is passed then "null" will be returned.
function MakeTimeValue ($hr,$min,$ampm='')
{
	if ($hr >= 1 && $hr <= 24 && $min >= 0 && $min<60 && strlen($min) > 0) {
		$ampm = strtolower($ampm);
		if ($hr <12 && $ampm=='pm')
			$hr += 12;
		else if ($hr==12 && $ampm=='am')
			$hr = 0;
		if (strlen($min) < 2)
			$min = '0' . $min;
		$time = "'$hr:$min:00'";
	} else {
		$time = 'null';
	}
	return $time;
}


// MakeDateValue() will take a UNIX time value and convert it to the 
// 'yyyy-mm-dd hh-mm-ss' format expected by mysql. 
// If an empty string is passed then "null" will be returned.
function MakeDateTimeValue ($unixtime)
{
	if ($unixtime > 0) {
		$utime = strtotime($unixtime);
		$newdate = date("'Y-m-d H:i:s'", $unixtime);
	} else {
		$newdate = 'null';
	}
	return $newdate;
}

// MakeNumericValue() will take a float or int value and in most cases
// return that value as is. If the value is empty, 0 or nonnumeric then 
// "null" is returned.
function MakeNumericValue ($val)
{
	$str = trim($val);
	if (!is_numeric($val))
		return 'null';
	return (strlen($val) > 0) ? $val : 'null';
}

function MakePhoneValue ($val1,$val2,$val3,$separator=".")
{
	if (strlen($val2) == 3 && strlen($val3) == 4) {
		$phone = (strlen($val1) > 0)
			? $val1.$separator.$val2.$separator.$val3
			: $val2.$separator.$val3;
		$phone = "'$phone'";
	} else 
		$phone = 'null';
	return $phone;
}

}
?>
