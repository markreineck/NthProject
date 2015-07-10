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

class FormClass extends AlpClass {

var $jspath = '/Alp/system/javascript';
var $imgpath = '/Alp/system/icons';
var $fixedlength=false;
var $autocap=false;
var $buttonclass='';
var $labelclass='';
var $textclass='';
var $textareaclass='';
var $listclass='';
var $errorclass='';
var $checkboxclass='';
var $fieldholderclass='';
var $radioclass='';
var $msgclass='';
var $validationlist;
var $validationfunc;
var $fieldlist;
var $dateformat;
var $newdata;
var $tableforms=false;
//var $framework;


/********************************************************************************
Constructor

The constructor should not be called directly. It is called by the framework 
when the form() method is called to implement the form class. It initiates the 
class by reading the setting from the configuration file.
*********************************************************************************/
function FormClass($framework)
{
	parent::__construct($framework);
	$this->dateformat = 'Y-m-d';
	$settings = $this->LoadConfig('forms');
	if ($settings) {
		$this->fieldholderclass = @$settings['FieldHolderClass'];
		$this->buttonclass = @$settings['ButtonClass'];
		$this->labelclass = @$settings['LabelClass'];
		$this->textclass = @$settings['TextClass'];
		$this->textareaclass = @$settings['TextAreaClass'];
		$this->listclass = @$settings['ListClass'];
		$this->errorclass = @$settings['ErrorClass'];
		$this->checkboxclass = @$settings['CheckBoxClass'];
		$this->radioclass = @$settings['RadioButtonClass'];
		$this->msgclass = @$settings['MessageClass'];
		$this->tableforms = @$settings['TableForms'];
		$this->dateformat = isset($settings['DateFormat']) ? $settings['DateFormat'] : 'Y-m-d';
	}
	$this->validationlist = array();
	$this->fieldlist = array();
	$this->newdata = false;
}

// NewData() informs the class that the form should be loaded with new data instead of reloading posted data.
// You would do this if reloading the same form for additional input after a successful save of the previous submission.
function NewData()
{
	$this->newdata = true;
}

function RedirectTo($url)
{
	echo "<script language='JavaScript'> window.location='$url'; </script>";	
	exit (0);
}

function ShowHiddenField ($name, $value)
{
	if (ctype_alnum($value))
		echo "<input type=\"hidden\" name=\"$name\" id=\"$name\" value=\"$value\">";
	else
		echo "<textarea name=\"$name\" id=\"$name\" style=\"visibility:hidden;height:0px;width:0px\">$value</textarea>";
}

/********************************************************************************
Page Construction Functions
*********************************************************************************/
function OpenFieldSection ()
{
	echo ($this->tableforms) ? '
<tr><td>' : '
<div>';
}

function CloseFieldSection ()
{
	echo ($this->tableforms) ? '</td></tr>
' : '</div></div>
';
}

function OpenFieldContainer ()
{
	echo ($this->tableforms) ? '<td>' : '<div>';
}

// This function will place a block onto the page where error messages will be placed. 
// It can be preloaded with an error message. 
// Otherwise form validation errors will be placed here whent he form is validated.
// A success message can also be passed indicating successful completion of something.
function ShowFormErrors ($err='', $donemsg='')
{
	if (empty($err))
		echo "<div class=\"$this->errorclass\" id=\"ErrorBlock\" style=\"display:none\"></div>
";
	else
		echo "<div class=\"$this->errorclass\" id=\"ErrorBlock\"><span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span> $err</div>
";
	if (!empty($donemsg))
		echo "<div class=\"$this->msgclass\" id=\"MessageBlock\"><span class=\"glyphicon glyphicon-ok-sign\" aria-hidden=\"true\"></span> $donemsg</div>
";
}

/********************************************************************************
Validation Construction Functions

Fields requiring validation are placed into a list along with the type of validation that is required.
Eventually, typically at the bottom of the page, the FormValidation() function is called which will scan the list and write javascript code to the page to implement the validation.
*********************************************************************************/
function AddValidationField ($name, $label, $rule, $data='')
{
	array_push($this->validationlist, array('Name' => $name, 'Label' => $label, 'Rule' => $rule, 'Data' => $data));
}

function AddNumValidationField ($name, $label, $min, $max)
{
	array_push($this->validationlist, array('Name' => $name, 'Label' => $label, 'Rule' => 'Number', 'Min' => $min, 'Max' => $max));
}

// AppendFieldName() keeps a list of field names on the form ensuring that no 2 fields have the same name.
function AppendFieldName ($name)
{
	if (!(array_search($name, $this->fieldlist) === false)) {
		$this->Framework()->ShowErrorMessage("Field $name already exists on the form", 'Form Error');
		exit(0);
	}

	array_push($this->fieldlist, $name);
}

// Use this function to load the form's OnSubmit attribute with javascript that will trigger the field validation
function ShowOnSubmit ($funcname='')
{
	$this->validationfunc = ($funcname == '') ? 'FormValidation' : $funcname;
	echo "onsubmit=\"return $this->validationfunc(this);\" ";
}

// FormValidation() will place the javascript on the page to execute the form validation. 
// Obviously you need to place all fields on the page before calling this.
// Therefore this is typically done at the bottom of the page. 
function FormValidation()
{
	$checkemail = false;
	$checkurl = false;
	$checknum = false;
	$checkdate = false;

	if ($this->validationfunc) {
	echo "
<script type=\"text/javascript\">
<!--
function $this->validationfunc (frm)
{
var valid = true;
var errmsg = '';
";

	foreach ($this->validationlist as $data) {
		$field = $data['Name'];
		$label = $data['Label'];
		switch ($data['Rule']) {
			case 'MinLen':
				$len = $data['Data'];
				echo "
if (frm.$field.value.length < $len) {
	errmsg = errmsg + '$label is required.<br>';
	valid = false;
}
";
				break;
			case 'MaxLen':
				$len = $data['Data'];
				echo "
if (frm.$field.value.length > $len) {
	errmsg = errmsg + 'Please limit $label to $len characters.<br>';
	valid = false;
}
";
				break;
			case 'Radio':
				$cnt = $data['Data'];
				echo "if ((frm." . $field . "[0].checked == false)";
				for ($x=1; $x<$cnt; $x++)
					echo " && (frm." . $field . "[$x].checked == false)";
				echo ") {
	errmsg = errmsg + '$label is required.<br>';
	valid = false;
}
";
				break;
			case 'Check':
				$cnt = $data['Data'];
				echo "if (frm.$field.checked == false) {
	errmsg = errmsg + '$label is must be checked.<br>';
	valid = false;
}
";
				break;
			case 'List':
				$cnt = $data['Data'];
				echo "if (frm.$field.selectedIndex == 0) {
	errmsg = errmsg + '$label must be selected.<br>';
	valid = false;
}
";
				break;
			case 'Email':
				$checkemail = true;
				echo "if (frm.$field.value.length > 0) {
	if (!EmailAddressOK (frm.$field.value)) {
		errmsg = errmsg + '$label is not a valid email address.<br>';
		valid = false;
	}
}
";
				break;
			case 'URL':
				$checkurl = true;
				echo "if (frm.$field.value.length > 0) {
	if (!IsUrl (frm.$field.value)) {
		errmsg = errmsg + '$label is not a valid web address.<br>';
		valid = false;
	}
}
";
				break;
			case 'Date':
				$checkdate = true;
				$len = $data['Data'];
				echo "if (frm.$field.value.length > 0) {
	if (!IsDate (frm.$field.value)) {
		errmsg = errmsg + '$label is not a valid date.<br>';
		valid = false;
	}
}
";
				break;
			case 'Digit':
				$checknum = true;
				$len = $data['Data'];
				echo "if (frm.$field.value.length > 0) {
	if (!IsDigits (frm.$field.value, $len)) {
		errmsg = errmsg + '$label is not valid.<br>';
		valid = false;
	}
}
";
				break;
			case 'Number':
				$checknum = true;
				echo "if (frm.$field.value.length > 0) {
	if (!IsNumber (frm.$field.value)) {
		errmsg = errmsg + '$label is not a valid number.<br>';
		valid = false;
	} else {
";
				$d = @$data['Min'];
				if ($d != 0)
					echo "if (frm.$field.value < $d) {
			errmsg = errmsg + '$label must be greater than $d.<br>';
			valid = false;
		}
