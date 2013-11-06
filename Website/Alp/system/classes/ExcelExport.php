<?php
class ExcelExport {

function ExcelExport()
{
}

function EchoHeader($filename)
{
	header('Expires: 0');
	header('Cache-control: private');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Content-Description: File Transfer');
	header('Content-Type: application/vnd.ms-excel');
	header('Content-disposition: attachment; filename="'.$filename.'"');
}

function EchoRow($row, $delim='')
{
	if (is_array($row)) {
		$cnt = 0;
		foreach ($row as $fld) {
			if ($cnt > 0)
				echo "\t";
			echo $fld;
			$cnt++;
		}
	} else {
		if ($delim)
			$row = str_replace($delim,"\t",$row);
		echo $row;
	}
	echo "\n";
}

function EchoGrid($data, $delim='')
{
	if (is_array($data))
		foreach ($data as $row)
			$this->EchoRow($row, $delim);
}

}
?>