<?php
function MakeIconLink($icon, $link, $hint, $onclick='', $color='')
{
	echo "<a href=\"$link\" onclick=\"$onclick\" class=\"ActionIconLinks $color\" title=\"$hint\"><span class=\"glyphicon glyphicon-$icon\"></span></a>";
}

// $size is currently not used. Why did we add this?
function MakeJSIcon($icon, $js, $hint, $size='')
{
	//echo "<img src=\"/image/$icon\" alt=\"$hint\" title=\"$hint\" class=\"icons\" border=\"0\" onClick=\"$js\">";
	echo "<a href=\"#hint\" onclick=\"$js\" alt=\"$hint\" class=\"ActionIconLinks\"><span class=\"glyphicon glyphicon-$icon\"></span></a>";
}
?>