";
				$d = @$data['Max'];
				if ($d != 0)
					echo "if (frm.$field.value > $d) {
			errmsg = errmsg + '$label must be less than $d.<br>';
			valid = false;
		}
";
				echo '	}
}
';
				break;
			default:
				echo '/* Unknown validation rule: '.$data['Rule'].' */
';
		}
	}
echo "
	if (!valid) {
		d = document.getElementById('ErrorBlock');
		if (d) {
			d.innerHTML = errmsg;
			d.style.display = 'block';
		}
		d = document.getElementById('MessageBlock');
		if (d) {
			d.style.display = 'none';
		}
	}
	return valid;
}
-->
</script>
";
	}
	echo "<script type=\"text/javascript\" src=\"$this->jspath/Alp.js\"></script>
";
/*
	if ($this->fixedlength) {
		echo "<script type=\"text/javascript\" src=\"$this->jspath/TextBoxAutoTab.js\"></script>
";
	}
	if ($this->autocap) {
		echo "<script type=\"text/javascript\" src=\"$this->jspath/DoCapitalization.js\"></script>
";
	}
	if ($checkemail) {
		echo "<script type=\"text/javascript\" src=\"$this->jspath/ValidateEmail.js\"></script>
";
	}
	if ($checkurl) {
		echo "<script type=\"text/javascript\" src=\"$this->jspath/ValidateURL.js\"></script>
";
	}
	if ($checknum) {
		echo "<script type=\"text/javascript\" src=\"$this->jspath/ValidateNumber.js\"></script>
";
	}
	if ($checkdate) {
		echo "<script type=\"text/javascript\" src=\"$this->jspath/ValidateDate.js\"></script>
";
	}
*/
}

/********************************************************************************
Low Level Input Field Construction Functions

These are helper function implement imput field creation. 
They would not be called outside of the class.
*********************************************************************************/

