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

class EmailClass {

var $from;
var $replyto;
var $to;
var $cc;
var $bcc;
var $msg;
var $subject;
var $framework;
var $phpmailerpath;

function EmailClass ($framework)
{
	$this->to = array();
	$this->cc = array();

	$settings = $framework->LoadClassConfig('email');
	if ($settings) {
		$this->from = isset($settings['From']) ? $settings['From'] : '';
		$this->replyto = isset($settings['ReplyTo']) ? $settings['ReplyTo'] : '';
		$this->bcc = isset($settings['BCC']) ? $settings['BCC'] : '';
		$this->phpmailerpath = isset($settings['PHPMailer']) ? $settings['PHPMailer'] : '';
	}
	$this->framework = $framework;
}
function Framework()
{
	return $this->framework;
}

function To ($to='')
{
	if ($to == '') {
		$this->to = array();
	} else if (!in_array($to,$this->to)) {
		$this->to[] = $to;
	}
}

function From ($from)
{
	$this->from = $from;
}

function CC ($cc)
{
	if ($cc == '') {
		$this->cc = array();
	} else if (!in_array($cc,$this->cc)) {
		$this->cc[] = $cc;
	}
}

function BCC ($bcc)
{
	if (!empty($this->bcc))
		$this->bcc .= ';';
	$this->bcc .= $bcc;
}

function Subject ($subject)
{
	$this->subject = $subject;
}

function Message ($msg)
{
	$this->msg = $msg;
}

function Debug ()
{
/*
	echo "Email:<br>
To: ";
	print_r($this->to);
	echo "<br>
Subject: $this->subject<br>
" . $this->MakeHeaders();
	$this->framework->DebugMsg($msg);
*/
}

private function MakeHeaders()
{
	$headers = 'From: ' . $this->from . "\r\n";
	$headers .= 'Reply-To: ' . $this->replyto . "\r\n";
	if (!empty($this->cc))
		$headers .= 'Cc: ' . $this->cc . "\r\n";
	if (!empty($this->bcc))
		$headers .= 'Bcc: ' . $this->bcc . "\r\n";
	$headers .= 'X-Mailer: PHP/' . phpversion();
	return $headers;
}

// Returns 0 if the message was sent (not necessarily received)
function Send ()
{
	if (!count($this->to) || empty($this->subject))
		return 1;
	$this->Debug();
	if ($this->phpmailerpath) {
		require_once ($this->phpmailerpath . '/class.phpmailer.php'); 
		$mail = new PHPMailer();
		$mail->SetFrom($this->from);
		$mail->AddReplyTo($this->replyto);
		$mail->AddBCC($this->bcc);
		foreach($this->to as $to)
			$mail->AddAddress($to);
		foreach($this->cc as $cc)
			$mail->AddCC($cc);
		$mail->Subject = $this->subject;
		$mail->MsgHTML($this->msg);
		$x = $mail->Send();
		return $x;
	} else {
		$headers = $this->MakeHeaders();
		return (mail ($this->to, $this->subject, $this->msg, $headers)) ? 0 : 2;
	}
}

// Returns 0 if the message was sent (not necessarily received)
function SendHTML ()
{
	if (empty($this->to) || empty($this->subject))
		return 1;

	$headers  = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= $this->MakeHeaders();

	$this->Debug();
	return (mail ($this->to, $this->subject, $this->msg, $headers)) ? 0 : 2;
}

}
