<script type="text/javascript" src="http://admin.tests.com/js/jquery-1.3.1.js"></script>
<script type="text/javascript" src="http://admin.tests.com/js/jquery-ui-1.7.2.custom.min.js"></script>

<?php
/*
<script type="text/javascript" src="http://www.tests.com/dbadmin/javascript/jquery-1.3.1.js"></script>
<script type="text/javascript" src="http://www.tests.com/dbadmin/javascript/jquery-ui-1.7.2.custom.min.js"></script>

<script type="text/javascript" src="http://admin.tests.com/javascript/javascript/jquery-1.3.1.js"></script>
<script type="text/javascript" src="http://admin.tests.com/javascript/javascript/jquery-ui-1.7.2.custom.min.js"></script>
*/
$this->LoadCSSFile('datepicker');
$this->LoadCSSFile('jquery-ui-1.7.2.custom');

function RenderDatePicker($tagid,$changemonth,$changeYear,$format,$max,$min)
{
	$datepicker = "
	<script type='text/javascript'>
		$(document).ready(function(){
			$('#$tagid').datepicker({
				showOn: 'button',
				buttonImage: 'image/datepicker.png',
				buttonImageOnly: true,
				changeMonth: $changemonth,
				changeYear: $changeYear,
				dateFormat: '$format',
				maxDate: '$max',
				minDate: '$min'
			});
		});
	</script>
	";
	echo $datepicker;
}
?>