// ShowInputLabel() begins construction of an input field by placing opening tags 
// and showing the label for the field.
function ShowInputLabel ($label, $name, $req)
{
	$lbl = ($req > 0) ? '* ' : '';
	if (strlen($label) > 0)
		$lbl .= $label . ':';
	$this->OpenFieldSection();
	if ($this->tableforms)
		echo "<label class=\"$this->labelclass\" for=\"$name\">$lbl</label></td>
";
	else
		echo "<div class=\"$this->labelclass\"><label for=\"$name\">$lbl</label></div>
";

 //REY: echo "<tr><td valign=\"top\"><label class=\"$this->labelclass\" for=\"$name\">$lbl</label></td>
}

// ShowInputLabel() places the original value of a field into a 
// hidden field of the sane name except with 'Old' appended.
private function ShowOriginalValue_ ($name, $value)
{
	if (strlen($value) > 0)
		$this->ShowHiddenField($name . 'Old', $value);
}

// ShowFixedLengthInputBox() is the base constructor for fixed length input fields.
// The initial value will be overridden with a posted value for NewData() has not been set.
// Javascript code will be implemented to move to the next field when the last character is entered.
private function ShowFixedLengthInput_ ($name, $next, $size, $val)
{
	$this->fixedlength = true;

	echo "<input name='$name' id='$name' type='text' class=\"$this->textclass\" size='$size' maxlength='$size' ";
	if (!$this->newdata && $this->Framework()->IsPosted($name)) {
		echo 'value="' . $this->Framework()->PostedData($name) . '" ';
	} else if (strlen($val) > 0) {
		$val = str_replace('"', '&quot;', $val);
		echo "value=\"$val\" ";
	} else {
		echo "onkeyup=\"TextBoxAutoTab('$name',$size,'$next');\" ";
	}
	if (strlen($this->textclass) > 0)
		echo "class='$this->textclass' ";
	echo '/>';
}

// ShowInput_() is the base constructor for a variable length input field.
// It will only place the field on the page, no label or surrounding tags.
// $fieldtype is typically "text" or "password"
// $minlen will create a javascript validation for minimum length. To simply make the field required set it to 1 
// Setting $autocap to true will implement a javascript autocap conversion whent he focus leaves the field
// Inclusion of a $help message will place a help icon that implements a mouse over help message.
private function ShowInput_ ($fieldtype, $label, $name, $maxlen, $size, $value, $minlen, $autocap, $help='')
{
	$val = (!$this->newdata && $this->Framework()->IsPosted($name)) ? $this->Framework()->PostedData($name) : str_replace('"', '&quot;', $value);

	$this->AppendFieldName ($name);
	echo "<input type=\"$fieldtype\" class=\"$this->textclass\" name=\"$name\" id=\"$name\" size=\"$size\" maxlength=\"$maxlen\" value=\"$val\" ";

	if ($autocap)
		echo "onblur='this.value=DoCapitalization(this.value)' ";
	echo '/>
';
	if (!empty($help))
		echo "<a id=\"$name-HelpIcon\" class=\"Tooltip\" z-index=\"998\"><img class=\"TooltipImg\" src=\"$this->imgpath/help.png\" alt=\"Help\" />
<div id=\"$name-Help\" class=\"TooltipDesc\" style=\"display:none\" z-index=\"999\"> $help
</div></a>
<script type=\"text/javascript\">
document.getElementById('$name-HelpIcon').addEventListener('mouseover', function() {
document.getElementById('$name-Help').style.display = 'block';
}, false);
document.getElementById('$name-HelpIcon').addEventListener('mouseout', function() {
document.getElementById('$name-Help').style.display = 'none';
}, false);
</script>
";

	$this->ShowOriginalValue_ ($name, $value);

	if ($minlen)
		$this->AddValidationField ($name, $label, 'MinLen', $minlen);
}

// ShowInputField_() is the base constructor for a variable length input field with label and surrounding tags.
// The parameters are the same as those for ShowInput_().
private function ShowInputField_ ($fieldtype, $label, $name, $maxlen, $size, $value, $minlen, $autocap, $help='')
{
	$this->ShowInputLabel ($label, $name, $minlen);
	$this->OpenFieldContainer();
	$this->ShowInput_ ($fieldtype, $label, $name, $maxlen, $size, $value, $minlen, $autocap);
	$this->CloseFieldSection();
}

/********************************************************************************
List Functions

These are helper function that fill a select list with values
These function are called by the list creation function from the class but could 
also be called outside of the class if someone needs to create a select list without using the class.
*********************************************************************************/

// ListValueDropDown() will fill a selection list with selections from an array selecting the item indicated by $sel. See the description of the input fields section later for the format of the $list array
function ListValueDropDown ($list, $sel)
{
	foreach ($list as $row) {
		if (is_array($row)) {
			$val = ($row[1]) ? $row[1] : $row[0];
			$var = $row[0];
		} else {
			$val = $row;
			$var = $row;
		}
;
		if ($var == $sel)
			echo "<option value='$var' selected='selected'>$val</option>";
		else
			echo "<option value='$var'>$val</option>";
	}
}

