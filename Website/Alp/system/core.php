<?php 
/*
Copyright (c) 2012-2015 Nth Generation. All rights reserved.

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

define('DEBUG_SQL', 1);
define('DEBUG_AJAX', 2);
define('DEBUG_CLASSES', 4);
define('DEBUG_INPUT', 8);
define('DEBUG_INCLUDE', 16);

interface AlpController
{
    public function Start();
}

abstract class AlpFramework implements AlpController {

var $URLBase;
var $DatabaseDriver;
var $DebugMode;
var $DebugMask;
var $DateFormat;

var $DatabaseLoaded = 0;
var $ModelList = array();
var $TableList = array();
var $LoadedClassList = array();
var $DataList = array();
var $FormClass = array();
var $DateClass = NULL;
var $AjaxClass = NULL;
var $CookieClass = NULL;

var $CSSFiles;
var $CSSPath;
var $cssloaded=false;

var $controllerlist;
var $UserSettings;
var $TermList = NULL, $DefaultTermList = NULL;

var $ModelPath = 'model';
var $SystemPath = 'system';
var $ConfigPath = 'config';
var $ViewPath = 'views';

var $DeviceType = '';
var $ActionList = array();
var $ErrorList = array();
var $MsgList = array();
var $FormBindings = array();

/**********************************************************************
 *	Class Initialize
 **********************************************************************/
function __construct ($controller)
{
	$this->controllerlist = $controller;

	ob_start();

	include ('Alp/config/globals.php');
	if ($this->DebugMode > 0) {
		set_exception_handler('exception_handler');
		set_error_handler("error_handler");

		$this->DebugPrint($_POST, DEBUG_INPUT, '_POST data:');
		$this->DebugPrint($_GET, DEBUG_INPUT, '_GET data:');
		$this->DebugPrint($_COOKIE, DEBUG_INPUT, 'Cookie:');
	}
}

function __destruct ()
{
	ob_end_flush();
}

function BindForms ($bind)
{
	$this->FormBindings = array_merge($this->FormBindings, $bind);
}

function Launch()
{
	if (count($_POST)) {
		if (isset($this->FormBindings)) {
			foreach ($this->FormBindings as $fld => $method) {
				if (isset($_POST[$fld]) && method_exists($this, $method)) {
					$this->$method();
					return;
				}
			}
		}

		if (method_exists($this, 'Post')) {
			$this->Post();
			return;
		}
	}
	$this->Start();
}

/**********************************************************************
 *	Error Messages
 **********************************************************************/

function LogError($msg)
{
	$this->ErrorList[] = $msg;
}

function ErrorString()
{
	$msg = '';
	foreach ($this->ErrorList as $e) {
		if ($msg)
			$msg .= '<br>';
		$msg .= $e;
	}
	return $msg;
}

function LogMsg($msg)
{
	$this->MsgList[] = $msg;
}

function MsgString()
{
	$msg = '';
	foreach ($this->MsgList as $e) {
		if ($msg)
			$msg .= '<br>';
		$msg .= $e;
	}
	return $msg;
}

function ShowMessages()
{
	$err = $this->ErrorString();
	$donemsg = $this->MsgString();

	if (empty($err))
		echo "<div class=\"$this->ErrorClass\" id=\"ErrorBlock\" style=\"display:none\"></div>
";
	else
		echo "<div class=\"$this->ErrorClass\" id=\"ErrorBlock\">$err</div>
";
	if (!empty($donemsg))
		echo "<div class=\"$this->MessageClass\" id=\"MessageBlock\">$donemsg</div>
";
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
	$path = $this->ConfigurationFilePath ($filename);
	$this->DebugMsg('Include: ' . $path, DEBUG_INCLUDE);
	include_once ($path);
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
 *	Language Files
 **********************************************************************/
private function LanguageFilePath ($filename)
{
	return $this->FrameworkFilePath('language',$filename);
}

function GetText ($key)
{
	if (!$this->TermList) {
		include $this->LanguageFilePath('en');
		$this->TermList = $termlist;
	}
	if (isset($this->TermList[$key]))
		return $this->TermList[$key];
	return '';
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
		$this->IncludePhpFile ($path);
	}
}

