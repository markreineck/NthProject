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

class JsonClass {

var $url;
//var $function;
//var $section;
var $framework;
//var $args;
//var $fieldlist;
var $debug;
var $async;

function JsonClass($framework)
{
	$settings = $framework->LoadClassConfig('json');
	if ($settings) {
		if (isset($settings['JsonPage']))
			$this->url = $framework->SiteURL().$settings['JsonPage'].'/';
		$this->debug = ($framework->DebugMode) ? true : $settings['DebugMode'];
	}
	$this->framework = $framework;
}

function SetAsync($async)
{
	$this->async = $async;
}

function JsonBase()
{
	echo "
<script type=\"text/javascript\">
var jsonurl='$this->url';
var jsondbg='$this->debug';
var jsonasync=";
	echo ($this->async) ? 'true' : 'false';
	echo ";
</script>
";
	$this->framework->LoadSystemJavascript('json');
	if ($this->debug > 0) {
		echo '
<br clear="all">
<div class="debug" id="jsondebug">JSON Debug:</div>
';
	}
}

}
?>