// ListNumDropDown() will fill a selection list with a list of numbers selecting the item indicated by $sel. 
// $start, $end and $inc indicate the value of the first selection, the value of the last section 
// and the amount to increment each selection from the first to the last.
// Build a list with selections 1,2,3,4,5 like this: ListNumDropDown (1, 5, 1);
// Build a list with selections 10,8,6,4,2,0 like this: ListNumDropDown (10, 0, -2);
function ListNumDropDown ($start, $end, $inc=1, $sel=0)
{
	for ($x=$start; $x!=$end; $x+=$inc) {
		if ($x == $sel)
			echo "<option value='$x' selected='selected'>$x</option>";
		else
			echo "<option value='$x'>$x</option>";
	}
}

/********************************************************************************
Basic Input Field Construction Functions

In general parameters are:
$label = Label for the field
$name = the name given to the field in the name attribute
$maxlen = the maximum lenght
$size = the size of the field in characters
$value = the initial value to be loaded into the field. in the case of check boxes
$minlen = the number of required characters to be entered. 0 makes the field optional
$onchange = optional javascript that can be executed on change

For selection lists and radio buttons the $list should have the following format:
The selection list array should contain arrays such that 0=>id and 1=>the verbal selection to be shown to the user. In the case where there is no separate id the selection list array can be just a array of strings.

For selection lists $req is
// 0=no selection required, an empty line will be included at the top of the list
// 1=an empty line is included empty line but selection is required
// 2=no empty line in the selection list, selection is required, 
     if no initial value is given thent he first item in the list is preselected
*********************************************************************************/

/********************************************************************************
Input Fields With Labels

These functions will place an input field on a page with a label and surrounded 
with either <div> tags or <tr><td> tags in order to palce them into a form.
*********************************************************************************/

// Show an input field with auto-capitalization
function ShowAutoCapField ($label, $name, $maxlen, $size, $value='', $minlen=0)
{
	$this->autocap = true;
	$this->ShowInputField_ ('text', $label, $name, $maxlen, $size, $value, $minlen, true);
}

// Show a text input field
function ShowTextField ($label, $name, $maxlen, $size, $value='', $minlen=0, $help='')
{
	$this->ShowInputField_ ('text', $label, $name, $maxlen, $size, $value, $minlen, false, $help);
}

// Show a password input field
function ShowPasswordTextField ($label, $name, $value='', $minlen='', $size=20, $help='')
{
	$this->ShowInputField_ ('password', $label, $name, $size, $size, $value, $minlen, false, $help);
}

// Show an input field accepting only digits (0-9)
function ShowDigitField ($label, $name, $size, $value='', $minlen=0)
{
	$this->ShowInputField_ ('text', $label, $name, $size, $size, $value, ($minlen > 0) ? 1 : 0, false);
	$this->AddValidationField ($name, $label, 'Digit', $minlen);
}

// Show an input field that accepts numeric input (digits plus decimals and signs)
function ShowNumericField ($label, $name, $min, $max, $size, $value='')
{
	$this->ShowInputField_ ('text', $label, $name, $size, $size, $value, ($min > 0) ? 1 : 0, false);
	$this->AddNumValidationField ($name, $label, $min, $max);
}

// Show an input field that requires a fixed number of characters such as a US state or an area code.
function ShowFixedLengthField ($label, $name, $size, $next, $value='', $required='')
{
	$this->ShowInputLabel ($label, $name, $minlen);

	$this->OpenFieldContainer();
	$this->ShowFixedLengthInput_ ($name, $next, $size, $value);
	$this->ShowOriginalValue_ ($name, $value);
	$this->CloseFieldSection();

	if ($required)
		$this->AddValidationField ($name, $label, 'MinLen', $size);
}

// Show a selection list field. 
function ShowListField ($label, $name, $list=NULL, $req=0, $sel='', $onchange='')
{
	$this->ShowInputLabel ($label, $name, $req);
	$this->OpenFieldContainer();
	$this->ShowList ($name, $list, $req, $sel, $onchange);
	$this->CloseFieldSection();
	if ($req == 1)
		$this->AddValidationField ($name, $label, 'List', '');
}

// Show a selection list field filled with numbers. 
private function MakeNumericList ($first, $last, $increment=1)
{
	$list = array();
	if ($increment>0)
		while ($first <= $last) {
			array_push($list,$first);
			$first += $increment;
		}
	else if ($increment<0)
		while ($first >= $last) {
			array_push($list,$first);
			$first += $increment;
		}
	return $list;
}

function ShowNumericListField ($label, $name, $first, $last, $increment=1, $sel='', $req=0) 
{
	$list = $this->MakeNumericList ($first, $last, $increment);
	$this->ShowListField ($label, $name, $list, $req, $sel);
}

// Show a a field with a set of radio buttons. 
function ShowRadioField ($label, $name, $list, $req=1, $sel='')
{
	$this->ShowInputLabel ($label, $name, $req);
	$this->OpenFieldContainer();
	$this->ShowRadioButtons ($label, $name, $list, $req, $sel);
	$this->CloseFieldSection();
}
function ShowRadioButtons ($label, $name, $list, $req=1, $sel='')
{
	$this->AppendFieldName ($name);
	$xsel = (!$this->newdata && $this->Framework()->IsPosted($name)) ? $this->Framework()->PostedData($name) : $sel;
	$this->ShowOriginalValue_ ($name, $sel);

	$cnt = 0;
	foreach ($list as $data) {
		$cnt += 1;
		echo "<label for=\"$name$cnt\" class=\"$this->labelclass\">$data[1]</label>&nbsp;";
		echo "<input type=\"radio\" class=\"styled\" name=\"$name\" id=\"$name$cnt\" value=\"$data[0]\"";
		if ($data[0] == $xsel)
			echo ' checked="checked"';
		if (!empty($data[2]))
			echo " onclick=\"$data[2]\"";
		echo '/><br />';
		
	}

	if ($req > 0)
		$this->AddValidationField ($name, $label, 'Radio', $cnt);
}

