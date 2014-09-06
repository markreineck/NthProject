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
The cookie class provides functions to set, get and clear cookie fields.
It is anticipated that child classes will be derived from this class to implement 
a cookie with the appropriate fields.
*/

class CookieClass extends AlpClass {

function CookieClass ($framework)
{
	parent::__construct($framework);
}

function Debug()
{
	if ($this->Framework()->DebugMode()) {
		echo 'Cookie: ';
		print_r($_COOKIE);
		echo '<br>';
	}
}

function Clear ($var)
{
	setcookie($var, '', 0, '/');
	unset($_COOKIE[$var]);
}

function Set ($var, $val)
{
	setcookie($var, $val, 0, '/');
	$_COOKIE[$var] = $val;
}

function Get ($var, $default='')
{
	return (isset($_COOKIE[$var])) ? $_COOKIE[$var] : $default;
}

}
