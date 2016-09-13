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

class ReportClass extends AlpClass {

function ReportClass ($framework)
{
	parent::__construct($framework);

}

private function RenderLabel($title, $tag='', $val='')
{
	if (isset($title->label)) {
		if ($tag)
			echo"<$tag>";
		echo ($val) ? $val : $title->label;
		if ($tag)
			echo"</$tag>";
	}
}
public function Render($file, $args)
{
	$path = 'Alp/reports/'.$file.'.xml';
	$xml = file_get_contents($path);
	$xml = simplexml_load_string($xml);
	$sql = '';
	$error = false;

	if (!isset($xml->query)) {
		echo 'Missing query<br>';
		$error = true;
	} else if (!isset($xml->query->table) || !$xml->query->table) {
		echo 'Missing query table<br>';
		$error = true;
	}
	if (!isset($xml->fields)) {
		echo 'Missing field list<br>';
		$error = true;
	} else if (!isset($xml->fields->field) || count($xml->fields->field) < 1) {
		echo 'Empty field list<br>';
		$error = true;
	}

	if ($error)
		return;

	if (isset($xml->title)) {
		$this->RenderLabel($xml->title, 'h1');
	}

	echo '<table cellpadding="3"><tr>';
	$x = 0;
	$summary = array();

	foreach($xml->fields->field as $fld) {
		if (!isset($xml->fields->field[$x]->summary))
			$xml->fields->field[$x]->summary = '';
		$summary[$x] = 0;

		$this->RenderLabel($fld, 'th');
		if ($sql)
			$sql .= ', ';
		$sql .= $fld->table . '.' . $fld->dbfield;
		$x++;
	}
	echo '</tr>';

	$sql = 'select ' . $sql . ' from ' . $xml->query->table;
	if (isset($xml->query->where) && $xml->query->where) {
		$from = array('lessthan','greaterthan');
		$to = array('<','>');
		foreach ($args as $val=>$var) {
			$from[] = '[' . $val . ']';
			$to[] = $var;
		}

		$sql .= ' where ' . str_replace($from, $to, $xml->query->where);
	}

	if (isset($xml->query->orderby) && $xml->query->orderby)
		$sql .= ' order by ' . $xml->query->orderby;
//$sql .= ' limit 10';

//$this->Framework()->Model()->DebugMode(2);
	$data = $this->Framework()->Model()->SelectAll($sql);

	foreach($data as $row) {
		echo '<tr>';
		$x = 0;
		foreach($xml->fields->field as $fld) {
			$idx = $fld->dbfield;
			$val = ($row->$idx) ? $row->$idx : ' ';
			$this->RenderLabel($fld, 'td', $val);

			switch($fld->summary) {
				case 'count':
					$summary[$x]++;
					break;
				case 'sum':
					$summary[$x] += $val;
					break;
			}
			$x++;
		}
		echo '</tr>';
	}

	echo '<tr>';
	$x = 0;
	foreach($xml->fields->field as $fld) {
		if ($xml->fields->field[$x]->summary) {
			$this->RenderLabel($fld, 'td', ($summary[$x] > 0) ? $summary[$x] : ' ');
		}
		$x++;
	}
	echo '</tr>';

	echo '</table>';
}

}
