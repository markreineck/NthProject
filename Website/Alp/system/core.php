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
interface AlpController
{
    public function Start();
}

abstract class AlpFramework implements AlpController {

var $URLBase;
var $DatabaseDriver;
var $DebugMode;
var $DateFormat;

var $DatabaseLoaded = 0;
var $ModelList = array();
var $TableList = array();
var $LoadedClassList = array();
var $DataList = array();
//var $PostData = array();
//var $GetData = array();
var $FormClass = NULL;
var $DateClass = NULL;
var $AjaxClass = NULL;
var $CookieClass = NULL;

var $CSSFiles;
var $CSSPath;
var $cssloaded=false;

var $controllerlist;
var $UserSettings;

var $ModelPath = 'model';
var $SystemPath = 'system';
var $ConfigPath = 'config';
var $ViewPath = 'views';

var $DeviceType = '';
var $ActionList = array();

/**********************************************************************
 *	Class Initialize
 **********************************************************************/
function AlpFramework($controller)
{
	/*
	foreach ($_POST as $var => $val) {
		$this->PostData[$var] = $val;
	}
	foreach ($_GET as $var => $val) {
		$this->GetData[$var] = $val;
	}
*/
	$this->controllerlist = $controller;

	include ('Alp/config/globals.php');
	if ($this->DebugMode > 0) {
		set_exception_handler('exception_handler');
		set_error_handler("error_handler");
		if ($this->DebugMode > 2) {
			if (count($_POST) > 0) {
				echo '<p>Post:';
				print_r($_POST);
				echo '</p>';
			}
			if (count($_GET) > 0) {
				echo '<p>Get:';
				print_r($_GET);
				echo '</p>';
			}
			echo '<p>Cookie:';
			print_r($_COOKIE);
			echo '</p>';
		}
	}
/*
	if (isset($this->ControllerFile) && !empty($this->ControllerFile))
		include('Alp/controllers/'.$this->ControllerFile.'.php');
	else
		include('Alp/controllers/'.$this->controllerlist[0].'.php');
*/
}

function Process()
{
	if (count($_POST) && method_exists($controller, 'Post'))
		$controller->Post();
	else
		$controller->Start();
}
/*
function PostData($var, $val)
{
	$this->PostData[$var] = $val;
}

/**********************************************************************
 *	Path Construction Utilities
 **********************************************************************/
function SiteURL()
{
	return $this->URLBase;
}

private function FrameworkFilePath ($folder, $filename)
{
	$path = 'Alp/'.$folder.'/'.$filename.'.php';
if ($this->DebugMode > 2)
echo "<p>$path</p>";
	return $path;
}

function ConfigurationFilePath ($filename)
{
	return $this->FrameworkFilePath($this->ConfigPath,$filename);
}

//Depricated
function LoadClassConfig($filename)
{
	include ($this->ConfigurationFilePath ($filename));
	return $settings;
}

function RedirectTo($page='')
{
	if (!strpos($page, '://', 0) > 0)
		$page = $this->URLBase . $page;
	if ($this->DebugMode > 1)
		echo '<a href="'.$page.'">Continue</a>';
	else
		header("Location: ".$page);
	exit();
}

function RedirectPost($url, $vars)
{
	echo "<form name=\"Redirect\" method=\"post\" action=\"$url\">";
	foreach ($vars as $var => $val) {
		echo "<input type=\"hidden\" name=\"$var\" value=\"$val\">";
	}
	echo '</form>
<script language="JavaScript">
window.onload = function(){
  document.forms["Redirect"].submit()
}
</script>
';
	exit();
}

/**********************************************************************
 *	CSS Files
 **********************************************************************/
function LoadCSSFile ($filename)
{
	echo '<link href="'.$this->URLBase.$this->CSSPath.$filename.'.css" rel="stylesheet" type="text/css" />
';
}

function LoadCSS ($csslist='')
{
	if (!$this->cssloaded) {
		foreach ($this->CSSFiles as $css) {
			$this->LoadCSSFile($css);
		}
		$this->cssloaded = true;
	}
	if (!empty($csslist)) {
		if (is_array($csslist)) {
			foreach ($csslist as $css) {
				$this->LoadCSSFile($css);
			}
		} else {
			$this->LoadCSSFile($csslist);
		}
	}
}

/**********************************************************************
 *	Javascript Files
 **********************************************************************/
function LoadJavascript ($jslist)
{
}

private function LoadSystemJavascriptFile ($js)
{
	echo '
<script src="/Alp/' . $this->SystemPath . '/javascript/' . $js . '.js" type="text/javascript"></script>
';
}

function LoadSystemJavascript ($jslist)
{
	if (is_array($jslist)) {
		foreach ($jslist as $js) {
			$this->LoadSystemJavascriptFile($js);
		}
	} else {
		$this->LoadSystemJavascriptFile($jslist);
	}
}

/**********************************************************************
 *	Library Loading
 **********************************************************************/
function LoadSystemLibrary($libfile)
{
	$path = $this->FrameworkFilePath($this->SystemPath.'/libraries',$libfile);
	if (is_file($path)) {
		include ($path);
	}
}

function LoadLibrary ($libfile)
{
	$path = $this->FrameworkFilePath('libraries',$libfile);
	if (is_file($path)) {
		include_once ($path);
	} else {
		include_once ($this->FrameworkFilePath($this->SystemPath.'/libraries',$libfile));
	}
}

/**********************************************************************
 *	View Loading
 **********************************************************************/
function LoadView ($viewname='')
{
	if (empty($viewname))
		$viewname = $this->Controller();
	extract($this->DataList);
	try {
		if ($this->DebugMode > 2)
			echo 'Load View: '.$this->FrameworkFilePath($this->ViewPath,$viewname).'<br>';
		require ($this->FrameworkFilePath($this->ViewPath,$viewname));
	} catch (Exception $ex) {
		echo 'Exception: ' . $ex->getMessage();
		exit;
	}
}

/**********************************************************************
 *	Class Loading
 **********************************************************************/

private function IncludeBaseClass()
{
	$path = $this->FrameworkFilePath($this->SystemPath,'AlpClass');
	include_once ($path);
}

private function IncludeSystemClass($name)
{
	$this->IncludeBaseClass();
	$path = $this->FrameworkFilePath($this->SystemPath.'/classes',$name);
	if (is_file($path)) {
		include ($path);
	}
}

private function LoadSystemClass($name)
{
	$this->IncludeSystemClass($name);
	return new $name($this);
}

function Cookie($classname='')
{
	if (!$this->CookieClass) {
		if (empty($classname)) {
			$this->CookieClass = $this->LoadSystemClass('CookieClass');
		} else {
			$this->IncludeSystemClass('CookieClass');
			include ($this->FrameworkFilePath('classes',$classname));
			$this->CookieClass = new $classname($this);
		}
	}
	return $this->CookieClass;
}

function Dates()
{
	if (!$this->DateClass)
		$this->DateClass = $this->LoadSystemClass('DateClass');
	return $this->DateClass;
}

function Ajax()
{
	if (!$this->AjaxClass)
		$this->AjaxClass = $this->LoadSystemClass('AjaxClass');
	return $this->AjaxClass;
}

private function IncludeClassFile($libfile)
{
	$this->IncludeBaseClass();
	$path = $this->FrameworkFilePath($this->SystemPath.'/classes',$libfile);
	if (!is_file($path)) {
		$path = $this->FrameworkFilePath('classes',$libfile);
	}
	include ($path);
}

function Forms($classname='')
{
	if (!$this->FormClass) {
		if (empty($classname)) {
			$this->FormClass = $this->LoadSystemClass('FormClass');
		} else {
			$this->IncludeSystemClass('FormClass');
			include ($this->FrameworkFilePath('classes',$classname));
			$this->FormClass = new $classname($this);
		}
	}
	return $this->FormClass;
}

function GetForm()
{
	return $this->FormClass;
}

function DBTable($modelname='', $myclassname='')
{
	if (count($this->TableList)) {
		// We already have at least one DB table loaded so see if the one we are looking for is already loaded
		if (!$modelname) {
			reset($this->TableList);
			return current($this->TableList);
		}
		if ($modelname) {
			if (isset($this->TableList[$modelname]))
				return current($this->TableList[$modelname]);
		}
	} else {
		// If we have not loaded any DB tables yet then load the necessary classes
		include ($this->FrameworkFilePath($this->SystemPath,'model/DBField'));
		include ($this->FrameworkFilePath($this->SystemPath,'model/DBProcedure'));
		include ($this->FrameworkFilePath($this->SystemPath,'model/DBTable'));
	}

	if (is_array($modelname)) {
		foreach ($modelname as $mname) {
			include ($this->FrameworkFilePath($this->ModelPath,$mname));
			$classname = $mname;
		}
	} else {
		if ($modelname) {
			include ($this->FrameworkFilePath($this->ModelPath,$modelname));
			$classname = $modelname;
		}
	}

	if (!empty($classname)) {
		if (!$myclassname)
			$myclassname = $classname;
		return $this->TableList[$myclassname] = new $myclassname($this);
	} else
		return FALSE;
}

function DBForm($binding, $classname='')
{
	if (!$this->FormClass) {
		$this->IncludeSystemClass('FormClass');
		$path = $this->FrameworkFilePath($this->SystemPath,'model/DBField');
		include ($path);
		if (empty($classname)) {
			$this->IncludeSystemClass('DBFormClass');
			$this->FormClass = new DBFormClass($this, $binding);
		} else {
			$this->IncludeSystemClass('DBFormClass');
			include ($this->FrameworkFilePath('classes',$classname));
			$this->FormClass = new $classname($this, $binding);
		}
	}
	return $this->FormClass;
}

function LoadClass ($libfile, $classname='', $libidx='')
{
	if (empty($classname))
		$classname = (is_array($libfile)) ? end($libfile) : $libfile;
	if (empty($libidx))
		$libidx = $classname;

	if (isset($this->LoadedClassList[$libidx])) {
		$lib = $this->LoadedClassList[$libidx];
	} else {
		if (is_array($libfile)) {
			foreach ($libfile as $lib) {
				$this->IncludeClassFile($lib);
			}
		} else {
				$this->IncludeClassFile($libfile);
		}
/*
		$path = $this->FrameworkFilePath($this->SystemPath.'/classes',$libfile);
		if (is_file($path)) {
			include ($path);
		} else {
			include ($this->FrameworkFilePath('classes',$libfile));
			$lib = new $classname();
		}
*/
		$lib = new $classname($this);
		$this->LoadedClassList[$libidx] = $lib;
	}
	return $lib;
}

/**********************************************************************
 *	Model Loading
 **********************************************************************/
function LoadDatabaseConfig()
{
	return $this->LoadClassConfig('database');
}

function LoadBindingConfig ($filename)
{
	include $this->FrameworkFilePath($this->ModelPath.'/binding',$filename);
	return $bindings;
}

function LoadModel ($modelname='', $dbindex=0, $pwd='', $username='', $dbname='', $host='')
{
	$classname = '';

	if (!$this->DatabaseLoaded) {
		include ($this->FrameworkFilePath($this->SystemPath.'/database',$this->DatabaseDriver));
		$this->DatabaseLoaded = 1;
	}

	if (is_array($modelname)) {
		foreach ($modelname as $mname) {
			include ($this->FrameworkFilePath($this->ModelPath,$mname));
			$classname = $mname;
		}
	} else {
		if (empty($modelname)) {
			$classname = 'DatabaseClass';
		} else {
			include ($this->FrameworkFilePath($this->ModelPath,$modelname));
			$classname = $modelname;
		}
	}

	if (!empty($classname)) {
		$db = new $classname($this, $pwd, $username, $dbname, $host);
		$this->ModelList[$dbindex] = $db;
		return $this->ModelList[$dbindex];
	} else
		return FALSE;
}

function Database ($dbindex=0)
{
	if (!isset($this->ModelList[$dbindex]) || !$this->ModelList[$dbindex])
		$this->LoadModel('',$dbindex); 
	return $this->ModelList[$dbindex];
}

function Model ($dbindex=0)
{
	if (!isset($this->ModelList[$dbindex]) || !$this->ModelList[$dbindex])
		$this->LoadModel('',$dbindex); 
	return $this->ModelList[$dbindex];
}

/**********************************************************************
 *	Class Variables
 **********************************************************************/
function Controller($idx=0)
{
	return (isset($this->controllerlist[$idx])) ? $this->controllerlist[$idx] : '';
}

function DebugPrint($data, $desc='')
{
	if ($this->DebugMode == 1)
		echo '<!-- ';
	if ($this->DebugMode > 0) {
		if ($desc)
			echo $desc . ': ';
		print_r($data);
	}
	if ($this->DebugMode == 1)
		echo ' -->';
	else if ($this->DebugMode > 1)
		echo '<br>';
}

function DebugMsg($msg)
{
	if ($this->DebugMode == 1)
		echo '<!-- ';
	if ($this->DebugMode > 0)
		echo $msg;
	if ($this->DebugMode == 1)
		echo ' -->';
	else if ($this->DebugMode > 1)
		echo '<br>';
}

function DebugMode($mode=-1)
{
	if ($mode >= 0)
		$this->DebugMode = $mode;
	return $this->DebugMode;
}

/**********************************************************************
 *	User Variables
 **********************************************************************/
function PutData ($var, $val)
{
	$this->DataList[$var] = $val;
}

private function FieldHasChanged($name)
{
	$oldidx = $name.'Old';
	$newval = (isset($_POST[$name])) ? $_POST[$name] : '';
	$oldval = (isset($_POST[$oldidx])) ? $_POST[$oldidx] : '';
	return ($newval != $oldval);
}

function DataChanged($var)
{
	$cng = false;
	if (is_array($var)) {
		foreach ($var as $v) {
			if ($this->FieldHasChanged($v))
				$cng = true;
		}
	} else {
		if ($this->FieldHasChanged($var))
			$cng = true;
	}
	return $cng;
}

/*
 * $_POST data
function PostedVar ($varname)
{
	// There should be an option for cleaned or uncleaned
	return (isset($_POST[$varname])) ? $_POST[$varname] : '';
}

function PostVarList()
{
	// Return a list of the names of the posted variables
	$posted = array();
	foreach ($_POST as $var => $val) {
// Need to clean the data here
		$posted[$var] = $val;
	}
	return $posted;
}

function GetVar ($varname)
{
	// There should be an option for cleaned or uncleaned
	return (isset($_GET[$varname])) ? $_GET[$varname] : '';
}
*/


function DateFormat ()
{
	return $this->DateFormat;
}

function UserSetting($setting)
{
	return (isset($this->UserSettings[$setting])) ? $this->UserSettings[$setting] : false;
}

/**********************************************************************
 *	Device Type
 **********************************************************************/
function DeviceType()
{
	if (!$this->DeviceType) {
		if (isset($_GET['DeviceType'])) {
			$this->DeviceType = $_GET['DeviceType'];
		} else {
			$this->LoadLibrary('Mobile_Detect');
			$md = new Mobile_Detect();
			if ($md->isMobile())
				$this->DeviceType = 'P';
			else if ($md->isTablet())
				$this->DeviceType = 'T';
			else
				$this->DeviceType = 'C';
		}
	}
	return $this->DeviceType;
}

function isPhone()
{
	return $this->DeviceType() == 'P';
}

function isTablet()
{
	return $this->DeviceType() == 'T';
}

function isComputer()
{
	return $this->DeviceType() == 'C';
}

/**********************************************************************
 *	Input Data
 **********************************************************************/

function FilteredInputData($method, $var, $filter, $options=0)
{
	$result = trim(filter_input($method, $var, $filter, $options));
	$this->DebugMsg("$var=[$result]");
	return $result;
}

function IsPosted($var)
{
	return isset($_POST[$var]);
}

function IsGet($var)
{
	return isset($_GET[$var]);
}

function PostedData($var)
{
	return $this->FilteredInputData(INPUT_POST, $var, FILTER_UNSAFE_RAW);
}

function PostedNumber($var)
{
	return $this->FilteredInputData(INPUT_POST, $var, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
}

function PostedDigit($var)
{
	return str_replace(array('+','-'),'',$this->FilteredInputData(INPUT_POST, $var, FILTER_SANITIZE_NUMBER_INT));
}

function PostedString($var)
{
	return $this->FilteredInputData(INPUT_POST, $var, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_ENCODE_HIGH);
}

function PostedHtml($var)
{
	return $this->FilteredInputData(INPUT_POST, $var, FILTER_UNSAFE_RAW, FILTER_FLAG_ENCODE_LOW+FILTER_FLAG_ENCODE_HIGH+FILTER_FLAG_ENCODE_AMP);
}

function GetData($var)
{
	return $this->FilteredInputData(INPUT_GET, $var, FILTER_UNSAFE_RAW);
}

function GetNumber($var)
{
	return $this->FilteredInputData(INPUT_GET, $var, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
}

function GetDigit($var)
{
	return str_replace(array('+','-'),'',$this->FilteredInputData(INPUT_GET, $var, FILTER_SANITIZE_NUMBER_INT));
}

function GetString($var)
{
	return $this->FilteredInputData(INPUT_GET, $var, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_ENCODE_HIGH);
}

function RequestData($var)
{
	return $this->FilteredInputData(INPUT_REQUEST, $var, FILTER_UNSAFE_RAW);
}

function RequestNumber($var)
{
	return $this->FilteredInputData(INPUT_REQUEST, $var, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
}

function RequestDigit($var)
{
	return str_replace(array('+','-'),'',$this->FilteredInputData(INPUT_REQUEST, $var, FILTER_SANITIZE_NUMBER_INT));
}

function RequestString($var)
{
	return $this->FilteredInputData(INPUT_REQUEST, $var, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_ENCODE_HIGH);
}

/**********************************************************************
 *	User Variables
 **********************************************************************/

static function ShowErrorMessage($msg, $title='Framework Error')
{
	echo "<br clear=\"all\"><p style=\"padding: 5px; color: #C00; border: 1px solid #333; background-color:#CCC\">
<strong>$title</strong>
<br>$msg</p>";
}

function Start()
{
	$this->ShowErrorMessage('No Start function has been implemented.');
}

}

function exception_handler($exception)
{
	AlpFramework::ShowErrorMessage($exception->getMessage(), 'PHP Exception');
}

function error_handler($errno, $errstr, $errfile, $errline)
{
	if ($errno != E_STRICT)
		AlpFramework::ShowErrorMessage("Error number $errno: $errstr<br>Line $errline of $errfile", 'PHP Error');
}
?>