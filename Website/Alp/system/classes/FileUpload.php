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
The purpose of the form class is to
1. Provide a common styling of forms throughout the site.
2. Provide easy and cinsistent implementation of javascript field falidation
3. Implement original values in order to determine what values have changed
4. Implement fields with special requirements such as numeric fields or fixed length fields
5. Reload posted data in case the operation fails requiring the user to make a change to what data is submitted.

The class supports forms built using <table> tags or <div> tags

It is anticipated that child classes will be derived from this class to implement 
more specific fields related to your project. For instance a FirstNameField() function
might be used in an application that requires entry of a first name in order to require
consistent input with the same constraints throughout the application.
*/

class FileUpload extends AlpClass {
var $errormsg;
var $FileTypes;
var $filelocation;
var $ResizeFileTypes;
var $MaxSize = 0;
var $MaxSizeDesc;
var $MaxHeight = 150;
var $MaxWidth = 150;

function FileUpload($framework)
{
	parent::__construct($framework);
	$settings = $this->LoadConfig('fileupload');

	if ($settings) {
		if (isset($settings['FileTypes']))
			$this->FileTypes = $settings['FileTypes'];
		if (isset($settings['ResizeFileTypes']))
			$this->ResizeFileTypes = $settings['ResizeFileTypes'];
		if (isset($settings['FileLocation']))
			$this->filelocation = $settings['FileLocation'];
		if (isset($settings['MaxSize'])) {
			$this->MaxSizeDesc = $settings['MaxSize'];
			$mult = 1;
			if ($pos = strpos($this->MaxSizeDesc,'K')) {
				$mult = 1024;
				$size = substr($this->MaxSizeDesc,0,$pos);
			} else if ($pos = strpos($this->MaxSizeDesc,'M')) {
				$mult = 1024 * 1024;
				$size = substr($this->MaxSizeDesc,0,$pos);
			} else
				$size = $this->MaxSizeDesc;

			if (is_numeric($size))
				$this->MaxSize = $size * $mult;			
		}
	}
}

function ErrorMsg()
{
	return $this->errormsg;
}

private function UploadHasErrors($fieldname, $allowedFiletypes='')
{
	$this->errormsg = '';

	if (!$allowedFiletypes)
		$allowedFiletypes = $this->FileTypes;

	if (!isset($_FILES[$fieldname]) || !$_FILES[$fieldname]) {
		$this->errormsg = 'No file upload was detected for ' . $fieldname;
		return true;
	} else {
		$targetname = $_FILES[$fieldname]["name"];
		$ext = substr($targetname, strpos($targetname,'.'), strlen($targetname)-1);

		if (!in_array(strtolower($ext),$allowedFiletypes)) {
			$this->errormsg = "Unable to upload file extension \"". $ext . "\"";
			return true;
		} else if ($_FILES[$fieldname]["size"] > $this->MaxSize ) {
			$this->errormsg = "Error: Your file was larger than $this->MaxSizeDesc. Please upload a smaller file.";	
			unlink($fileTmpLoc);
			return true;
		} else {
			$file = $_FILES[$fieldname];
			if ($file['error']) {
				$this->DebugMsg($file['error']);
				$this->errormsg = 'There was an error uploading the file to the server: ' . $file['error'] . ', Please check the validity of the file you are uploading and try again.';
				return true;
			}
		}
	}
	return false;
}

function UploadPostedFile($fieldname, $targetloc='', $targetname='')
{
	$this->errormsg = '';
	if (!$this->UploadHasErrors($fieldname)) {
		$file = $_FILES[$fieldname];
		return $this->UploadFile(($targetname) ? $targetname : $file['name'], $file['tmp_name'], $targetloc);
	}
	return '';
}

function DeleteFile($oldname, $targetloc)
{
	if (!$targetloc)
		$targetloc = $this->filelocation;
	if ($oldname) {
		unlink($targetloc.$oldname);
	}
}

function UploadFile($targetname, $tempname, $targetloc='')
{
	$this->DebugMsg("Upload $tempname to $targetloc / $targetname<br>");

	$this->errormsg = '';
	$resultfile = '';
	if ($targetname) {
		if (!$targetloc)
			$targetloc = $this->filelocation;

		$targetPath	= $targetloc . $targetname;

		$this->DebugMsg("Move $tempname to $targetPath");
		if (move_uploaded_file($tempname, $targetPath)) {
			$resultfile = $targetname;
		} else {
			$this->errormsg = "There was an error uploading the file, please try again.";
			$this->DebugMsg($this->errormsg);
			return '';
		}
	}
	return $resultfile;
}

function ResizeUpload ($tagname, $destfile, $quality=75, $MaxHeight=64, $MaxWidth=64) 
{
	$msg = NULL;
	$im = '';

	$this->errormsg = '';
	if (!$this->UploadHasErrors($tagname, $this->ResizeFileTypes)) {
		$fileName = $_FILES[$tagname]["name"];
		$fileTmpLoc = $_FILES[$tagname]["tmp_name"];
		$fileType = $_FILES[$tagname]["type"];
		$fileSize = $_FILES[$tagname]["size"];
		$fileExt = strtolower(end(explode(".", $fileName)));

		if ($fileExt == "jpeg" || $fileExt == "jpg" ) {
			$im = imagecreatefromjpeg($fileTmpLoc);
		} else if ($fileExt == "gif") {
			$im = @imagecreatefromgif($fileTmpLoc);
		} else if ($fileExt == "png") {
			$im = @imagecreatefrompng($fileTmpLoc);
		}

		if (empty($im)) {
			$this->errormsg = "Error: the uploaded file was not a valid image file or not a valid file type.";
		} else {
			$originalsize = getimagesize($fileTmpLoc);
			$originalwidth = $originalsize[0];
			$originalheight = $originalsize[1];

			$height = $MaxHeight;
			$width = $MaxWidth;

			$ratio_orig = $originalwidth / $originalheight;
			if ($width/$height > $ratio_orig) {
				$width = $height*$ratio_orig;
			} else {
				$height = $width/$ratio_orig;
			}

			$newimage = imagecreatetruecolor($width,$height);
			imagefill ($newimage, 0, 0, imagecolorallocate ($newimage, 255, 255, 255));
			imagecopyresampled($newimage, $im, 0,0,0,0, $width, $height, $originalwidth, $originalheight);
			$fileSizeResized = filesize($fileTmpLoc);
			imagejpeg($newimage, $destfile, $quality);
		}
		unlink($fileTmpLoc);
	}
	return $destfile;
}

}
?>