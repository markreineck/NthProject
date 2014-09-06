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

class AlpClass {

var $framework;

function AlpClass ($framework)
{
	$this->framework = $framework;
}

function Framework()
{
	return $this->framework;
}

function LoadConfig($filename)
{
	include ($this->Framework()->ConfigurationFilePath ($filename));
	return $settings;
}

function DebugMsg($msg)
{
	$this->Framework()->DebugMsg($msg);
}

}
