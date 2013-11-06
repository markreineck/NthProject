<h1>
<?php
echo $PageTitle;
?>
</h1>
<div id="TaskList" name="TaskList">
<?php
$this->LoadView('widgets/showtasktree');
?>
</div>
<?php
$this->Ajax()->AjaxBase();
?>
<SCRIPT LANGUAGE="JavaScript">
function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}


function PrjClick(img,div)
{
var x=0;
var s;

	if (document.getElementById) {
		x = document.getElementById(div);
		y = document.getElementById(img);
		if (x) 
			if (x.style.display == 'none') {
				x.style.display = 'block';
				y.src = 'image/closeicon.png';
				s = div + '=1';
				
			} else {
				x.style.display = 'none';
				y.src = 'image/openicon.png';
				s = div + '=-1';
				
			}
		document.cookie = s;
	}
}

var x=0;
var y=0;

<?php
$x = 0;
while ($x < $countproj) {
$div = $slst[$x];
echo "y = readCookie('$div');
if (y < 1) {
x = document.getElementById('$div');
	if (x) x.style.display = 'none';
x = document.getElementById('i$div');
	if (x) x.src = 'image/openicon.png';
} else {
x = document.getElementById('$div');
	if (x) x.style.display = 'block';
x = document.getElementById('i$div');
	if (x) x.src = 'image/closeicon.png';
}
";
	$x += 1;
}

$xinner = 1;
while ($xinner <= $innercnt) {
$divinner = $slstinner[$xinner];
echo "yinner = readCookie('$divinner');
if (yinner < 1) {
xin = document.getElementById('$divinner');
	if (xin) xin.style.display = 'none';
xin = document.getElementById('i$divinner');
	if (xin) xin.src = 'image/openicon.png';
} else {
xin = document.getElementById('$divinner');
	if (xin) xin.style.display = 'block';
xin = document.getElementById('i$divinner');
	if (xin) xin.src = 'image/closeicon.png';
}
";
	$xinner += 1;
}
?>


</script>

