<?php
class FileUpload {
var $errormsg;
var $filetypes;
var $filelocation;

function FileUpload($framework)
{
	$settings = $framework->LoadClassConfig('fileupload');
	if ($settings) {
		if (isset($settings['FileTypes']))
			$this->filetypes = $settings['FileTypes'];
		if (isset($settings['FileLocation']))
			$this->filelocation = $settings['FileLocation'];
	}
	$this->framework = $framework;
}

function ErrorMsg()
{
	return $this->errormsg;
}

function UploadFile($targetname, $tempname, $targetloc='')
{
	$this->errormsg = '';
	$resultfile = '';
	if ($targetname) {
		if (!$targetloc)
			$targetloc = $this->filelocation;

		$targetPath	= $targetloc . $targetname;
		$allowedFiletypes = $this->filetypes;
		$ext = substr($targetname, strpos($targetname,'.'), strlen($targetname)-1);
		if (!in_array(strtolower($ext),$allowedFiletypes)) {
			$this->errormsg = "Unable to upload file extension \"". $ext . "\"";
			return '';
		} else {
			if (move_uploaded_file($tempname, $targetPath)) {
				$resultfile = $targetname;
			} else {
				$this->errormsg = "There was an error uploading the file, please try again.";
				return '';
			}
		}
	}
	return $resultfile;
}

function DeleteFile($oldname, $targetloc)
{
	if (!$targetloc)
		$targetloc = $this->filelocation;
	if ($oldname) {
		unlink($targetloc.$oldname);
	}
}

}
?>