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

class HtmlText {

function CleanSymbols($rawdata)
{
	return str_replace(
		array(
'Á','á','É','é','Í','í','Ñ','ñ','Ó','ó','Ú','ú','Ü','ü','¿','¡',
'€','£','¢','<','>',
'¼','½','¾','°','÷','×','±','²','³'
		),
		array(
'&Aacute;','&aacute;','&Eacute;','&eacute;','&Iacute;','&iacute;','&Ntilde;','&ntilde;','&Oacute;','&oacute;','&Uacute;','&uacute;','&Uuml;','&uuml;','&iquest;','&iexcl;',
'&euro;','&pound;','&cent;','&lt;','&gt;',
'&frac14;','&frac12;','&frac34;','&deg;','&divide;','&times;','&plusmn;','&sup2;','&sup3;'
		),
		$rawdata);
}

// Next, replace their Windows-1252 equivalents.
function CleanWindowsCharacters($rawdata)
{
	return str_replace(
		array(
"\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6",
chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
		array(
"'", "'", '"', '"', '-', '--', '...',
"'", "'", '"', '"', '-', '--', '...'),
		$rawdata);
}

function RemoveBadCharacters($rawdata)
{
	return trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $rawdata));
}

function Clean($rawdata)
{
	return $this->CleanSymbols($this->CleanWindowsCharacters($this->RemoveBadCharacters($rawdata)));
}

}
?>