function LoadLibrary ($libfile)
{
	$path = $this->FrameworkFilePath('libraries',$libfile);
	if (is_file($path)) {
		$this->IncludePhpFile ($path);
	} else {
		$this->IncludePhpFile ($this->FrameworkFilePath($this->SystemPath.'/libraries',$libfile));
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
 
private function IncludePhpFile($path)
{
	$this->DebugMsg('Include: ' . $path, DEBUG_INCLUDE);
	include_once ($path);
}

private function IncludeBaseClass()
{
	$path = $this->FrameworkFilePath($this->SystemPath,'AlpClass');
	$this->IncludePhpFile($path);
}

private function IncludeSystemClass($name)
{
	$this->IncludeBaseClass();
	$path = $this->FrameworkFilePath($this->SystemPath.'/classes',$name);
	if (is_file($path))
		$this->IncludePhpFile($path);
}

private function LoadSystemClass($name)
{
	$this->IncludeSystemClass($name);
	$this->DebugMsg('Class: ' . $name, DEBUG_CLASSES);
	return new $name($this);
}

function Cookie($classname='')
{
	if (!$this->CookieClass) {
		if (empty($classname)) {
			$this->CookieClass = $this->LoadSystemClass('CookieClass');
		} else {
			$this->IncludeBaseClass();
			$this->IncludeSystemClass('CookieClass');
			$this->IncludePhpFile($this->FrameworkFilePath('classes',$classname));
			$this->DebugMsg('Cookie: ' . $classname, DEBUG_CLASSES);
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
	$this->IncludePhpFile($path);
}

function Forms($classname='')
{
    if (!count($this->FormClass)) {
		if (empty($classname)) {
            $this->FormClassName = 'FormClass';
            $this->FormClass[] = $this->LoadSystemClass('FormClass');
		} else {
            $this->FormClassName = $classname;
			$this->IncludeSystemClass('FormClass');
			$this->IncludePhpFile($this->FrameworkFilePath('classes',$classname));
			$this->DebugMsg('Form: ' . $classname, DEBUG_CLASSES);
			$this->FormClass[] = new $classname($this);
		}
	} else
		$this->DebugMsg('Fetch Form: ' . get_class($this->FormClass[0]), DEBUG_CLASSES);
	return $this->FormClass[0];
}

function MakeForm($classname)
{
    if (!$this->FormClass) {
        $this->IncludeSystemClass('FormClass');
    }
    if (!$this->FormClass || !isset($this->FormClass[$classname])) {
		$this->DebugMsg('Form: ' . $classname, DEBUG_CLASSES);
        $this->FormClass[$classname] = new $this->FormClassName($this);
    }
	$this->DebugMsg('Fetch Form: ' . get_class($this->FormClass[$classname]), DEBUG_CLASSES);
    return $this->FormClass[$classname];
}

function GetForm()
{
	return $this->FormClass[0];
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
		$this->IncludePhpFile ($this->FrameworkFilePath($this->SystemPath,'model/DBField'));
		$this->IncludePhpFile ($this->FrameworkFilePath($this->SystemPath,'model/DBProcedure'));
		$this->IncludePhpFile ($this->FrameworkFilePath($this->SystemPath,'model/DBTable'));
	}

	if (is_array($modelname)) {
		foreach ($modelname as $mname) {
			$this->IncludePhpFile ($this->FrameworkFilePath($this->ModelPath,$mname));
			$classname = $mname;
		}
	} else {
		if ($modelname) {
			$this->IncludePhpFile ($this->FrameworkFilePath($this->ModelPath,$modelname));
			$classname = $modelname;
		}
	}

	if (!empty($classname)) {
		if (!$myclassname)
			$myclassname = $classname;
		$this->DebugMsg('DBTable: ' . $classname, DEBUG_CLASSES);
		return $this->TableList[$myclassname] = new $myclassname($this);
	} else
		return FALSE;
}

function DBForm($binding, $classname='')
{
	if (!$this->FormClass) {
		$this->IncludeSystemClass('FormClass');
		$path = $this->FrameworkFilePath($this->SystemPath,'model/DBField');
		$this->IncludePhpFile ($path);
		if (empty($classname)) {
			$this->IncludeSystemClass('DBFormClass');
			$this->DebugMsg('DBForm: DBFormClass', DEBUG_CLASSES);
			$this->FormClass = new DBFormClass($this, $binding);
		} else {
			$this->IncludeSystemClass('DBFormClass');
			$this->IncludePhpFile ($this->FrameworkFilePath('classes',$classname));
			$this->DebugMsg('DBForm: ' . $classname, DEBUG_CLASSES);
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
		$this->DebugMsg('Class: ' . $classname, DEBUG_CLASSES);
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
	$this->IncludePhpFile($this->FrameworkFilePath($this->ModelPath.'/binding',$filename));
	return $bindings;
}

function LoadModel ($modelname='', $dbindex=0, $pwd='', $username='', $dbname='', $host='')
{
	$classname = '';

	if (!$this->DatabaseLoaded) {
		$this->IncludePhpFile ($this->FrameworkFilePath($this->SystemPath.'/database',$this->DatabaseDriver));
		$this->DatabaseLoaded = 1;
	}

	if (is_array($modelname)) {
		foreach ($modelname as $mname) {
			$this->IncludePhpFile ($this->FrameworkFilePath($this->ModelPath,$mname));
			$classname = $mname;
		}
	} else {
		if (empty($modelname)) {
			$classname = 'DatabaseClass';
		} else {
			$this->IncludePhpFile ($this->FrameworkFilePath($this->ModelPath,$modelname));
			$classname = $modelname;
		}
	}

	if (!empty($classname)) {
		$this->DebugMsg('Model: ' . $classname, DEBUG_CLASSES);
		$db = new $classname($this, $pwd, $username, $dbname, $host);
		$this->ModelList[$dbindex] = $db;
		return $this->ModelList[$dbindex];
	} else
		return FALSE;
}

// Depricated. Please use Model()
function Database ($dbindex=0)
{
	return $this->Model($dbindex);
}

function Model ($dbindex=0)
{
	if (!isset($this->ModelList[$dbindex]) || !$this->ModelList[$dbindex])
		$this->LoadModel('',$dbindex); 
	$this->DebugMsg('Fetch Model: ' . get_class($this->ModelList[$dbindex]), DEBUG_CLASSES);
	return $this->ModelList[$dbindex];
}

/**********************************************************************
 *	Class Variables
 **********************************************************************/
function Controller($idx=0)
{
	return (isset($this->controllerlist[$idx])) ? $this->controllerlist[$idx] : '';
}

function DebugPrint($data, $mask=0, $desc='')
{
	if (!$mask || !$this->DebugMask || $mask & $this->DebugMask) {
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
}

function DebugMsg($msg, $mask=0)
{
	if (!$mask || !$this->DebugMask || $mask & $this->DebugMask) {
		if ($this->DebugMode == 1)
			echo '<!-- ';
		if ($this->DebugMode > 0)
			echo $msg;
		if ($this->DebugMode == 1)
			echo ' -->';
		else if ($this->DebugMode > 1)
			echo '<br>';
	}
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
			$this->DebugMsg('Class: Mobile_Detect', DEBUG_CLASSES);
			$md = new Mobile_Detect();
			if ($md->isMobile() && !$md->isTablet())
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
	$this->DebugMsg("$var=[$result]", DEBUG_INPUT);
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