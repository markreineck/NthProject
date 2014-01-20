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
@session_start();

$version = phpversion();

$v = explode('.',$version);
if ($v[0] < 5) {
	echo "PHP version 5.0 or greater is required. You are currently using version $version<br>";
	exit;
}

require_once('Alp/system/core.php');

// Load and execute the framework initialization
$pagestr = (empty($_GET['p'])) ? 'index' : $_GET['p'];
$page = explode('/',$pagestr);
if ($page[0] == 'test:') {
	// Run test cases
	require_once('Alp/system/testcontroller.php');
	$pagestr = substr($pagestr,6);
	require_once('Alp/test/' . $pagestr . '.php');
	$classname = end($page);
	$testcase = new $classname($this);
	$controller = new TestController($testcase);
} else {
	// Run a live controller
	require_once('Alp/controllers/' . $page[0] . '.php');
	$controller = new $page[0]($page);
	if (count($_POST) && method_exists($controller, 'Post'))
		$controller->Post();
	else
		$controller->Start();
}

?>