// Show a checkbox field. In this case $value is the value that should be returned
// if the checkbox is checked. $checked is a rtrue/false value indicating initial state.
// Required allows you to require that the checkbox be checked for instance to accepts terms.
function ShowCheckBoxField ($label, $name, $value, $checked, $required=false, $onclick='')
{
	$this->ShowInputLabel ($label, $name, $required);

	$this->OpenFieldContainer();


	$this->ShowCheckBox ($name, $value, $checked, $required, $onclick);

	$this->CloseFieldSection();

	if ($required)
		$this->AddValidationField ($name, $label, 'Check', '');
}

// Shows a textarea field
function ShowTextArea ($label, $name, $rows, $cols, $value='', $minlen=0)
{
	$this->AppendFieldName ($name);

	if (!$this->newdata && $this->Framework()->IsPosted($name)) {
		$val = $this->Framework()->PostedData($name);
		if (get_magic_quotes_gpc())
			$val = stripslashes($val);
	} else {
		$val = $value;
	}

	echo "<textarea class=\"$this->textareaclass\" name=\"$name\" id=\"$name\" cols=\"$cols\" rows=\"$rows\" />$val</textarea>
";
	$this->ShowOriginalValue_ ($name, $value);

	if ($minlen)
		$this->AddValidationField ($name, $label, 'MinLen', $minlen);
}

function ShowTextAreaField ($label, $name, $rows, $cols, $value='', $minlen=0)
{
	$this->ShowInputLabel ($label, $name, $minlen);
	$this->OpenFieldContainer();

	$this->ShowTextArea ($label, $name, $rows, $cols, $value, $minlen);
	$this->CloseFieldSection();
}

/********************************************************************************
Input Fields Without Labels

These functions will place an input field on a page withotu any labels or surrounding tags.
You might use this for instance if making a grid of input fields.
*********************************************************************************/

// Show an input field accepting only digits (0-9)
function ShowDigitInput ($label, $name, $size, $value='', $minlen=0, $maxlen=0)
{
	if ($maxlen <= 0)
		$maxlen = $size;
	$this->ShowInput_ ('text', $label, $name, $maxlen, $size, $value, ($minlen > 0) ? 1 : 0, false);
	$this->AddValidationField ($name, $label, 'Digit', $minlen);
}

function ShowNumericInput ($label, $name, $minlen, $maxlen, $size, $value='')
{
	$this->ShowInput_ ('text', $label, $name, $maxlen, $size, $value, ($minlen > 0) ? 1 : 0, false);
	$this->AddNumValidationField ($name, $label, $min, $max);
}

function ShowTextInput ($label, $name, $maxlen, $size, $value='', $minlen=0)
{
	$this->ShowInput_ ('text', $label, $name, $maxlen, $size, $value, ($minlen > 0) ? 1 : 0, false);
}

// Show a selection list field. Values for $req are:
function ShowList ($name, $list=NULL, $req=0, $sel='', $onchange='')
{
	$this->AppendFieldName ($name);
	$xsel = (!$this->newdata && $this->Framework()->IsPosted($name)) ? $this->Framework()->PostedData($name) : $sel;

	echo "<select name=\"$name\" id=\"$name\" class=\"$this->listclass\"";
	if (!empty($onchange))
		echo " onChange=\"$onchange\"";
	echo "/>
";
	if ($req < 2)
		echo '<option></option>';
	if ($list)
		$this->ListValueDropDown ($list, $xsel);
	echo '</select>
';
	$this->ShowOriginalValue_ ($name, $sel);
}

// Show a selection list field filled with numbers. 
function ShowNumericList ($name, $first, $last, $increment=1, $sel='', $req=0) 
{
	$list = $this->MakeNumericList ($first, $last, $increment);
	$this->ShowList ($name, $list, $req, $sel);
}

// Show a checkbox field. In this case $value is the value that should be returned
// if the checkbox is checked. $checked is a rtrue/false value indicating initial state.
// Required allows you to require that the checkbox be checked for instance to accepts terms.
function ShowCheckBox ($name, $value=1, $checked=false, $required=false, $onclick='')
{
	$this->AppendFieldName ($name);

	$xsel = (!$this->newdata && $this->Framework()->IsPosted($name)) ? $this->Framework()->PostedData($name) : $checked;

	echo "<input type=\"checkbox\" name=\"$name\" id=\"$name\" value=\"$value\"";
	if (!empty($this->checkboxclass))
		echo " class=\"$this->checkboxclass\"";
	if ($xsel)
		echo ' checked="checked"';
	if (!empty($onclick))
		echo " onclick=\"$onclick\"";
	echo "/>";
	if ($checked)
		$this->ShowOriginalValue_ ($name, $value);
	if ($required)
		$this->AddValidationField ($name, $label, 'Check', '');
}

