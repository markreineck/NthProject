<?php
class showimg extends AlpFramework
{

function Start()
{
	$id = $_GET['id'];
	
	if (!isset($id) || empty($id)) {
		die("Please select your image!");
	} else {
		$row = $this->Model()->SelectRow('SELECT imagetype, contents FROM taskfiles WHERE fileid='.$id);
		$content = $row->contents;
	
		header('Content-type: '.$row->imagetype);
		echo $content;
	}
}
}
?>
