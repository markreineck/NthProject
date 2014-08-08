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

class AjaxClass {

var $url;
var $function;
var $section;
var $framework;
var $args;
var $fieldlist;
var $debug;
var $async;

function AjaxClass($framework)
{
	$settings = $framework->LoadClassConfig('ajax');
	if ($settings) {
		if (isset($settings['AjaxPage']))
			$this->url = $framework->SiteURL().$settings['AjaxPage'].'/';
//Depricated, use AjaxContainer
		if (isset($settings['AjaxSection']))
			$this->section = $settings['AjaxSection'];
		if (isset($settings['AjaxContainer']))
			$this->section = $settings['AjaxContainer'];
		if (isset($settings['AjaxArgs']))
			$this->args = $settings['AjaxArgs'];
		$this->debug = ($framework->DebugMode) ? true : $settings['DebugMode'];
	} else {
		$this->debug = $framework->DebugMode;
	}
	$this->framework = $framework;
}

function SetPage($pagename)
{
	$this->url = $this->framework->SiteURL().$pagename.'/';
}

function SetContainer($section)
{
	$this->section = $section;
}
// Depricated, use SetContainer
function SetSection($section)
{
	$this->section = $section;
}

function SetFunction($function)
{
	$this->function = $function;
}

function SetAsync($async)
{
	$this->async = $async;
}

function SetFields($fieldlist)
{
	$this->fieldlist = $fieldlist;
}

function AppendArg($list, $var, $val='')
{
	if ($list)
		$list .= '&';
	return $list . $var . '=' . $val;
}

function AddArg($var, $val)
{
	$this->args = $this->AppendArg($this->args, $var, $val);
}

private function DoAjaxFill($func, $sect, $arglist)
{
	$dbg = ($this->debug) ? 'ajaxdebug' : '';
	$async = ($this->async) ? 'true' : 'false';
	return "DoAjaxFill('$func', '$sect', $arglist, $async, '$dbg');";
}

function FillInnerHTML($func, $sect, $args='')
{
	$arglist = $this->args;
	if ($args)
		$arglist = $this->AppendArg($arglist, $args);
	return $this->DoAjaxFill($func, $sect, "'$arglist'");
}

function Query($func, $sect, $args=NULL)
{
	$arglist = $this->args;
	if ($args) {
		if (is_array($args)) {
			foreach ($args as $var => $val) {
				$arglist = $this->AppendArg($arglist, $var, $val);
			}
		} else {
			if ($arglist)
				$arglist .= '&';
			$arglist .= $args;
		}
	}
	return $this->DoAjaxFill($func, $sect, "'$arglist'");
}

function QueryThis($func, $sect, $var)
{
	$arglist = ($this->args) ? "'".$this->args.'&' : "'";
	$arglist .= $var."='+this.value";
	return $this->DoAjaxFill($func, $sect, $arglist);
}

function DefaultQuery($args='')
{
	return $this->FillInnerHTML($this->function, $this->section, $args);
}

private function MakeFieldList($fields)
{
	$args = 'new Array(';
	$size = 0;
	if (is_array($fields)) {
		foreach ($fields as $fld) {
			if ($size > 0)
				$args .= ',';
			$args .= "'$fld'";
			$size++;
		}
	}
	return $args . ')';
}

function FieldQuery($func, $sect, $fields=NULL)
{
	if ($fields && $this->fieldlist)
		$args = $this->MakeFieldList(array_merge($this->fieldlist,$fields));
	else if ($fields)
		$args = $this->MakeFieldList($fields);
	else if ($this->fieldlist)
		$args = $this->MakeFieldList($this->fieldlist);
	else
		$args = "''";

	$dbg = ($this->debug) ? 'ajaxdebug' : '';
	$async = ($this->async) ? 'true' : 'false';
	return "DoAjaxFromFields('$func', '$sect', '$this->args', $args, $async, '$dbg');";
}

function DefaultFieldQuery($fields=NULL)
{
	return $this->FieldQuery($this->function, $this->section, $fields);
}

function AjaxBase()
{
	echo "
<script type=\"text/javascript\">
var ajaxurl='$this->url';
</script>
";
	$this->framework->LoadSystemJavascript('Ajax');
	if ($this->debug > 0) {
	echo '
<br clear="all">
<div class="debug" id="ajaxdebug">AJAX Debug:</div>
';
	}
}

}
?>