/********************************************************************************
Date / Time Input Fields 

These functions will place an input field on a page withotu any labels or surrounding tags.
You might use this for instance if making a grid of input fields.
*********************************************************************************/

// Converts a date in string format to a date in the desired format as indicated in the concifuration file.
// This is necessary because dates stored in a database are often in a different format than what should be shown.
// $value can also be special codes like Today, FirstDayOfWeek or FirstDayOfMonth and other frequent default dates.
function GetDateString ($value)
{
	$date = NULL;
	switch($value){
		case 'Today':
			$date = date($this->dateformat);	
		break;
		case 'Yesterday':
			$date = date($this->dateformat,time()-86400); 
		break;
		case 'FirstDayOfWeek':
			$tempdate = getdate();
			$date = date($this->dateformat,mktime(0,0,0,date("m"),date("d")-date("d",(86400 * $tempdate['wday'])),date("Y")));
		break;
		case 'FirstDayOfMonth':
			$date = date($this->dateformat,mktime(0,0,0,date("m"),01,date("Y")));	
		break;
		case 'FirstDayOfYear':
			$date = date($this->dateformat,mktime(0,0,0,01,01,date("Y")));
		break;
		case 'Tomorrow':
			$date = date($this->dateformat,time()+86400); 
		break;
		case 'LastDayOfWeek':
			$tempdate = getdate();
			$cnt = $tempdate['wday'];
			$cnt = 6-$cnt;
			$date = date($this->dateformat,mktime(0,0,0,date("m"),date("d")+date("d",(86400 * $cnt)),date("Y")));
		break;
		case 'LastDayOfMonth':
			$date =  date($this->dateformat, strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y').' 00:00:00'))));
		break;
		case 'LastDayOfYear':
			$date = date($this->dateformat,mktime(0,0,0,12,31,date("Y")));
		break;
		default:
			if ($value) {
				$date = $this->FixDateString($value);
			} else {
				$date = NULL;
			}
		break;
	}
	return $date;
}

function GetTimeString ($value)
{
	$h = $_POST[$value.'1'];
	$m = $_POST[$value.'2'];
	$s = (isset($_POST[$value.'3'])) ? $_POST[$value.'3'] : '';
	$ap = (isset($_POST[$value.'AP'])) ? $_POST[$value.'AP'] : 'AM';
	if ($h == '' || $m == '' || !is_numeric($h) || !is_numeric($m))
		return '';
	else if ($h < 0 || $h > 23 || $m < 0 || $m > 59)
		return '';
	if (strlen($m) < 2)
		$m = '0' . $m;
	if ($ap == 'PM')
		$h += 12;
	$time = $h . ':' . $m;
	if ($s > 0 && $s < 10)
		$time .= ':0' . $s;
	else if ($s > 9 && $s < 60)
		$time .= ':' . $s;
	return $time;
}

// Convert a date string given in most normal formats to the desired format from the configuration file
function FixDateString($date)
{
	$datedash = str_replace("'","",explode("-",$date));
	if (count($datedash)>0) {
		$date = str_replace("-","/",$date);
	} else {
		$date = str_replace("'","",$date);
	}
	if ($date && strlen($date) > 4) {
		$date = str_replace("'","",$date);
		$newdate = date($this->dateformat,strtotime($date));
	} else {
		$newdate = 'null';
	}
	return $newdate;
}

// Show a date field 
function ShowDateField ($label, $name, $value='', $required=false)
{
	$val = $this->GetDateString ($value);
	$this->ShowInputField_ ('text', $label, $name, 15, 15, $val, ($required) ? 1 : 0, false);
	$this->AddValidationField ($name, $label, 'Date', 1);
}

// Show a time input field field (24 hour format)
function ShowTimeField ($label, $name, $next, $value='', $required=false)
{
	$this->ShowInputLabel ($label, $name, ($required) ? 2 : 0);
	$timeparts = explode (':', $value);
	switch (count($timeparts)) {
		case 2:
			$timeinit = array($timeparts[0], $timeparts[1]);
			break;
		case 1:
			$timeinit = array($timeparts[0], '');
			break;
		default:
			$timeinit = array('', '');
	}
	$this->OpenFieldContainer();

	$this->ShowFixedLengthInput_ ($name.'1', $name.'2', 2, $timeinit[0]);
	echo '&nbsp;:&nbsp;';
	$this->ShowFixedLengthInput_ ($name.'2', $next, 2, $timeinit[1]);
	$this->ShowOriginalValue_ ($name, $value);
	$this->CloseFieldSection();

	if ($required) {
		$this->AddValidationField ($name.'1', $label, 'Req', 1);
		$this->AddValidationField ($name.'2', $label, 'Req', 1);
	}
	$this->AddNumValidationField ($name.'1', $label, 0, 24);
	$this->AddNumValidationField ($name.'2', $label, 0, 59);
}

function ShowTime12Field ($label, $name, $value='', $required=false)
{
	$this->ShowInputLabel ($label, $name, ($required) ? 2 : 0);
	$timeparts = explode (':', $value);
	$ap = 'AM';
	switch (count($timeparts)) {
		case 0:
			$h = '';
			$m = '';
			break;
		case 1:
			$h = $timeparts[0];
			$m = '';
			break;
		default:
			$h = $timeparts[0];
			$m = $timeparts[1];
	}
	if ($h >= 12) {
		$ap = 'PM';
		$h -= 12;
	}

	$this->OpenFieldContainer();

	$this->ShowFixedLengthInput_ ($name.'1', $name.'2', 2, $h);
	echo '&nbsp;:&nbsp;';
	$this->ShowFixedLengthInput_ ($name.'2', $name.'AP', 2, $m);
	echo '&nbsp;';
	$this->ShowList ($name.'AP', array('AM'=>'AM','PM'=>'PM'), 2, $ap);
	$this->ShowOriginalValue_ ($name, $value);
	$this->CloseFieldSection();

	if ($required) {
		$this->AddValidationField ($name.'1', $label, 'Req', 1);
		$this->AddValidationField ($name.'2', $label, 'Req', 1);
	}
	$this->AddNumValidationField ($name.'1', $label, 0, 12);
	$this->AddNumValidationField ($name.'2', $label, 0, 59);
}

/********************************************************************************
Phone Number Input Fields

Phone number field functions will enable phone number input by placing separate 
fixed length input fields for each componenet of the phone number with auto-tab between them.

Because phone numbers will be posted as 3 separate fields it is easiest to use the 
ReturnPhoneNumber() function when processing the form results to assemble them back into 
a single string representation of the phone number. 

$minparts should be:
0 = optional
2 = area code not required
3 = phone number including area code is required
*********************************************************************************/

// Show a phone input field.
function ShowPhoneField ($label, $name, $next, $value='', $minparts=0)
{
	$this->ShowInputLabel ($label, $name, $minparts);
	$ph = explode ('-', $value);
	switch (count($ph)) {
		case 2:
			$phone = array('', $ph[0], $ph[1]);
			break;
		case 3:
			$phone = array($ph[0], $ph[1], $ph[2]);
			break;
		default:
			$phone = array('', '', '');
	}
	$this->OpenFieldContainer();

	$this->ShowFixedLengthInput_ ($name.'1', $name.'2', 3, $phone[0], ($minparts > 2) ? 3 : 0);
	echo '&nbsp;-&nbsp;';
	$this->ShowFixedLengthInput_ ($name.'2', $name.'3', 3, $phone[1], ($minparts > 1) ? 3 : 0);
	echo '&nbsp;-&nbsp;';
	$this->ShowFixedLengthInput_ ($name.'3', $next, 4, $phone[2], ($minparts > 1) ? 4 : 0);
	$this->ShowOriginalValue_ ($name, $value);
	$this->CloseFieldSection();

	$this->AddValidationField ($name.'1', $label, 'Digit', 3);
	$this->AddValidationField ($name.'2', $label, 'Digit', 3);
	$this->AddValidationField ($name.'3', $label, 'Digit', 4);
}

// Show a phone input field with an extension. 
// Minparts should be 2 or 3 depending on if an area code is required.
function ShowPhoneExtField ($label, $name, $value='', $ext='', $extlen, $minparts=0)
{
	$this->ShowInputLabel ($label, $name, $minparts);
	$ph = explode ('-', $value);
	switch (count($ph)) {
		case 2:
			$phone = array('', $ph[0], $ph[1]);
			break;
		case 3:
			$phone = array($ph[0], $ph[1], $ph[2]);
			break;
		default:
			$phone = array('', '', '');
	}
	$this->OpenFieldContainer();

	$this->ShowFixedLengthInput_ ($name.'1', $name.'2', 3, $phone[0], ($minparts > 2) ? 3 : 0);
	echo '&nbsp;-&nbsp;';
	$this->ShowFixedLengthInput_ ($name.'2', $name.'3', 3, $phone[1], ($minparts > 1) ? 3 : 0);
	echo '&nbsp;-&nbsp;';
	$this->ShowFixedLengthInput_ ($name.'3', $name.'Ext', 4, $phone[2], ($minparts > 1) ? 4 : 0);
	echo '&nbsp;Ext.&nbsp;';
	
	$nameext = $name . 'Ext';
	echo "<input type=\"text\" class=\"$this->textclass\" name=\"$nameext\" id=\"$nameext\" size=\"$extlen\" maxlength=\"$extlen\" value=\"$ext\" />
";

	$this->ShowOriginalValue_ ($name, $value);
	$this->ShowOriginalValue_ ($name.'Ext', $ext);
	$this->CloseFieldSection();

	$this->AddValidationField ($name.'1', $label, 'Digit', 3);
	$this->AddValidationField ($name.'2', $label, 'Digit', 3);
	$this->AddValidationField ($name.'3', $label, 'Digit', 4);
	$this->AddValidationField ($nameext, $label, 'Digit');
}

function ReturnPhoneNumber ($name, $delimiter='-')
{
	$p1 = $this->Framework()->PostedData($name.'1');
	$p2 = $this->Framework()->PostedData($name.'2');
	$p3 = $this->Framework()->PostedData($name.'3');
	if (strlen($p2) == 3 && strlen($p3) == 4) {
		$phone = $p2 . $delimiter . $p3;
		if (strlen($p1) == 3)
			$phone = $p1 . $delimiter . $phone;
		$phone = "$phone";
	} else
		$phone = '';
	return ($phone);
}

/********************************************************************************
Zip Code Input Fields

Zip code field functions will enable 2 part zip code input by placing separate 
fixed length input fields for each componenet of the zip code with auto-tab between them.
BNote that a 5 digit zip code can be easily implemented useign a fixed length field.

Because zip codes will be posted as 2 separate fields it is easiest to use the 
ReturnZipCode() function when processing the form results to assemble them back into 
a single string representation of the zip code. 

$minparts should be:
0 = optional
1 = the 1st 5 digits are required
2 = all 9 digits are required
*********************************************************************************/

// Show a zipcode field
function ShowZipCodeField ($label, $name, $next, $value='', $minparts=0)
{
	$this->ShowInputLabel ($label, $name, $minparts);
	$ph = explode ('-', $value);
	switch (count($ph)) {
		case 2:
			$zip = array($ph[0], $ph[1]);
			break;
		case 1:
			$zip = array($ph[0], '');
			break;
		default:
			$zip = array('', '');
	}
	$this->OpenFieldContainer();

	$this->ShowFixedLengthInput_ ($name.'1', $name.'2', 5, $zip[0]);
	echo '&nbsp;-&nbsp;';
	$this->ShowFixedLengthInput_ ($name.'2', $next, 4, $zip[1]);
	$this->ShowOriginalValue_ ($name, $value);
	$this->CloseFieldSection();

	if ($minparts > 0)
		$this->AddValidationField ($name.'1', $label, 'Req', 1);
	if ($minparts > 1)
		$this->AddValidationField ($name.'2', $label, 'Digit', 1);
	$this->AddValidationField ($name.'1', $label, 'Digit', 5);
	$this->AddValidationField ($name.'2', $label, 'Digit', 4);
}

function ReturnZipCode ($name)
{
	$z1 = $this->Framework()->PostedData($name.'1');
	$z2 = $this->Framework()->PostedData($name.'2');
	if (strlen($z1) == 5) {
		$zip = (strlen($z2) == 4) ? "$z1-$z2" : $z1;
	} else
		$zip = '';
	return ($zip);
}

/********************************************************************************
Other Specialized Input Fields
*********************************************************************************/

// Show an input field for enter a state code. This will require exactly 2 characters and will convert them to caps. It will not validate tha twhat is entered is an actual state code.
function ShowStateField ($name, $next, $value='', $minlen='')
{
	$this->ShowInputLabel ('State', $name, $minlen);
	$this->OpenFieldContainer();

	$this->ShowFixedLengthInput_ ($name, $next, 2, $value, ($minlen > 0) ? 2 : 0);
	$this->ShowOriginalValue_ ($name, $value);
	$this->CloseFieldSection();
}

// Show an input field for an email address. This will validate that the field is a properly formatted email address (but cannot validate that the address actualy is in use).
function ShowEmailField ($label, $name, $size, $value='', $required=false)
{
	$this->ShowTextField ($label, $name, 60, $size, $value, ($required) ? 1 : 0);
	$this->AddValidationField ($name, $label, 'Email');
}

// Show an input field for a url. This will validate a properly formatted url (but cannot validate that the url is actualy is in use). It will accept partial as well as complete urls.
function ShowURLField ($label, $name, $size, $value='', $minlen='')
{
	$this->ShowTextField ($label, $name, 100, $size, $value, $minlen);
	$this->AddValidationField ($name, $label, 'URL');
}

/********************************************************************************
Button Fields
*********************************************************************************/

// Show a submit button
function ShowSubmitButton ($caption='Save', $name='SaveBtn')
{
	echo "<input name=\"$name\" type=\"submit\" class=\"$this->buttonclass\" value=\"$caption\" alt=\"Submit Form\" />
";
}

// Show a button to redirect to a different url
function ShowRedirectButton ($url, $caption='Cancel', $name='CancelBtn')
{
	echo "<input name=\"$name\" type=\"button\" class=\"$this->buttonclass\" value=\"$caption\" alt=\"Cancel Form\" onclick='location.href=\"$url\"' />
";
}

// Show a button that executes javascript when clicked.
function ShowJavaScriptButton ($js, $caption, $name='')
{
	echo "<input name=\"$name\" type=\"button\" class=\"$this->buttonclass\" value=\"$caption\" alt=\"Cancel Form\" onclick=\"$js\" />
";
}

function ShowHideButton ($tagid, $caption='Cancel', $name='')
{
	$this->ShowJavaScriptButton ("HideTag('$tagid')", $caption, $name);
}

function ShowUnhideButton ($tagid, $caption, $name='')
{
	$this->ShowJavaScriptButton ("ShowTag('$tagid')", $caption, $name);
}

// Show a button to run form validation without submitting the form. This is a debugging tool.
function ValidateButton ($funcname='')
{
	$this->validationfunc = ($funcname == '') ? 'FormValidation' : $funcname;
	echo "<input type=\"button\" class=\"$this->textclass\" value=\"Validate\" onclick=\"$this->validationfunc(this.form);\" />";
}

}
?>