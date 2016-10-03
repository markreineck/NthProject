<?php
/*
Copyright (c) 2015, Nth Generation. All rights reserved.

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

class JavaScriptClass extends AlpClass {

private function ArrayToQuotedList($src)
{
	$from = '';
	foreach ($src as $s) {
		if ($from)
			$from .= ',';
		$from .= "'".$s."'";
	}
	return $from;
}

function CopyFields($src, $dest)
{
	$from = $this->ArrayToQuotedList($src);
	$to = $this->ArrayToQuotedList($dest);
	return 'CopyFields([' . $from. '],[' . $to . '])';
}

function ShowID($id)
{
	return "ShowTag('$id')";
}

function HideID($id)
{
	return "HideTag('$id')";
}

function JavaScriptBase()
{
	$this->framework->LoadSystemJavascript('Alp');
}

}
?>