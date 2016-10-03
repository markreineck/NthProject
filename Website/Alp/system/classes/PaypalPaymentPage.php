<?php
// https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables/

/*
Copyright (c) 2015, Nth Generation. All rights reserved.

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

class PaypalPaymentPage extends AlpClass {

var $Mode;
var $notifyurl;
var $url;
var $amount;
var $customer;
var $invoice;
var $debug;

function PaypalPaymentPage($framework)
{
	parent::__construct($framework);
	$settings = $this->LoadConfig('paypal');
	if ($settings) {
		$this->Mode = (isset($settings['Mode'])) ? $settings['Mode'] : 'L';
		if ($this->Mode == 'L') {
			$this->url = $settings['LiveURL'];
		} else {
			$this->url = $settings['SandBoxRUL'];
		}
		$this->notifyurl = (isset($settings['NotifyURL'])) ? $settings['NotifyURL'] : '';
		$this->debug = ($this->Framework()->DebugMode) ? true : $settings['DebugMode'];
	} else {
		$this->debug = $this->Framework()->DebugMode;
	}
}

function AmountField($data)
{
	$this->Framework()->Forms()->ShowHiddenField('amount', $data);
}

function InvoiceField($data)
{
	$this->Framework()->Forms()->ShowHiddenField('invoice', $data);
}

function ItemNumberField($data)
{
	$this->Framework()->Forms()->ShowHiddenField('item_number', $data);
}

function ItemNameField($data)
{
	$this->Framework()->Forms()->ShowHiddenField('item_name', $data);
}

function QuantityField($data)
{
	$this->Framework()->Forms()->ShowHiddenField('quantity', $data);
}

function CustomField($data)
{
	$this->Framework()->Forms()->ShowHiddenField('custom', $data);
}

function DefaultContactFields($first, $last, $email, $address, $city, $state, $zip, $country)
{
	if ($first)
		$this->Framework()->Forms()->ShowHiddenField('custom', $first);
	if ($last)
		$this->Framework()->Forms()->ShowHiddenField('custom', $last);
	if ($address)
		$this->Framework()->Forms()->ShowHiddenField('custom', $address);
	if ($city)
		$this->Framework()->Forms()->ShowHiddenField('custom', $city);
	if ($state)
		$this->Framework()->Forms()->ShowHiddenField('custom', $state);
	if ($$zip)
		$this->Framework()->Forms()->ShowHiddenField('custom', $zip);
	if ($country)
		$this->Framework()->Forms()->ShowHiddenField('custom', $country);
}

function OpenForm()
{
	echo '
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input name="cmd" type="hidden" value="_s-xclick" /> <input name="hosted_button_id" type="hidden" value="YV7DBTYB7NJ6E" />
';
	if ($this->notifyurl)
		$this->Framework()->Forms()->ShowHiddenField('notify_url', $this->notifyurl);
}

function CloseForm()
{
	echo '
<input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif" type="image" /> 
<img alt="" border="0" height="1" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" />
</form>
';	
}

function ExtractPaymentResult()
{
}

function GetAuthorizationCode()
{
}

function GetErrorCode()
{
}

function GetErrorMsg()
{
}

function GetAmount()
{
	return $this->amount;
}

function GetInvoice()
{
	return $this->invoice;
}

function GetCustomer()
{
	return $this->customer;
}

}
?>