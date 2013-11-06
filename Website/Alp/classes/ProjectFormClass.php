<?php

class ProjectFormClass extends FormClass 
{

var $format = 'mm-dd-yy';

function ProjectFormClass($framework)
{
	$this->FormClass($framework);
// Load jquery modules here
}

private function LoadDatePickerJsCss()
{
	static $dateloaded = false;

	if (!$dateloaded) {
		$this->Framework()->LoadCSSFile('datepicker');
		$this->Framework()->LoadCSSFile('jquery-ui-1.7.2.custom');
		echo '
<script type="text/javascript" src="http://projects.nth-generation.com/js/jquery-1.3.1.js"></script>
<script type="text/javascript" src="http://projects.nth-generation.com/js/jquery-ui-1.7.2.custom.min.js"></script>
';
		$dateloaded = true;
	}
}
	
private function RenderDatePicker($tagid,$changemonth,$changeYear,$format,$max,$min)
{
	$cy = ($changeYear) ? 'true' : 'false';
	$cm = ($changemonth) ? 'true' : 'false';

	$this->LoadDatePickerJsCss();
	$datepicker = "
<script type='text/javascript'>
	$(document).ready(function(){
		$('#$tagid').datepicker({
			showOn: 'button',
			buttonImage: 'http://projects.nth-generation.com/image/calendar.png',
			buttonImageOnly: true,
			changeMonth: '$cm',
			changeYear: '$cy',
			dateFormat: '$format'";
if ($max)
	$datepicker .= ",
			maxDate: '$max'";
if ($min)
	$datepicker .= ",
			minDate: '$min'";
$datepicker .= ",
		});
	});
</script>
";
	echo $datepicker;
}

// Show a date field 
function ShowDatePickerField ($label, $name, $value='', $required=false, $max='', $min='', $format='', $changemonth=true, $changeYear=true)
{
	if (!$format) $format = $this->format;
	$this->RenderDatePicker($name, $changemonth, $changeYear, $format, $max, $min);	
	$this->ShowDateField ($label, $name, $value, $required);
}

private function LoadHTMLJsCss()
{
	static $htmlloaded = false;
	
	if (!$htmlloaded) {
		echo '
<script src="ckeditor/ckeditor.js" type="text/javascript"></script>
';
		$htmlloaded = true;
	}
}

function ShowHTMLEditField ($label, $name, $rows, $cols, $value='', $minlen=0)
{
	$this->LoadHTMLJsCss();
	$this->ShowTextAreaField ($label, $name, $rows, $cols, $value, $minlen);
	echo '
<script type="text/javascript">
//<![CDATA[	
CKEDITOR.replace( "'.$name.'" );
//]]>
</script>
';
}

}
?>