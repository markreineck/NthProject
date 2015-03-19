<?php 
// Read the database name for the site
//include ('mysqldb.php');

class DatabaseClass {

var $debug=false;
var $conn;
var $stmt;
var $errorcode = 0;
var $error = ''; //, $persistent, $result_proc, $result_parse;
var $bind = array();  
var $fresh_row_xy;
		
function DatabaseClass($framework)
{
	$dbconfig = $framework->LoadDatabaseConfig();
	$this->debug = $framework->DebugMode;
	error_reporting (E_ALL);

	$this->conn = mysql_connect($dbconfig['DB_HOST'],$dbconfig['DB_USER'],$dbconfig['DB_PASSWORD']);
	if (!$this->conn) {
//		$this->error="Can not access the database";	
	} else {
		mysql_select_db($dbconfig['DB_SCHEMA']);
	}
	//this is the now the class globalvariables that holds the connection in our database: $this->$conn
	return ($this->conn);
}

function DatabaseHandle ()
{
	return ($this->conn);	
}

function DebugMode($dbg=0)
{
	$this->debug = $dbg;
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


function HasError ()
{
	if ($this->errorcode != 0)
		return true;
	else if (strlen($this->error) > 0)
		return true;
	else
		return false;
}

function SetError ($code)
{
	$this->errorcode = $code;
}
	
function ErrorMsg ()
{
	if (strlen($this->error) > 0)
		return ($this->error);
	else if ($this->errorcode != 0)
		return 'Database error ' . $this->errorcode;
	else
		return '';
}

function ErrorCode ()
{
	return ($this->errorcode);	
}
         
// Execute is intended to be used to execute any database update statement
// (insert, update, delete, or stored procedure call)
function Execute ($sql)
{
	$this->Debug($sql);
	$this->errorcode = 0;
	$result = mysql_query($sql,$this->conn);
	
	if (!$result) { 
		$this->error = mysql_error($this->conn);
		$this->Debug($this->error);
	} else {
		$result = mysql_affected_rows();
	}
	return $result;
}

// Execute is intended to be used to execute a stored procedure
// If an error is returned the vaslued will be saved to $this->error
function ExecuteProc ($sql)
{
	$this->Debug($sql);
	$this->errorcode = 0;
	$result = mysql_query($sql,$this->conn);
	
	if (!$result) { 
		$this->error = mysql_error($this->conn);
		$this->Debug($this->error);
		$this->errorcode = mysql_errno ($this->conn);
	} else {
		$this->errorcode = $this->GetErrorCode();
	}
	return $this->errorcode;
}

// ExecuteKey is like Execute except that it is intended for insertion into tables with an auto_increment only. 
// It will return the value of the autoincrement
function ExecuteKey ($sql)
{
	$this->Debug($sql);
	$this->errorcode = 0;
	$result = mysql_query($sql,$this->conn);
	
	if (!$result) { 
		$this->error = mysql_error($this->conn);
		$this->errorcode = mysql_errno ($this->conn);
	} else {
		$this->errorcode = $this->GetErrorCode();
		if ($this->errorcode == 0) {
			$result = $this->GetLastID();
		} else {
			$result = false;
		}
	}
	return $result;
}

function GetLastID()
{
	$sql = 'select last_insert_id()';
	$result = mysql_query($sql, $this->conn); 
	$row = mysql_fetch_array($result, MYSQL_NUM);
	$this->Debug('ID=' . $row[0]);
	return $row[0];
}

function GetErrorCode()
{
	$sql = 'select @err';
	$result = mysql_query($sql, $this->conn); 
	$row = mysql_fetch_array($result, MYSQL_NUM);
	$this->Debug('Result: ' . $row[0]);
	return $row[0];
}

// Select returns a single field
function Select($sql)
{
	$this->Debug($sql);
	$result = mysql_query($sql, $this->conn); 
	$row = mysql_fetch_array($result, MYSQL_NUM);
	return $row[0];
}

function SelectMysqlVariable ($var)
{
	return (empty($var)) ? '' : $this->Select('select @'.$var);
}

// SelectRow returns all of the records in the row as an array
function SelectRow ($sql,$fieldtype=0)
{
	$this->Debug($sql);
	$result = mysql_query($sql, $this->conn);
    $row = mysql_fetch_array($result, ($fieldtype==1)? MYSQL_NUM : MYSQL_ASSOC);
    return $row;
}


// FillList will echo to the page the selected records as options for a selection box
// The 1st field selected should be the id and the 2nd should be the description
function FillList ($sql, $selected=0)
{
	$this->Debug($sql);
	$result = mysql_query($sql, $this->conn); 
	$row = mysql_fetch_array($result, MYSQL_NUM);
	$cnt = 0;
	while ($row) {
		$id = $row[0];
		$name = $row[1];
		$cnt += 1;
		if ($selected == $id) {
			echo "<OPTION value='$id' selected>$name</OPTION>";
		} else {
			echo "<OPTION value='$id'>$name</OPTION>";
		}
		$row = mysql_fetch_array($result, MYSQL_NUM);
	}
	return $cnt;			
}

// ReturnList does the same thing as FillList except that it returns the list
// as a string insted of echoing to the page.
function ReturnList ($sql, $selected=0)
{
	$this->Debug($sql);
	$result = mysql_query($sql, $this->conn); 
	if (!$result) {
		$this->error = mysql_error($this->conn);
		return (false);
	}
	$row = mysql_fetch_array($result, MYSQL_NUM);
	$cnt = 0;
	$data = '';
	while ($row) {
		$id = $row[0];
		$name = $row[1];
		$cnt += 1;
		if ($selected == $id) {
			$data .= "<OPTION value=$id selected>$name</OPTION>";
		} else {
			$data .= "<OPTION value=$id>$name</OPTION>";
		}
		$row = mysql_fetch_array($result, MYSQL_NUM);
	}
	return $data;			
}

// SelectFirstRow will return all of the fields in a row as an array like SelectRow
// but it will leave the query open so that addition rows can be selected using SelectNextRow
// When thee are no more rows found the FALSE will be returned
function SelectFirstRow($sql,$fieldtype=0)
{
	$this->Debug($sql);
	$this->error = '';
	$this->stmt = mysql_query($sql, $this->conn); 
	if (!$this->stmt) {
		$this->error = mysql_error($this->conn);
	}
	return $this->SelectNextRow ($fieldtype);
}

function SelectNextRow ($fieldtype=0)
{
	if ($this->stmt) {
		$row = mysql_fetch_array($this->stmt, ($fieldtype==1)? MYSQL_NUM : MYSQL_ASSOC);
		if ($row == FALSE) {
			mysql_free_result($this->stmt);
			$this->stmt = FALSE;
		}
		return $row;
	} else
		return false;
}

function SelectAll($sql,$fieldtype=0)
{
	$list = array();
	$row = $this->SelectFirstRow($sql,$fieldtype);
	while ($row) {
		$list[] = $row;
		$row = $this->SelectNextRow($fieldtype);
	}
	return $list;
}

function SelectDelimitedList($sql,$delim)
{
	$result = '';
	$list = array();
	$row = $this->SelectFirstRow($sql,MYSQL_NUM);
	while ($row) {
		if (strlen($result) > 0)
			$result .= $delim;
		$result .= $row[0];
		$row = $this->SelectNextRow($fieldtype);
	}
	return $list;
}

function Export($sql, $delim=',', $type='')
{
	$this->Debug($sql);
	$this->error = '';
	$this->stmt = mysql_query($sql, $this->conn); 
	if (!$this->stmt) {
		$this->error = mysql_error($this->conn);
	}
	$row = mysql_fetch_array($this->stmt, MYSQL_NUM);
	$cnt = sizeof($row);
	$i = 0;
	while ($row) {
		if ($type == 'array') {
			$x = 0;
			while ($x < $cnt) {
				$rows[$i][$x] = trim($row[$x]);
				$x++;
			}
			$i++;
		} 
		else {
			$x = 0;
			while ($x < $cnt) {
				echo $row[$x];
				$x += 1;
				if ($x < $cnt) echo $delim ;
			}
			echo '
';
		}
		$row = mysql_fetch_array($this->stmt, MYSQL_NUM);
	}
	return (isset($rows))?$rows:'';
}

function CloseDB()
{
	mysql_close($this->conn); //close the connection to free the memory
}

////////////////////////////////////////////////////////////////////

function MakeBoolValue ($str)
{
	$str = trim($str);
	return (strlen($str) > 0) ? "'X'" : 'null';
}

function MakeNumericBoolValue ($str)
{
	return ($str > 0) ? '1' : 'null';
}

function MakeStringValue ($str, $allowhtml=false)
{
	$str = get_magic_quotes_gpc() ? stripslashes($str):$str;	
	$str = trim($str);
	$str = mysql_real_escape_string($str);
	if (!$allowhtml)
		$str = strip_tags($str);
	return (strlen($str) > 0) ? "'$str'" : 'null';
}

function RemoveStringQuote($str) {
	$countquote = explode("'",$str);
	if (count($countquote) > 2) {
		$str = substr($str, 1, -1);
	}
	return $str;
}

function MakeDateValue ($date)
{
	if ($date && strlen($date) > 4) {
		$date = str_replace("'","",$date);
		$newdate = date("'Y-m-d'",strtotime($date));
	} else {
		$newdate = 'null';
	}
	return $newdate;
}

function MakeNextDayValue ($date)
{
	if ($date && strlen($date) > 4) {
		$dt = strtotime($date) + 86400;
		$newdate = date("'Y-m-d'",$dt);
	} else {
		$newdate = 'null';
	}
	return $newdate;
}

function MakeTimeValue ($hr,$min,$ampm)
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
