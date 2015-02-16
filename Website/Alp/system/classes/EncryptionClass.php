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
The encryption class encapsulates functions to both create a random salt 
and to encrypt a string using the salt. 
*/

class EncryptionClass extends AlpClass {

function CreateSalt()
{
	$random = md5(mt_rand());
	$salt = substr(str_shuffle($random), mt_rand(0,15), 16);
	return $salt;
}

function EncryptString($data, $salt)
{
	return hash('sha256', $data.$salt);
}

}
?>