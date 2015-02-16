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

class WebServiceClass extends AlpClass {

var $url;

function WebServiceClass($framework)
{
	parent::__construct($framework);
	$settings = $this->LoadConfig('webservice');
	if (isset($settings['URL'])) {
		$this->url = $settings['URL'];
	} else {
		$this->url = '';
	}
}

function MakeURL($service='')
{
	return $this->url . '/' . $service;
}

function Query($service='')
{
$url = $this->MakeURL($service);

$ch = curl_init( $url );

// Configuring curl options
$options = array(
CURLOPT_RETURNTRANSFER => true,
//CURLOPT_USERPWD	=> $username . ":" . $password,  // authentication
//CURLOPT_POSTFIELDS => $json_string,
CURLOPT_HTTPHEADER => array('Content-type: application/json')
);

// Setting curl options
curl_setopt_array( $ch, $options );

// Getting results
$jsonObject = curl_exec($ch); // Getting jSON result string

return json_decode($jsonObject);

}

}
?>